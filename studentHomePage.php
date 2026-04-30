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
// include 'db.php';
// $login_id = $_SESSION['login_id'] ?? '';
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
  <title>Student Home Page</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="studentPageStyle.css">
</head>
<body>
  <!-- <div class="container">
    <div class="dashboard"> -->
    <!-- Sidebar -->
     <header>
     </header>
     <aside>
        <?php include "sidebar.php" ?>
     </aside>
   
<main>
    <!-- Main Content -->

<!-- START: Book Dashboard section -->
<div id="dashboard-section" class="content-section" style="display: block;">
       <form method="GET" class="search-form">
  <input type="text" name="query" placeholder="Search books..." required>
  <button class="search" type="submit"><span class="material-icons">search</span></button>
</form>
<!--search book result-->
      <?php
$params = [];

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search = '%' . trim($_GET['query']) . '%';
    $book_query = "SELECT * FROM BOOKS WHERE Title LIKE ? OR Author_1 LIKE ? OR Author_2 LIKE ? OR Author_3 LIKE ? OR Category Like ?";
    
    $stmt = $conn->prepare($book_query);
    $stmt->bind_param("sssss", $search, $search, $search, $search,$search);
    $stmt->execute();
    $book_result = $stmt->get_result();

    echo "<div class='your-book'>🔍 Search Results</div><div class='book-container'>";
    
    if ($book_result->num_rows > 0) {
        while ($book = $book_result->fetch_assoc()) { ?>
            <div class="book-card">
                <img src="<?php echo $book['Cover_image_path']; ?>" alt="Book Cover">
                <h3><?php echo $book['Title']; ?></h3>
                <p><strong>Author(s):</strong> 
                    <?php echo $book['Author_1']; ?>
                    <?php if ($book['Author_2']) echo ", " . $book['Author_2']; ?>
                    <?php if ($book['Author_3']) echo ", " . $book['Author_3']; ?>
                </p>
                <p><strong>Edition:</strong> <?php echo $book['Edition']; ?></p>
                <p><strong>Publisher:</strong> <?php echo $book['Publisher']; ?></p>
                <p><strong>Category:</strong> <?php echo $book['Category']; ?></p>
                <p><strong>
                 <?php 
                if ($book['Quantity'] > 0) {
                  echo "<span style='color:green;'>Available: ".$book['Quantity']."</span>";
               }
                else {
                    echo "<span style='color:red;'>Available: ".$book['Quantity']."</span>";
               }
 ?></strong></p>
            </div>
        <?php }
    } else {
        echo "<p style='grid-column: 1 / -1; text-align: center;'>No results found for '<strong>" . htmlspecialchars($_GET['query']) . "</strong>'</p>";
    }

    echo "</div>";
}
?><!--search book-->
      <h2>📚 Book Dashboard</h2><br>
  <!--show the student's category books-->
  <!-- <h2>Books on Category :</h2><br><br> -->
  <div class="your-book"><?php echo $category?></div>
<div class="book-container">
<?php
$preferred_query = "SELECT * FROM BOOKS WHERE Category = ?";
$pref_stmt = $conn->prepare($preferred_query);
$pref_stmt->bind_param("s", $category);
$pref_stmt->execute();
$pref_result = $pref_stmt->get_result();

while ($book = $pref_result->fetch_assoc()) { ?>
  <div class="book-card">
    <img src="<?php echo $book['Cover_image_path']; ?>" alt="Book Cover">
    <h3><?php echo $book['Title']; ?></h3>
    <p><strong>Author(s):</strong> 
      <?php echo $book['Author_1']; ?>
      <?php if ($book['Author_2']) echo ", " . $book['Author_2']; ?>
      <?php if ($book['Author_3']) echo ", " . $book['Author_3']; ?>
    </p>
    <p><strong>Edition:</strong> <?php echo $book['Edition']; ?></p>
    <p><strong>Publisher:</strong> <?php echo $book['Publisher']; ?></p>
    <p><strong>Category:</strong> <?php echo $book['Category']; ?></p>
    <p><strong>
                 <?php 
                if ($book['Quantity'] > 0) {
                  echo "<span style='color:green;'>Available: ".$book['Quantity']."</span>";
               }
                else {
                    echo "<span style='color:red;'>Available: ".$book['Quantity']."</span>";
               }
 ?></strong></p>
  </div>
<?php } ?>
</div>
<br>
<?php $subject_query="SELECT DISTINCT category FROM BOOKS WHERE Category != ?";
$subject_stmt = $conn->prepare($subject_query);
$subject_stmt->bind_param("s", $category);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();      
while($subject=$subject_result->fetch_assoc()){
  $other_category=$subject['category'];
  ?>
  <div class="your-book"><?php echo $subject['category']; ?></div>
  <div class="book-container">
  <?php
  $other_query = "SELECT * FROM BOOKS WHERE Category= ?";
$other_stmt = $conn->prepare($other_query);
$other_stmt->bind_param("s", $other_category);
$other_stmt->execute();
$other_result = $other_stmt->get_result();

while ($book = $other_result->fetch_assoc()) { ?>
  <div class="book-card">
    <img src="<?php echo $book['Cover_image_path']; ?>" alt="Book Cover">
    <h3><?php echo $book['Title']; ?></h3>
    <p><strong>Author(s):</strong> 
      <?php echo $book['Author_1']; ?>
      <?php if ($book['Author_2']) echo ", " . $book['Author_2']; ?>
      <?php if ($book['Author_3']) echo ", " . $book['Author_3']; ?>
    </p>
    <p><strong>Edition:</strong> <?php echo $book['Edition']; ?></p>
    <p><strong>Publisher:</strong> <?php echo $book['Publisher']; ?></p>
    <p><strong>Category:</strong> <?php echo $book['Category']; ?></p>
    <p><strong>
                 <?php 
                if ($book['Quantity'] > 0) {
                  echo "<span style='color:green;'>Available: ".$book['Quantity']."</span>";
               }
                else {
                    echo "<span style='color:red;'>Available: ".$book['Quantity']."</span>";
               }
 ?></strong></p>
  </div>
<?php }?>
              </div>
<?php
}
?>
<!-- <div class="your-book">Other Books</div>
<div class="book-container"> -->
<!-- other category books -->
</div>
<!-- END: Book Dashboard section -->

<!--</div>end of book container-->
<!--end of main-->
            </main>
</body>
</html>
