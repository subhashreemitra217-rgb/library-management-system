<?php
//   session_start();
//   if (!isset($_SESSION['user'])) {
//     session_destroy();
//     header("Location: index.html");
//     exit();
//   }  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once("Connection.php");
    
    $date = $_POST['date'];
    $reg_no=(int)$_POST['reg_no'];
    $action=$_POST['action'];
    $msg = ($action === 'issue') ? "Issued" : 
       (($action === 'Reissued') ? "Re-issued" : 
       (($action === 'Returned') ? "Returned" : "Lost"));

    $msg_date = ($action === 'issue') ? "" : 
       (($action === 'Reissued') ? "Re-issue" : 
       (($action === 'Returned') ? "Return" : "Fine pay"));
    
           
    $query = "SELECT R.Issue_date, RE.PFP_PATH, RE.CLG_ROLL,RE.SEM,
            RE.FNAME,RE.LNAME,B.TITLE,R.USER_ID AS 'U_ID' 
            FROM READERS RE,LOGIN_READERS L,REPORTS R,BOOKS B WHERE
            RE.USER_ID = L.LOGIN_ID AND L.LOGIN_ID = R.USER_ID
            AND B.ISBN = R.BOOK_NO AND R.Reg_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reg_no);
    if ($stmt->execute()) {
        $result = $stmt->get_result()->fetch_assoc();  
        $name=$result['FNAME']." ".$result['LNAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successful Transaction</title>
    <link rel="stylesheet" href="Successful_msg_css.css">
</head>
<body>
    <div class="card">
        <img src="<?php echo $result['PFP_PATH'];?>" alt="Student Photo" class="profile">
        <div class="return">  
            <?php if ($msg != 'Lost') {?>          
            <div>Book <?php echo $msg;?></div><?php 
            } else { ?>
            <div>Fine Paid</div><?php } ?>
            
            <div class="tick">
                <span class="tickicon">
                    ✓
                </span>
            </div>
        </div>
        <?php if ($msg != 'Lost') {?>
        <div class="success">The book was <?php echo $msg;?> successfully!!</div><?php 
        } else { ?>
        <div class="success" style="color:red;">The book was Lost!!</div><?php } ?>
        

        <div class="details">
            <p><strong>Student Name :</strong> <?php echo $name;?></p>
            <span class="sem"><strong>Student ID :</strong> <?php echo $result['U_ID'];?></span>
            <span><strong>Sem :</strong> <?php echo $result['SEM'];?></span>
            <p><strong>College Roll :</strong> <?php echo $result['CLG_ROLL'];?></p>
            <br>            
            <p><strong>Book Title :</strong> <?php echo $result['TITLE'];?></p>
            <p><strong>Issue Date :</strong> <?php echo $result['Issue_date'];?></p>
            <?php if ($action != 'issue') {?>
            <p><strong><?php echo $msg_date;?> Date :</strong> <?php echo $date;?></p><?php } ?>
            <p><strong>Report Number :</strong> <?php echo $reg_no;?></p>
        </div><br>
        <a href="QRScan.html" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
        $stmt->close();
    }else {
    mysqli_close($conn);
    echo "<script>
        alert('⚠️Some Error Occured...Try again Later');
        window.location.href = 'QRScan.html';
        </script>";
    }  
    mysqli_close($conn);  
  }  
?>