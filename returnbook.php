<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    require_once("Connection.php"); 

    //Fetch Values   
    $isbn = $_POST['isbn'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $action = $_POST['reissue'] ?? '';
    $book_status=$_POST['book_status'] ?? '';
    $fine=(float)$_POST['fine_amount'] ?? '0';

    
    $action = $action ? 'Reissued' : ($book_status === 'lost' ? 'Lost' : 'Returned');

    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');

    //Calculate Fine
    $report_result = $conn->query("SELECT * FROM REPORTS WHERE USER_ID = '$user_id' AND BOOK_NO = '$isbn' AND Issue_return IN ('Issued','Reissued') ORDER BY Reg_no DESC LIMIT 1");

    if ($report_result->num_rows === 0) {
        echo "<script>
            alert('⚠️This book has not been issued or reissued by the user');
            window.location.href = 'QRScan.html';
            </script>";
        exit();
    }
    
    $return_date = $date;
    $report = $report_result->fetch_assoc();
    $expected = $report['Expected_return_date'];
    $days_late = max(0, (strtotime($return_date) - strtotime($expected)) / (60*60*24));
    if ($days_late!=0) {
        $days_late=$days_late-1;
    }
    $late_fine = $days_late * 3;

    echo $late_fine;
    $fine_amount = (float)$late_fine + $fine;
    if ($fine_amount==0) {
        $conn->begin_transaction();
        try {
            //Fetch Report Number
            $query = "SELECT Reg_no FROM REPORTS WHERE USER_ID = '$user_id' AND BOOK_NO = '$isbn' AND Issue_return IN ('Issued','Reissued') ORDER BY Reg_no DESC LIMIT 1";
            $res=mysqli_query($conn,$query);
            $arr=mysqli_fetch_assoc($res);
            $reg_no=$arr['Reg_no'];
            
            if ($action == 'Reissued') {
                $expected_return = date('Y-m-d', strtotime('+14 days'));
                $stmt = $conn->prepare("UPDATE REPORTS SET Issue_return = ?, Issue_date = ? ,Expected_return_date = ? WHERE Reg_no = ?");
                $stmt->bind_param("ssss",  $action,$date,$expected_return,$reg_no);
                $stmt->execute();
            } else {                
                $stmt = $conn->prepare("UPDATE REPORTS SET Issue_return = ?, Return_date = ? WHERE Reg_no = ?");
                $stmt->bind_param("sss",  $action,$date,$reg_no);
                $stmt->execute();

                $update = $conn->prepare("UPDATE BOOKS SET Quantity = Quantity + 1 WHERE ISBN = ?");
                $update->bind_param("s", $isbn);
                $update->execute();
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
    else {
        echo "
                <form id='redirectForm' action='Fine.php' method='POST'>
                    <input type='hidden' name='isbn' value='" . htmlspecialchars($isbn) . "'>
                    <input type='hidden' name='action' value='" . htmlspecialchars($action) . "'>
                    <input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>
                    <input type='hidden' name='book_status' value='" . htmlspecialchars($book_status) . "'>
                    <input type='hidden' name='fine' value='" . htmlspecialchars($fine_amount) . "'>
                </form>
                <script>
                    document.getElementById('redirectForm').submit();
                </script>
                ";
    }
}
?>