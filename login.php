<?php
session_start();
include 'db_connect.php'; // your DB connection file

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Login</title>
</head>
<body>  

  <div class="auth-page">
    
    <aside class="banner">
      <h2>Nice to see you again</h2>
      <h1>WELCOME BACK</h1>
      <p>Borrow, review and discover your favourite books in one place.</p>
    </aside>

   
    <div class="container">
      <h1>Login</h1>

      <?php if (!empty($error)) : ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="login.php" method="post">
        <label>
          Username:
          <input type="text" id="username" name="username" class="form-control" required>
        </label>
        <label>
          Password:
          <input type="password" id="password" name="password" class="form-control" required>
        </label>
        <button type="submit" class="button">Login</button>
      </form>

      <div class="auth-links">
        <a href="register.php">Don’t have an account? Register</a>
      </div>
    </div>
  </div>
  <!-- /.auth-page -->

</body>
</html>
