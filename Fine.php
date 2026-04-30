<?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once("Connection.php"); 

    //Fetch Values   
    $isbn = $_POST['isbn'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $fine=(float)$_POST['fine'] ?? '0';
    
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');

    //Fetch Report Number
    $query = "SELECT Reg_no FROM REPORTS WHERE USER_ID = '$user_id' AND BOOK_NO = '$isbn' AND Issue_return IN ('Issued','Reissued') ORDER BY Reg_no DESC LIMIT 1";
    $res=mysqli_query($conn,$query);
    $arr=mysqli_fetch_assoc($res);
    $reg_no=$arr['Reg_no'];

    //Fetch details
    $query = "SELECT R.Issue_date, RE.PFP_PATH, RE.CLG_ROLL,RE.SEM,
            RE.FNAME,RE.LNAME,B.TITLE,R.USER_ID AS 'U_ID' 
            FROM READERS RE,LOGIN_READERS L,REPORTS R,BOOKS B WHERE
            RE.USER_ID = L.LOGIN_ID AND L.LOGIN_ID = R.USER_ID
            AND B.ISBN = R.BOOK_NO AND R.Reg_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reg_no);
    if ($stmt->execute()) {
        $arr = $stmt->get_result()->fetch_assoc();  
        $name=$arr['FNAME']." ".$arr['LNAME'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Fine Payment Status</title>
  <style>
    body {
      background-color: #61b087a0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 0;
      text-align: center;
    }

    .card {
      background-color: rgb(253, 246, 246);
      border-radius: 30px;
      padding: 20px;
      padding-bottom: 35px;
      max-width: 350px;
      width: 80%;
      box-shadow: 0px 4px 12px rgb(112, 181, 112);
    }

    img.profile {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .details {
      text-align: left;
      margin: 25px 20px 10px 40px;
      line-height: 30px;
    }

    .details span {
      margin: 8px 0;
      font-size: 16px;
    }

    .fine {
      color: rgb(242, 25, 25);
      font-size: 26px;
      font-weight: bold;
      margin: 12px;
      animation: scale 1.5s ease-in-out infinite;
    }

    @keyframes scale {
      0% {
        transform: scale(1.2);
      }
      50% {
        transform: scale(1.1);
      }

      100% {
        transform: scale(1.2);
      }
    }

    .paid-button {
      padding: 10px 40px;
      font-size: 18px;
      background-color: white;
      border: 2px solid black;
      border-radius: 10px;
      font-weight: bold;
      box-shadow: 3px 3px 0px black;
      cursor: pointer;

    }

    .paid-button:hover {
      background-color: rgba(192, 190, 190, 0.651);
      transform: scale(1.2);
      transition: ease-in-out;
      transition-duration: 0.5s;
      font-size: 19px;
    }
  </style>
</head>

<body>
  <div class="card">
    <img src="<?php echo $arr['PFP_PATH'];?>" alt="Student Photo" class="profile">
    <div class="info">
      <div class="fine">Fine Amount : <?php echo $fine;?></div>
      <div class="details">
        <span><strong>Student Name :</strong> <?php echo $name;?></span><br>
        <span style="margin-right: 18px"><strong>Student ID :</strong> <?php echo $arr['U_ID'];?></span>
        <span><strong>Sem :</strong> <?php echo $arr['SEM'];?></span>
        <br><br>
        <span><strong>Book Title :</strong> <?php echo $arr['TITLE'];?></span><br>
        <span><strong>Issue Date :</strong> <?php echo $arr['Issue_date'];?></span><br>
        <span><strong>Today :</strong> <?php echo $date;?></span><br>
        <span><strong>Report Number :</strong> <?php echo $reg_no;?></span>
      </div>
      <br>
      <form action="Finalize_fine.php" method="post">
        <input type='hidden' name='action' value="<?php echo $action;?>">
        <input type='hidden' name='isbn' value="<?php echo $isbn;?>">
        <input type='hidden' name='user_id' value="<?php echo $user_id;?>">
        <input type='hidden' name='fine' value="<?php echo $fine;?>">
        <input type='hidden' name='regno' value="<?php echo $reg_no;?>">
        <button class="paid-button">PAID</button>
      </form>
    </div>
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
