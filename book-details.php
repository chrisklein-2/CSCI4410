<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>No book selected.</div>";
    exit();
}

$book_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-warning'>Book not found.</div>";
    exit();
}

$book = $result->fetch_assoc();
$stmt->close();
?>

<?php include 'backend/header-b.php'; ?>
<div class="container-fluid display-table">
  <div class="row display-table-row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-10 col-sm-11 display-table-cell v-align dashboard-main">
      <div class="user-dashboard">
        <h3 class="mt-4 mb-4">Book Details</h3>

        <div class="card mb-4">
          <div class="row no-gutters">
            <div class="col-md-4 text-center">
              <img src="<?= htmlspecialchars($book['image']) ?>" alt="Book Image" class="img-fluid p-3" style="max-height: 400px;">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($book['title']) ?></h4>
                <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                <p class="card-text"><strong>Description:</strong> <?= htmlspecialchars($book['description']) ?></p>
                <p class="card-text"><strong>Page Count:</strong> <?= htmlspecialchars($book['length']) ?></p>
                <p class="card-text"><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
                <p class="card-text"><strong>Copies Available:</strong> <?= htmlspecialchars($book['copies_available']) ?></p>
                <p class="card-text"><strong>Times Checked Out:</strong> <?= htmlspecialchars($book['checkedout_count']) ?></p>
              </div>
            </div>
          </div>
        </div>

        
        <a href="books.php" class="btn btn-secondary">Back to Book List</a>
      </div>
    </div>
  </div>
</div>
<?php include 'backend/footer-b.php'; ?>
