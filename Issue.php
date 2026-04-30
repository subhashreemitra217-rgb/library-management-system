<?php
    // session_start();
    //   if (!isset($_SESSION['user'])) {
    //     session_destroy();
    //     header("Location: index.html");
    //     exit();
    //   }
    require_once("Connection.php");
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Fetch Values
        date_default_timezone_set('Asia/Kolkata');
        $date_today = date('Y-m-d');
        $isbn = $_POST['isbn'];
        $user_id = $_POST['user_id'];
        $action = $_POST['action'];

        //Validate book
        $check = $conn->prepare("SELECT * FROM BOOKS WHERE ISBN = ?");
        $check->bind_param("s", $isbn);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows == 0) {
            echo "<script>alert('⚠️ Invalid ISBN');
                    window.location.href = 'QRScan.html';
                    </script>";
            exit();
        }

        // Validate user
        $check = $conn->prepare("SELECT * FROM LOGIN_READERS WHERE LOGIN_ID = ?");
        $check->bind_param("s", $user_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows == 0) {
            echo "<script>alert('⚠️ Unauthorized User!!! Enter a Valid User Id');
                    window.location.href = 'QRScan.html';
                    </script>";
            exit();
        }

        // Check latest record of issue for this user-book pair
        $active_check = $conn->prepare("SELECT * FROM REPORTS WHERE USER_ID = ? AND BOOK_NO = ? ORDER BY Reg_no DESC LIMIT 1");
        $active_check->bind_param("ss", $user_id, $isbn);
        $active_check->execute();
        $last_record = $active_check->get_result()->fetch_assoc();
    
        $can_issue = true;
        if ($last_record && in_array($last_record['Issue_return'], ['Issued', 'Reissued'])) {
            $can_issue = false;
        }
        $expected_return = date('Y-m-d', strtotime('+14 days'));
        
        
        if ($action === 'issue') {
            if (!$can_issue) {
                echo "<script>
                    alert('⚠️ Book is already Issued or Reissued. Please return it before Issuing again.');
                    window.location.href = 'QRScan.html';
                    </script>";
                exit();
            }

            // Check quantity
            $qty_check = $conn->prepare("SELECT Quantity FROM BOOKS WHERE ISBN = ?");
            $qty_check->bind_param("s", $isbn);
            $qty_check->execute();
            $qty_result = $qty_check->get_result()->fetch_assoc();

            if ($qty_result['Quantity'] <= 0) {
                echo "<script>
                    alert('⚠️ Book Out of Stock');
                    window.location.href = 'QRScan.html';
                    </script>";
                exit();
            }


            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO REPORTS (USER_ID, BOOK_NO, Issue_return, Issue_date, Expected_return_date) VALUES (?, ?, 'Issued', ?, ?)");
                $stmt->bind_param("ssss", $user_id, $isbn, $date_today, $expected_return);
                $stmt->execute();

                $update = $conn->prepare("UPDATE BOOKS SET Quantity = Quantity - 1 WHERE ISBN = ?");
                $update->bind_param("s", $isbn);
                $update->execute();

                $conn->commit();

                //Fetch Report Number
                $query = "SELECT Reg_no FROM REPORTS ORDER BY Reg_no DESC LIMIT 1;";
                $res=mysqli_query($conn,$query);
                $arr=mysqli_fetch_assoc($res);
                $reg_no=$arr['Reg_no'];

                echo "
                    <form id='redirectForm' action='Succesful_msg.php' method='POST'>
                        <input type='hidden' name='date' value='" . htmlspecialchars($date_today) . "'>
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
        elseif ($action === 'return' || $action === 'reissue') {
            if (!$last_record || !in_array($last_record['Issue_return'], ['Issued', 'Reissued'])) {    
                echo "<script>
                    alert('⚠️This book has not been issued or reissued by the user');
                    window.location.href = 'QRScan.html';
                    </script>";
            }
            echo "<form id='redirectForm' action='return_form.php' method='POST'>
                        <input type='hidden' name='action' value='" . htmlspecialchars($action) . "'>
                        <input type='hidden' name='isbn' value='" . htmlspecialchars($isbn) . "'>
                        <input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>
                    </form>
                    <script>
                        document.getElementById('redirectForm').submit();
                    </script>";
            exit();
        }  
    }  
?>