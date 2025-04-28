<?php
session_start();

$admin_password = '$2y$10$2NwvJU/R90S0TkykH4ECjOhPwR9YZj0qTLOYsI9tA0lD55v5fd02u'; // adminpassword

// If the user hasn't logged in yet, show the login form
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
        if (password_verify($_POST['admin_password'], $admin_password)) {
            $_SESSION['is_admin'] = true;
            header("Location: admin.php"); // reload after login
            exit();
        } else {
            echo "Incorrect password. Please try again.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
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
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="password" name="admin_password" placeholder="Enter admin password" required>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>

    <?php
    exit(); //stop page so the rest of admin.php doesn't load yet
}

require 'db_connect.php';

//check if user is logged in
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
            }
        } 
    ?>
</body>
</html>
