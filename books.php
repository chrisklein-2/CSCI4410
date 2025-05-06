<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = 'Guest';
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($fetchedUsername);
if ($stmt->fetch()) {
    $username = $fetchedUsername;
}
$stmt->close();

// --- Handle Logic Before Any Output ---
$flag = "normDisplay";
$default_result = $conn->query("SELECT * FROM books ORDER BY title ASC, author ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        $search = htmlspecialchars($_POST['search']);
        $search_sql = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR genre LIKE '%$search%' ORDER BY title ASC, author ASC";
        $default_result = $conn->query($search_sql);
    }

    if (isset($_POST['checkout'])) {
        $user_id = $_SESSION['user_id'];
        $book_id = $_POST['book_id'];

        $check_sql = "SELECT * FROM checked_out WHERE user_id = $user_id AND book_id = $book_id";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $_SESSION['checkout_error'] = "You have already checked out this book.";
        } else {
            $insert_checkout = "INSERT INTO checked_out (user_id, book_id) VALUES ($user_id, $book_id)";
            $update_book = "UPDATE books SET copies_available = copies_available - 1, checkedout_count = checkedout_count + 1 WHERE book_id = $book_id";

            if ($conn->query($insert_checkout) && $conn->query($update_book)) {
                header("Location: books.php");
                exit();
            } else {
                $_SESSION['checkout_error'] = "Checkout failed. Please try again.";
            }
        }
    }

    if (isset($_POST['turnIn'])) {
        $user_id = $_SESSION['user_id'];
        $book_id = $_POST['book_id'];

        $conn->query("DELETE FROM checked_out WHERE user_id = $user_id AND book_id = $book_id LIMIT 1");
        $conn->query("UPDATE books SET copies_available = copies_available + 1 WHERE book_id = $book_id");

        $flag = "userCheckOutDisplay";
        $default_result = $conn->query("SELECT b.book_id, b.title, b.author, b.description, b.length, b.genre, b.image, b.copies_available, c.due_date FROM checked_out c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = $user_id");
    }

    if (isset($_POST['userCheckedOut'])) {
        $user_id = $_SESSION['user_id'];
        $flag = "userCheckOutDisplay";
        $default_result = $conn->query("SELECT b.book_id, b.title, b.author, b.description, b.length, b.genre, b.image, b.copies_available, c.due_date FROM checked_out c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = $user_id");
    }
}
?>

<?php include 'backend/header-b.php'; ?>
<div class="container-fluid display-table">
  <div class="row display-table-row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-10 col-sm-11 display-table-cell v-align dashboard-main">
      <div class="row">
        <?php include 'backend/top-header.php'; ?>
      </div>

      <div class="user-dashboard">
        <h3 class="mt-4 mb-4">Welcome back, <?= htmlspecialchars($username) ?> 👋</h3>

        <?php if (isset($_SESSION['checkout_error'])): ?>
          <div class="alert alert-warning"><?= $_SESSION['checkout_error'] ?></div>
          <?php unset($_SESSION['checkout_error']); ?>
        <?php endif; ?>

        <div class="row border-bottom pb-3 mb-3">
          <div class="col-6">
            <form method="POST" class="form-inline d-flex">
              <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search books...">
              <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
          </div>
          <div class="col-6 d-flex justify-content-end">
            <form method="POST">
              <input type="hidden" name="userCheckedOut">
              <button type="submit" name="userCheckedOut" class="btn btn-info">View My Checked Out Books</button>
            </form>
          </div>
        </div>

        <table class="table table-bordered mt-4" id="bookTable">
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>Description</th>
              <th>Page Count</th>
              <th>Genre</th>
              <th>Image</th>
              <th>Copies</th>
              <th>Status</th>
              <th>Action</th>
              <th>More</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($default_result->num_rows > 0): ?>
              <?php while ($row = $default_result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['title']) ?></td>
                  <td><?= htmlspecialchars($row['author']) ?></td>
                  <td><?= htmlspecialchars($row['description']) ?></td>
                  <td><?= htmlspecialchars($row['length']) ?></td>
                  <td><?= htmlspecialchars($row['genre']) ?></td>
                  <td><a href="#" data-toggle="modal" data-target="#modal<?= $row['book_id'] ?>">View Image</a></td>
                  <td><?= $row['copies_available'] ?></td>

                  <?php if ($flag == 'userCheckOutDisplay'): ?>
                    <td>Checked Out</td>
                    <td>
                      <form method="POST">
                        <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                        <button type="submit" name="turnIn" class="btn btn-warning btn-sm">Turn In</button>
                      </form>
                    </td>
                  <?php else: ?>
                    <?php if ($row['copies_available'] > 0): ?>
                      <td>Available</td>
                      <td>
                        <form method="POST">
                          <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                          <button type="submit" name="checkout" class="btn btn-success btn-sm">Checkout</button>
                        </form>
                      </td>
                    <?php else: ?>
                      <td>Unavailable</td>
                      <td><span class="text-muted">Not Available</span></td>
                    <?php endif; ?>
                  <?php endif; ?>

                  <td>
                    <a href="book-details.php?id=<?= $row['book_id'] ?>" class="btn btn-info btn-sm">View More</a>
                  </td>
                </tr>

                <!-- Image Modal -->
                <div class="modal fade" id="modal<?= $row['book_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel<?= $row['book_id'] ?>" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel<?= $row['book_id'] ?>">Book Cover</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body text-center">
                        <img src="<?= htmlspecialchars($row['image']) ?>" class="img-fluid">
                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="10" class="text-center">No books found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'backend/footer-b.php'; ?>
