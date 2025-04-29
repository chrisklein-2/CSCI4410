<?php
session_start();
include 'db_connect.php'; //include your database connection

//if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); //redirect to dashboard if already logged in
    exit();
}

$servername = "localhost"; //create initial connection
$username = "root";
$password = "";
$database = "library_database"; 
$conn = new mysqli($servername, $username, $password, $database);

//form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //get the form values and sanitize them
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $username = mysqli_real_escape_string($conn, $username);
    $dupe_username_check = "SELECT * FROM users WHERE username = '$username'";
    $dupe_username_check_result = mysqli_query($conn, $dupe_username_check);

    //validate input
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) { //password length check
        $error = "Password must be at least 8 characters long.";
    } elseif ($dupe_username_check_result->num_rows > 0) {
        $error = "Username already taken. Please choose another.";
    }

    //if no validation errors
    if (!isset($error)) {
        //hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        //prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            //redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Register</title>
</head>
<body onload="generate()">
    <header>
        <nav>
            <ul class="navbar">
                <li><a href="home.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Register</h1>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="register.php" method="post" onsubmit="return printmsg();">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <div>
                <input type="text" id="submit" placeholder="Enter code" required>
                <div id="captchaImage"></div>
                <button type="button" onclick="generate()">Refresh</button>
            </div>
            <p id="result"></p>

            <button type="submit" class="button">Register</button>
        </form>
    </div>
</body>
</html>
