<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php'; // Ensure $conn is set

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
?>

<header class="dashboard-header">
                        <div class="col-md-7">
                            <nav class="navbar-default pull-left">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle collapsed" data-toggle="offcanvas" data-target="#side-menu" aria-expanded="false">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                    <div id="custom-search-input">
                                <div class="input-group col-md-12">
                                    <input type="text" class="form-control input-lg" placeholder="Type to search..." />
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-lg" type="button">
                                        <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                                </div>
                            </nav>
                            
                        </div>
                        <div class="col-md-5">
                             <div class="header-rightside">
                                 <a class="btn btn-default text-decoration-none" href="logout.php">Log Out <i class="fa fa-sign-out"></i></a>
                            </div>
                                 <!-- <div class="header-rightside">
                                <ul class="list-inline header-top pull-right">
                                    <!-- Example single danger button -->
                                <!-- <div class="btn-group">
                                <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= htmlspecialchars($username) ?>
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="#"><?= htmlspecialchars($username) ?></a></li>
                                    <li><a class="dropdown-item" href="profile.php">View Profile</a></li>
                                    <li><a class="dropdown-item" href="logout.php">Log Out <i class="fa fa-sign-out"></i></a></li>
                                </ul>
                                </div>
                                </div>
                                    
                                    <li class="dropdown right">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span><?= htmlspecialchars($username) ?></span>
                                            <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                            <div class="navbar-content">
                                               
                                                
                                                <div class="divider">
                                                </div>
                                                <a href="" class="dropdown-item view  "><?= htmlspecialchars($username) ?></a>
                                                <a href="profile.php" class="dropdown-item view  ">View Profile</a>
                                                <a class="dropdown-item" href="logout.php"><span>Log Out</span> <i class="fa fa-sign-out"></i></a>
                                            </div>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>  -->
                        </div>
                    </header>