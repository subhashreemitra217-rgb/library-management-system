<?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once("Connection.php"); 

    //Fetch Values   
    $isbn = $_POST['isbn'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $reg_no=$_POST['regno'] ?? '';
    $fine=$_POST['fine'] ?? '0';

    echo $fine."<br>";
    echo $user_id."<br>";
    echo $isbn."<br>";
    echo $action."<br>";
    echo $reg_no."<br>";

    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');
    $conn->begin_transaction();
      try {
        
        if ($action == 'Reissued') {
            $expected_return = date('Y-m-d', strtotime('+14 days'));
            $stmt = $conn->prepare("UPDATE REPORTS SET Issue_return = ?, Issue_date = ? ,Expected_return_date = ? , Fine_paid = 'yes',  Fine_amount = ? WHERE Reg_no = ?");
            $stmt->bind_param("sssss",  $action,$date,$expected_return,$fine,$reg_no);
            $stmt->execute();
        } else  {                
            $stmt = $conn->prepare("UPDATE REPORTS SET Issue_return = ?, Return_date = ? ,Fine_paid = 'yes',  Fine_amount = ? WHERE Reg_no = ?");
            $stmt->bind_param("ssss",  $action,$date,$fine,$reg_no);
            $stmt->execute();
            if ($action != 'Lost') {
                $update = $conn->prepare("UPDATE BOOKS SET Quantity = Quantity + 1 WHERE ISBN = ?");
                $update->bind_param("s", $isbn);
                $update->execute();
            }            
        }

        $conn->commit();
        
        echo "
                <form id='redirectForm' action='Succesful_msg.php' method='POST'>
                    <input type='hidden' name='date' value='" . htmlspecialchars($date) . "'>
                    <input type='hidden' name='reg_no' value='" . htmlspecialchars($reg_no) . "'>
                    <input type='hidden' name='action' value='" . htmlspecialchars($action) . "'>
                </form>
                <script>
                    document.getElementById('redirectForm').submit();
                </script>
                ";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
            alert('⚠️Some Error Occured...Try again Later');
            window.location.href = 'QRScan.html';
            </script>";
    }
}
?>