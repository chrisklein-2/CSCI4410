<?php
session_start();
require 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = 'Guest'; // Default fallback

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($fetchedUsername);
    if ($stmt->fetch()) {
        $username = $fetchedUsername; // Found in DB
    }
    $stmt->close();
}

$username = htmlspecialchars($_SESSION['username']); 



// Fetch metrics
$totalBooks = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0];
$totalGenres = $conn->query("SELECT COUNT(DISTINCT genre) FROM books")->fetch_row()[0];
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$totalCheckedOut = $conn->query("SELECT COUNT(*) FROM checked_out")->fetch_row()[0];
?>



     
<?php include 'backend/header-b.php'; ?>
   


    <div class="container-fluid display-table">
        <div class="row display-table-row">
           
           <?php  include 'sidebar.php'?>
           
            <div class="col-md-10 col-sm-11 display-table-cell v-align dashboard-main">
                <!--<button type="button" class="slide-toggle">Slide Toggle</button> -->
                <div class="row">
                <?php  include 'backend/top-header.php'?>
                </div>
                <div class="user-dashboard">
                <h3 class="mt-5 mb-5">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?> 👋</h3>

                    <div class="row column1">
                        <div class="col-md-6 col-lg-3">
                            <div class="full counter_section margin_bottom_30 yellow_bg">
                               <a href="dashboard-patient.php" class="overlay"></a>
                             <div class="counter-info">
                             <div class="counter_icon">
                                 <div> 
                                    <i class="fa fa-user"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?= $totalBooks ?></p>
                                    
                                 </div>
                              </div>
                            </div>
                            <p class="head_counter">Total Books</p>
                            </div>
                        
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30 blue1_bg">
                              <div class="counter-info">
                              <div class="counter_icon">
                                 <div> 
                                 <i class="fa fa-calendar"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no">1</p>
                                   
                                 </div>
                              </div>
                              </div>
                              <p class="head_counter">Total Genre</p>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30 green_bg">
                              <div class="counter-info">
                              <div class="counter_icon">
                                 <div> 
                                 <i class="fa fa-user"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?= $totalUsers ?></p>
                                </div>
                                </div>
                              </div>
                            <p class="head_counter">Total Users </p>
                           </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                           <div class="full counter_section margin_bottom_30 red_bg">
                              <div class="counter-info">
                              <div class="counter_icon">
                                 <div> 
                                 <i class="fa fa-user"></i>
                                 </div>
                              </div>
                              <div class="counter_no">
                                 <div>
                                    <p class="total_no"><?= $totalCheckedOut ?></p>
                                </div>
                            </div>
                        </div>
                        <p class="head_counter">Books Checked Out</p>
                           </div>
                        </div>
                     </div>
                    <div class="row">
                    <div class="chart-container">   
                       <div class="card px-5 py-2">
                       <canvas id="ageChart" width="500" height="500"></canvas>
                       </div>
                       <div class="card px-5 py-2">
                       <canvas id="genderChart" width="500" height="500" ></canvas>
                       </div>
                    </div>

                    </div>
                
                    </div>
             
        <?php include 'backend/footer-b.php'; ?>

