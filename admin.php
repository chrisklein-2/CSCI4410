<?php
session_start();

$admin_password = '$2y$10$2NwvJU/R90S0TkykH4ECjOhPwR9YZj0qTLOYsI9tA0lD55v5fd02u'; // adminpassword

// Save the referring page
if (!isset($_SESSION['redirect_from']) && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['redirect_from'] = $_SERVER['HTTP_REFERER'];
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
        if (password_verify($_POST['admin_password'], $admin_password)) {
            $_SESSION['is_admin'] = true;
            $redirectUrl = $_SESSION['redirect_from'] ?? 'admin.php';
            unset($_SESSION['redirect_from']);
            header("Location: $redirectUrl");
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
            }
            .signin {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .signin-content {
                background: white;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                max-width: 800px;
            }
            .signin-form {
                width: 100%;
            }
            .img-holder {
                margin-right: 30px;
            }
            .img-holder img {
                max-width: 300px;
            }
            .logo-text {
                font-size: 1.5rem;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
    <section class="signin">
        <div class="signin-content">
            <div class="img-holder">
                <figure><img src="./Assets/Images/admin.jpg" class="img signin-img" alt="sign in image"></figure>
            </div>

            <form method="POST" class="signin-form">
                <a class="navbar-brand mb-3 d-block" href="index.php">
                    <img src="./Assets/Images/book.gif" width="50"> 
                    <span class="logo-text text-primary">Library</span>
                </a>
                <h3 class="mb-3">Admin Sign In</h3>
                <?php if (isset($error)) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>

                <div class="form-group mb-4">
                    <label class="form-label" for="admin_password">Admin Password</label>
                    <input type="password" id="admin_password" name="admin_password" class="form-control" required />
                </div>

                <button type="submit" class="btn btn-primary btn-block mb-4">Login</button>
                <a href="index.php" class="link">Return to Main Page</a>
            </form>
        </div>
    </section>
    </body>
    </html>
    <?php
    exit();
}

require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$flag = "userTable";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>

</head>
<body>
    <header>
        <nav>
            <ul class="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="admin.php">Librarian Managment Page</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <h1>Librarian Managment Page</h1>

    <form method="POST">
        <input type="hidden">
        <button type="submit" name="viewUsers">View All Users</button>
        <button type="submit" name="modBookData">Modify Book Database</button>
        <button type="submit" name="Analytics">Analytics</button>
    </form>
    
    <br>

    <?php
        $default_result= "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['viewUsers'])) {
                
                $viewUsers_sql = "SELECT users.user_id, users.username, users.email,
                                books.title AS book_title, books.author AS book_author
                                FROM users LEFT JOIN checked_out ON users.user_id = checked_out.user_id
                                LEFT JOIN books ON checked_out.book_id = books.book_id
                                ORDER BY users.user_id";

                $default_result = $conn->query($viewUsers_sql);
            }

            if (isset($_POST['deleteUser'])) {
                $user_to_delete = $_POST['user_id'];
                $current_user = $_SESSION['user_id'];
            
                if ($user_to_delete == $current_user) {
                    echo "<script>alert('You cannot delete your own account.');</script>";
                } else {

                    //delete user checkouts
                    $delete_checkouts_sql = "DELETE FROM checked_out WHERE user_id = $user_to_delete";
                    $conn->query($delete_checkouts_sql);

                    //delete user
                    $delete_user_sql = "DELETE FROM users WHERE user_id = $user_to_delete";
                    if ($conn->query($delete_user_sql)) {
                        echo "<script>alert('User deleted successfully.');</script>";
                    } else {
                        echo "<script>alert('Failed to delete user.');</script>";
                    }
                }
                
                $viewUsers_sql = "SELECT users.user_id, users.username, users.email,
                                books.title AS book_title, books.author AS book_author
                                FROM users LEFT JOIN checked_out ON users.user_id = checked_out.user_id
                                LEFT JOIN books ON checked_out.book_id = books.book_id
                                ORDER BY users.user_id"; //refetch updated list
                $default_result = $conn->query($viewUsers_sql);
            }

            if (isset($_POST['modBookData'])) {
                $flag = "modBookData";
                $all_books = "SELECT * FROM books ORDER BY title ASC, author ASC";
                $default_result = $conn->query($all_books);
            }

            if (isset($_POST['addBook'])) {
                $title = $_POST['title'];
                $author = $_POST['author'];
                $description = $_POST['description'];
                $length = $_POST['length'];
                $genre = $_POST['genre'];
                $image = $_POST['image'];
                $copies = $_POST['copies_available'];
            
                $add_book_sql = "INSERT INTO books (title, author, description, length, genre, image, copies_available)
                               VALUES ('$title', '$author', '$description', $length, '$genre', '$image', $copies)";

                $dupe_title_check = "SELECT * FROM books WHERE title = '$title' AND author = '$author'";
                $dupe_title_check_result = mysqli_query($conn, $dupe_title_check);

                if ($dupe_title_check_result->num_rows > 0) {
                    echo "<script>alert('Book already available.');</script>";
                } elseif ($conn->query($add_book_sql)) {
                        echo "<script>alert('Book added successfully.');</script>";
                    } else {
                        echo "<script>alert('Failed to add book. Make sure all fields are completed.');</script>";
                }

                $flag = "modBookData";
                $all_books = "SELECT * FROM books ORDER BY title ASC, author ASC";
                $default_result = $conn->query($all_books);
            }

            if (isset($_POST['deleteBook'])) {
                $book_to_delete = $_POST['book_id'];

                $delete_checkouts_sql = "DELETE FROM checked_out WHERE book_id = $book_to_delete";
                $conn->query($delete_checkouts_sql);

                $delete_book_sql = "DELETE FROM books WHERE book_id = $book_to_delete";
                if ($conn->query($delete_book_sql)) {
                    echo "<script>alert('Book deleted successfully.');</script>";
                } else {
                    echo "<script>alert('Failed to delete book.');</script>";
                }
                $flag = "modBookData";
                $all_books = "SELECT * FROM books ORDER BY title ASC, author ASC";
                $default_result = $conn->query($all_books);
            }

            if (isset($_POST['updateCopies'])) {
                $book_id = intval($_POST['book_id']);
                $copies_available = intval($_POST['copies_available']);
            
                $query = "UPDATE books SET copies_available = $copies_available WHERE book_id = $book_id";
                mysqli_query($conn, $query);

                $flag = "modBookData";
                $all_books = "SELECT * FROM books ORDER BY title ASC, author ASC";
                $default_result = $conn->query($all_books);
            }
            if (isset($_POST['Analytics'])) {
                $flag = "analytics";
                
                //analytics data for passing 
                $analytics_data = [];
            
                $users_query = "SELECT COUNT(*) AS total_users FROM users";
                $books_query = "SELECT SUM(copies_available) AS total_books FROM books";
                $checked_out_query = "SELECT COUNT(*) AS total_checked_out FROM checked_out";
            
                $top_books_query = "
                    SELECT books.title, COUNT(checked_out.book_id) AS checkout_count
                    FROM checked_out
                    JOIN books ON checked_out.book_id = books.book_id
                    GROUP BY checked_out.book_id
                    ORDER BY checkout_count DESC
                    LIMIT 5
                ";
            
                $users_result = $conn->query($users_query);
                $books_result = $conn->query($books_query);
                $checked_out_result = $conn->query($checked_out_query);
                $top_books_result = $conn->query($top_books_query);
            
                if ($users_result && $books_result && $checked_out_result && $top_books_result) {
                    $analytics_data['total_users'] = $users_result->fetch_assoc()['total_users'];
                    $analytics_data['total_books'] = $books_result->fetch_assoc()['total_books'];
                    $analytics_data['total_checked_out'] = $checked_out_result->fetch_assoc()['total_checked_out'];
            
                    $analytics_data['top_books'] = [];
                    while ($row = $top_books_result->fetch_assoc()) {
                        $analytics_data['top_books'][] = $row;
                    }
                }
            
                $default_result = $analytics_data;
            }            
        }

        displayTable($default_result, $flag);

        function displayTable($result, $flag) {
            if ($result){
                if ($flag == "userTable"){
                    //group users and their books
                    $users = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userId = $row['user_id'];

                        if (!isset($users[$userId])) {
                            $users[$userId] = [
                                'user_id' => $userId,
                                'username' => $row['username'],
                                'email' => $row['email'],
                                'books' => [],
                            ];
                        }

                        if (!empty($row['book_title']) && !empty($row['book_author'])) {
                            $users[$userId]['books'][] = "{$row['book_title']} by {$row['book_author']}";
                        }
                    }

                    //display table
                    echo "<table border='1'>";
                    echo "<tr><th>User ID</th><th>Username</th><th>Email</th><th>Books Checked Out</th><th>Action</th></tr>";

                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>{$user['user_id']}</td>";
                        echo "<td>{$user['username']}</td>";
                        echo "<td>{$user['email']}</td>";

                        echo "<td><ul>";
                        if (!empty($user['books'])) {
                            foreach ($user['books'] as $book) {
                                echo "<li>$book</li>";
                            }
                        } else {
                            echo "<li>No books checked out</li>";
                        }
                        echo "</ul></td>";

                        echo "<td>
                            <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this user?');\">
                                <input type='hidden' name='user_id' value='{$user['user_id']}'>
                                <button type='submit' name='deleteUser'>Delete User</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } elseif ($flag == "modBookData"){
                        echo "<h3>Add Book</h3>";
                        echo "<form method='POST'>";
                            echo "<tr>";
                            echo "<td><input type='text' name='title' placeholder='Title'></td>";
                            echo "<td><input type='text' name='author' placeholder='Author'></td>";
                            echo "<td><input type='text' name='description' placeholder='Description'></td>";
                            echo "<td><input type='number' name='length' placeholder='Pages'></td>";
                            echo "<td><input type='text' name='genre' placeholder='Genre'></td>";
                            echo "<td><input type='text' name='image' placeholder='Image URL'></td>";
                            echo "<td><input type='number' name='copies_available' placeholder='Copies'></td>";
                            echo "<td><button type='submit' name='addBook'>Add Book</button></td>";
                            echo "</tr>";
                        echo "</form>";
                    
                        echo "<br>";

                    echo "<table border='1'>";
                    echo "<tr>";
                    echo "<th>" . "Title" . "</th>";
                    echo "<th>" . "Author" . "</th>";
                    echo "<th>" . "Description" . "</th>";
                    echo "<th>" . "Page Count" . "</th>";
                    echo "<th>" . "Genre" . "</th>";
                    echo "<th>" . "Image" . "</th>";
                    echo "<th>" . "Copies Available" . "</th>";
                    echo "<th>" . "Action" . "</th>";
                    echo "<tr>";
                    
                    while ($row = mysqli_fetch_assoc($result)) { 
                        echo "<tr>";
                        echo "<td>{$row['title']}</td>";
                        echo "<td>{$row['author']}</td>";
                        echo "<td>{$row['description']}</td>";
                        echo "<td>{$row['length']}</td>";
                        echo "<td>{$row['genre']}</td>";
                        echo "<td>{$row['image']}</td>"; // echo "<td><img src='{$row['image']}'></td>"; -> replace with to display images
                        echo "<td><form method='POST'>
                        <input type='hidden' name='book_id' value='{$row['book_id']}'>
                            <input type='number' name='copies_available' value='{$row['copies_available']}'>
                            <button type='submit' name='updateCopies'>Save Change</button>
                        </form></td>";
                        echo "<td><form method='POST'>
                                    <input type='hidden' name='book_id' value='{$row['book_id']}'>
                                    <button type='submit' name='deleteBook'>Delete</button>
                        </form></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                elseif ($flag == "analytics") {
                    $total_books = $result['total_books'];
                    $total_checked_out = $result['total_checked_out'];
                    $checked_out_percent = $total_books > 0 ? round(($total_checked_out / $total_books) * 100, 2) : 0;
                
                    echo "<h2>Library Analytics</h2>";
                
                    echo "<table border='1'>";
                    echo "<tr><th>Statistic</th><th>Value</th></tr>";
                    echo "<tr><td>Total Users</td><td>{$result['total_users']}</td></tr>";
                    echo "<tr><td>Total Books</td><td>{$total_books}</td></tr>";
                    echo "<tr><td>Total Books Checked Out</td><td>{$total_checked_out} ({$checked_out_percent}%)</td></tr>";
                    echo "</table>";
                    echo "<div style='display: flex; justify-content: center; align-items: center; gap: 40px;'>";
                    echo "<div>";
                        echo "<h3>Top 5 Most Checked Out Books</h3>";
                        echo "<canvas id='topBooksChart' width='400' height='400' style='width: 400px; height: 400px;'></canvas>";
                    echo "</div>";
                    echo "<div>";
                        echo "<h3>Checked Out vs Available</h3>";
                        echo "<canvas id='checkedOutPercentChart' width='400' height='400' style='width: 400px; height: 400px;'></canvas>";
                    echo "</div>";
                    echo "</div>";
                
                    // Pass PHP arrays to JavaScript
                    echo "<script>
                    const topBooksLabels = " . json_encode(array_column($result['top_books'], 'title')) . ";
                    const topBooksData = " . json_encode(array_column($result['top_books'], 'checkout_count')) . ";
                
                    const checkedOutData = [$total_checked_out, " . ($total_books - $total_checked_out) . "];
                    </script>";
                }                
            }
        } 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    //Script for the piegraphs
    const ctx1 = document.getElementById('topBooksChart').getContext('2d');
    const topBooksChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: topBooksLabels,
            datasets: [{
                label: 'Checkouts',
                data: topBooksData,
                backgroundColor: [
                    '#3498db', '#e74c3c', '#9b59b6', '#f1c40f', '#1abc9c'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Top 5 Most Checked Out Books'
                }
            }
        }
    });

    const ctx2 = document.getElementById('checkedOutPercentChart').getContext('2d');
    const checkedOutPercentChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Checked Out', 'Available'],
            datasets: [{
                data: checkedOutData,
                backgroundColor: ['#2ecc71', '#e67e22'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Percentage of Books Checked Out'
                }
            }
        }
    });
</script>

</body>
</html>
