<?php
session_start();
if (!isset($_SESSION['user'])) {
  session_destroy();
  header("Location: login_admin.php");
  exit();
}
require_once("Connection.php");
function showRequests($result)
{ ?>
  <div class="table-card">
    <table>
      <tr>
        <th>Request ID</th>
        <th>Book Name</th>
        <th>Author Name 1</th>
        <th>Author Name 2</th>
        <th>Author Name 3</th>
        <th>Category</th>
        <th>Edition</th>
        <th>ISBN</th>
        <th>User ID</th>
        <th>Delete</th>
      </tr>
      <?php
      if($result->num_rows==0) {
        echo "<tr style='height:62px;'><td colspan='10' style='color:#787878;'>- - - No requested books - - -</td></tr></table>";
        return;
      }
      for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        $arr = mysqli_fetch_assoc($result);
        $REQ_ID=$arr['REQ_ID'];
        $BOOK_NAME=$arr['BOOK_NAME'];
        $AUTHOR_NAME_1=$arr['AUTHOR_NAME_1'];
        $AUTHOR_NAME_2=$arr['AUTHOR_NAME_2'];
        $AUTHOR_NAME_3=$arr['AUTHOR_NAME_3'];
        $ISBN=$arr['ISBN'];
        $USER_ID=$arr['USER_ID'];
        $CATEGORY=$arr['CATEGORY'];
        $EDITION=$arr['EDITION'];

        if ($arr['AUTHOR_NAME_2'] == NULL) {
          $AUTHOR_NAME_2=" - ";
        }
        if ($arr['AUTHOR_NAME_3'] == NULL) {
          $AUTHOR_NAME_3=" - ";
        }
        if ($arr['ISBN'] == NULL) {
          $ISBN=" - ";
        }
        if ($arr['EDITION'] == NULL) {
          $EDITION=" - ";
        }
        ?>
        <tr>
          <td><?php echo $REQ_ID ?></td>
          <td><?php echo $BOOK_NAME ?></td>
          <td><?php echo $AUTHOR_NAME_1 ?></td>
          <td><?php echo $AUTHOR_NAME_2 ?></td>
          <td><?php echo $AUTHOR_NAME_3 ?></td>
          <td><?php echo $CATEGORY ?></td>
          <td><?php echo $EDITION ?></td>
          <td><?php echo $ISBN ?></td>
          <td><?php echo $USER_ID ?></td>
          <td>
            <form action="" method="post">                
              <input type="hidden" name="req_id" value="<?php echo $REQ_ID; ?>">
              <button name="Delete_btn" id="delete" style="background: none; border:none;">
                <img src="uploads/required_pics/trash.png" alt="Delete" height="30px">
              </button>
            </form>
          </td>
        </tr> <?php
      } ?>
    </table>
  </div>
  <?php
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Requested Books</title>
  <link rel="stylesheet" href="searchboxStyle.css">
  <style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: black;
    }
    body {
        background: linear-gradient(to right, var(--light), var(--middle), rgb(79, 166, 109));
        display: flex;
        flex-direction: column;
    }

    main {
        margin: 60px 20px 20px 110px;
    }
    .table-card {
        background-color: rgba(255, 255, 255, 0.23);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: auto;
        margin: 60px 10px 20px 30px;
    }

    table {
        width: 100%;
        margin-right: 10px;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 20px;
        overflow: hidden;
    }

    th {
        background-color: rgb(132 156 174 / 50%);
        padding: 12px 16px;
        font-weight: bold;
        font-size: 18px;
        text-align: center;
        height: 3em;
    }

    td {
        background-color: rgba(196, 195, 169, 0.47);
        padding: 12px 16px;
        font-size: 17px;
        border-bottom: 1px solid #e1e8f0;
        text-align: center;
        height:4em;
    }

    tr:not(:first-child):hover {
        background-color: rgba(249, 249, 249, 0.46);
    }
    tr:last-child td{
        border-bottom: none;
    } 
    .head {
      display :flex;
      justify-content: center;
      align-items: center;
      font-family: sans-serif;
      font-size: 36px;
      margin-bottom: 35px;
    }
    #delete:hover{
      transform: scale(1.2);
    }
    #delete {
      transition: 0.3s ease-in-out;
    }
  </style>
</head>

<body>
  <aside>
    <?php include "side.php"; ?>
  </aside>
  <main>
    <h2 class="head">Requested Books</h2>
    <?php
      $query="SELECT * FROM REQUEST_BOOKS";
      $result=mysqli_query($conn,$query);
      showRequests($result);
    ?>
  </main>
</body>

</html>
<?php 
  if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['Delete_btn'])) {
    $req_id = $_POST['req_id'];
    $stmt = $conn->prepare("DELETE FROM REQUEST_BOOKS WHERE REQ_ID = ?");
    $stmt->bind_param("s", $req_id);
    try {
      if ($stmt->execute()) {
        echo "<script>
              alert('✅ Deleted successfully.');
              window.location.href = 'viewRequest.php';
              </script>";
      }
    } catch (mysqli_sql_Exception $e) {
        echo "<script>
              alert('⚠️ Failed to delete');
              window.location.href = 'viewRequest.php';
              </script>";
    }        
  }
?>

<?php mysqli_close($conn); ?>