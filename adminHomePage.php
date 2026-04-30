<?php
  session_start();
  if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: index.html");
    exit();
  }
  require_once("Connection.php");
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Delete'])) {
      $isbn = $_POST['isbn'];
      $check = $conn->prepare("SELECT COUNT(*) FROM REPORTS WHERE BOOK_NO= ? AND Issue_return IN ('Issued','Reissued') ");      
      $check->bind_param("s", $isbn);
      $check->execute();
      $check= $check->get_result()->fetch_assoc();
      if ($check['COUNT(*)'] >=1) {
        echo "<script>
            alert('❌ Cannot delete: Book is currently issued to a student.');
            window.location.href = 'adminHomePage.php';
            </script>";
      }
      else {
        $stmt = $conn->prepare("DELETE FROM BOOKS WHERE ISBN = ?");
        $stmt->bind_param("s", $isbn);
        try {
          if ($stmt->execute()) {
            echo "<script>
                  alert('✅ Book deleted successfully.');
                  window.location.href = 'adminHomePage.php';
                  </script>";
          }
        } catch (mysqli_sql_Exception $e) {
              echo "<script>
                    alert('⚠️ Failed to delete book');
                    window.location.href = 'adminHomePage.php';
                    </script>";
        }        
      }
    }
  }
    function showBooks($result){ ?>
      <div class="books"><?php
      while ($book = $result->fetch_assoc()) {
        $author = $book['Author_1'];
        if ($book['Author_2'])
          $author = $author . ", " . $book['Author_2'];
        if ($book['Author_3'])
          $author = $author . ", " . $book['Author_3']; ?>
          <div class="book-card">
            <img src="<?php echo $book['Cover_image_path']; ?>" alt="Book Cover">
            <div class="icon-buttons">

              <!-- Edit Button with Confirmation -->
              <form method="POST" action="editbook.php" onsubmit="return confirmEdit();">
                <input type="hidden" name="isbn" value="<?php echo $book['ISBN']; ?>">
                <button class="tooltip" name="Edit" id="Edit" style="left: 75%;"><img src="uploads/required_pics/pencil.png" alt="Edit">
                  <span class="tooltiptext">Edit</span></button>
              </form>

              <!-- Delete Button with Confirmation -->
              <form method="POST" action="" onsubmit="return confirmDelete();">
                <input type="hidden" name="isbn" value="<?php echo $book['ISBN']; ?>">
                <button class="tooltip" id="Delete" name="Delete" style="left:86%;"><img src="uploads/required_pics/trash.png"
                    alt="Delete">
                  <span class="tooltiptext">Delete</span></button>
              </form>
            </div>
            <h3><?php echo $book['Title']; ?></h3><br>
            <div class="book_details"><?php
            if ($book['Quantity'] == 0)
              echo "<p style='color:#e93e3e;'><strong>Available : " . $book['Quantity'] . "</strong></p>";
            else
              echo "<p style='color:green;'><strong>Available : " . $book['Quantity'] . "</strong></p>"; ?>
              <p><strong>ISBN :</strong>
                <?php echo $book['ISBN']; ?>
              </p>
              <p><strong>Author(s) :</strong>
                <?php echo $author; ?>
              </p>
              <p><strong>Edition :</strong>
                <?php echo $book['Edition']; ?>
              </p>
              <p><strong>Publisher :</strong>
                <?php echo $book['Publisher']; ?>
              </p>
              <p><strong>Category :</strong>
                <?php echo $book['Category']; ?>
              </p>
              <p><strong>Price :</strong>
                <?php echo $book['Price']; ?>
              </p>
            </div>
          </div><?php
      } ?>
    </div>
    <?php
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link rel="stylesheet" href="adminHome.css">
  <link rel="stylesheet" href="searchboxStyle.css">
  <script>
    function confirmDelete() {
      return confirm("Are you sure you want to delete this book?");
    }
    function confirmEdit() {
      return confirm("Are you sure you want to edit this book details?");
    }
  </script>
</head>

<body>
  <header>
    <form class="search-box" action="" method="POST">
      <input type="text" name="searchInput" placeholder="Search">
      <button type="submit" name="Search"><img src="uploads/required_pics/search.png" alt=""></button>
    </form>
  </header>
  <aside>
    <?php include "side.php"; ?>
  </aside>
  <main>    
    <?php include "Container.php"; ?>
    <div class="all-books">
      <span><img src="uploads/required_pics/book-open-cover.png" alt="">
        <h1>Book Library</h1>
      </span><br><br>
      <!-- Show Books -->
      <?php include "searchbar.php"; ?>
      <?php
      $query = "SELECT category from books group by category;";
      $result1 = mysqli_query($conn, $query);
      for ($i = 0; $i < mysqli_num_rows($result1); $i++) {
        $arr = mysqli_fetch_assoc($result1);
        $cat = $arr['category'];
        $bookQuery = "SELECT * from BOOKS where category = '" . $cat . "' ORDER BY title;";
        $result = mysqli_query($conn, $bookQuery); ?>
        <div class="heading-container">
          <h1><?php echo $cat; ?></h1>
        </div><?php
        showBooks($result);
      } ?>
    </div>
  </main>
</body>

</html>
<?php
mysqli_close($conn);
?>