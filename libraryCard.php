<?php
session_start(); 
include 'Connection.php';
//1. fetch student ID
 $login_id = $_SESSION['username'] ?? '';
if(!isset($_SESSION['username'])){
  session_destroy();
  header("Location: index.html");
  exit();
}
//  session_start(); 
// include 'db.php';
// //1. fetch student ID
//  $login_id = $_SESSION['login_id'] ?? '';
// if(!isset($_SESSION['login_id'])){
//   session_destroy();
//   header("Location: login.php");
//   exit();
// }
// 2. Fetch student category
$category = '';
$cat_query = "SELECT DEPARTMENT,PFP_PATH FROM READERS WHERE USER_ID = ?";
$cat_stmt = $conn->prepare($cat_query);
$cat_stmt->bind_param("s", $login_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

if ($cat_result->num_rows === 1) {
    $student = $cat_result->fetch_assoc();
    $category = $student['DEPARTMENT'];
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile Card</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="studentPageStyle.css">
  <link rel="stylesheet" href="libraryCard.css">
</head>
<body>
    <!-- Sidebar -->
     <aside>
        <?php include "sidebar.php" ?>
     </aside>
   
<main>
    <!-- Main Content -->

<!-- START: Book Dashboard section -->
<!-- <div id="dashboard-section" class="content-section" style="display: block;"> -->
<div class="main-card" style="flex-direction: column; gap:45px;">
      <div class="header">
      <h2> Your Profile Card</h2>
    </div>
      <?php
    $student_id = $login_id;

    $stmt = $conn->prepare("SELECT * FROM READERS WHERE USER_ID = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        ?>
        <div class="card-content">
          <img src="<?php echo $student['PFP_PATH']; ?>" alt="Student Photo" class="profile-pic-large">
          <div class="studentCard-details">
            <p><strong>Name:</strong> <?php echo $student['FNAME'] . " " . $student['LNAME']; ?></p>
            <p><strong>College Roll:</strong> <?php echo $student['CLG_ROLL']; ?></p>
            <p><strong>Semester:</strong> <?php echo $student['SEM']; ?></p>
            <p><strong>Department:</strong> <?php echo $student['DEPARTMENT']; ?></p>
            <p><strong>Phone:</strong> <?php echo $student['PH_NO']; ?></p>
            <p><strong>Address:</strong> <?php echo $student['ADDRESS']; ?></p>
          </div><!--end of studentCard-details-->
        </div><!--end of card-content-->
        <?php
    } else {
        echo "<p>Student information not found.</p>";
    }
    ?>
    </div><!--end of main-->
<!-- </div> -->
<!-- END: Book Dashboard section -->

<!--end of main-->
            </main>
</body>
</html>
