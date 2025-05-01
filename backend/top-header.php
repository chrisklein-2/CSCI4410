
<?php



// Include the database connection
include 'db_connect.php';
// Check if the user is logged in (assuming the username is stored in the session after login)
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Username from session

    // Prepare SQL to fetch user details
    $sql = "SELECT username, first_name, last_name, email, phone_number FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username); // Bind the username to the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the username exists in the database
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userName = $user['username'];
            $firstname = $user['first_name'];
            $lastname = $user['last_name'];
            $email = $user['email'];
            $phoneNumber = $user['phone_number'];
          
        } else {
            // Default values if no user is found
            $userName = "Unknown User";
            $firstname = "N/A";
            $lastname = "N/A";
            $email = "N/A";
            $phoneNumber = "N/A";
            $address = "N/A";
        }

        $stmt->close();
    } else {
        // Handle SQL statement preparation error
        die("Error preparing SQL statement: " . $conn->error);
    }
} else {
    // If no session found, set default values
    $userName = "Guest";
    $firstname = "N/A";
    $lastname = "N/A";
    $email = "N/A";
    $phoneNumber = "N/A";
    $address = "N/A";
}
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
                                <ul class="list-inline header-top pull-right">
                                    
                                    <li class="dropdown right">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <span><?php echo htmlspecialchars($userName); ?></span>
                                            <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                            <div class="navbar-content">
                                               
                                                
                                                <div class="divider">
                                                </div>
                                                <a href="" class="dropdown-item view  "><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></a>
                                                <a href="profile.php" class="dropdown-item view  ">View Profile</a>
                                                <a class="dropdown-item" href="logout.php"><span>Log Out</span> <i class="fa fa-sign-out"></i></a>
                                            </div>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </header>