<?php
session_start();
if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "LMS";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendMail($to, $name, $uid, $pwd = '', $status = 'Approved') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'testcollegemail1234@gmail.com';
        $mail->Password = 'oiqixzyhnlpyfqrs'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('testcollegemail1234@gmail.com', 'Library Admin');
        $mail->addAddress($to);
        $mail->isHTML(false);

        if ($status === 'Approved') {
            $mail->Subject = 'Library Access Approved';
            $mail->Body = "Dear $name,

We are pleased to inform you that you have been successfully registered as a valid reader of the College Library.

You can now access all library services and resources using the following credentials:

Library ID: $uid  
Password: $pwd

Please use these credentials to log in every time you visit the college library or access our online portal.

For your security, do not share your login details with anyone. If you face any issues, feel free to reach out to the library help desk.

Welcome aboard, and happy reading!

Best regards,  
Library Admin  
MMC COLLEGE
";
        } else {
            $mail->Subject = 'Library Access Rejected';
            $mail->Body = "Dear $name,

We regret to inform you that your library access request has been rejected.

Please contact the librarian for further assistance.

Regards,
Library Admin
 MMC COLLEGE";
        }

        $mail->send();
        echo "<script>alert('Email sent successfully!'); window.location.href='approve_requests.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Email sending failed: {$mail->ErrorInfo}');</script>";
    }
}

// Approve
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];

    $getReq = $conn->prepare("SELECT USER_ID, PASSWORD FROM SIGNUP_REQUESTS WHERE REQUEST_ID = ? AND STATUS = 'Pending'");
    $getReq->bind_param("i", $id);
    $getReq->execute();
    $res = $getReq->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $uid = $row['USER_ID'];
        $pwd = $row['PASSWORD'];

        $checkReader = $conn->prepare("SELECT FNAME, EMAIL FROM READERS WHERE USER_ID = ?");
        $checkReader->bind_param("s", $uid);
        $checkReader->execute();
        $r = $checkReader->get_result();

        if ($r->num_rows === 1) {
            $reader = $r->fetch_assoc();
            $email = $reader['EMAIL'];
            $fname = $reader['FNAME'];

            $insert = $conn->prepare("INSERT INTO LOGIN_READERS (LOGIN_ID, PASSWORD) VALUES (?, ?)");
            $insert->bind_param("ss", $uid, $pwd);
            if ($insert->execute()) {
                $update = $conn->prepare("UPDATE SIGNUP_REQUESTS SET STATUS = 'Approved' WHERE REQUEST_ID = ?");
                $update->bind_param("i", $id);
                $update->execute();
                sendMail($email, $fname, $uid, $pwd, 'Approved');
            }
        }
    }
}

// Reject
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];

    $getReq = $conn->prepare("SELECT USER_ID FROM SIGNUP_REQUESTS WHERE REQUEST_ID = ? AND STATUS = 'Pending'");
    $getReq->bind_param("i", $id);
    $getReq->execute();
    $res = $getReq->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $uid = $row['USER_ID'];

        $checkReader = $conn->prepare("SELECT FNAME, EMAIL FROM READERS WHERE USER_ID = ?");
        $checkReader->bind_param("s", $uid);
        $checkReader->execute();
        $r = $checkReader->get_result();

        if ($r->num_rows === 1) {
            $reader = $r->fetch_assoc();
            $email = $reader['EMAIL'];
            $fname = $reader['FNAME'];

            $update = $conn->prepare("UPDATE SIGNUP_REQUESTS SET STATUS = 'Rejected' WHERE REQUEST_ID = ?");
            $update->bind_param("i", $id);
            $update->execute();

            sendMail($email, $fname, $uid, '', 'Rejected');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, var(--light), var(--middle), rgb(79, 166, 109));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 50vh;
            color: #333;
        }

        main {
            margin: 60px 20px 20px 110px;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.29);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1800px;
            text-align: center;
        }

        .container h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: rgb(132 156 174 / 50%);
            padding: 12px 16px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }

        td {
            background-color: rgba(221, 220, 182, 0.6);
            padding: 12px 16px;
            font-size: 17px;
            border-bottom: 1px solid #e1e8f0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:not(:first-child):hover {
            background-color: rgba(249, 249, 249, 0.46);
        }

        a {
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: rgb(62, 63, 63);
        }

        .approve-button {
            display: inline-block;
            padding: 8px 15px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(to right, rgba(141, 136, 207, 0.79), rgba(131, 159, 193, 0.69));
            color: black;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .approve-button:hover {
            background: linear-gradient(to right, rgba(141, 136, 207, 0.3), rgba(131, 159, 193, 0.34));
        }

        .pfp_table_img img {
            width: 67px;
            height: 67px;
            border-radius: 65px;
        }
    </style>
</head>

<body>
    <aside>
        <?php include "side.php"; ?>
    </aside>
    <main>

        <div class="container">
            <h2>Signup Requests</h2>
            <table>
                <tr>
                    <th>Request ID</th>
                    <th>Profile</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Sem</th>
                    <th>Roll</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php
                //$query = "SELECT * FROM SIGNUP_REQUESTS S , readers R WHERE STATUS = 'Pending' and S.user_id=R.user_id";
                $query = "SELECT * FROM SIGNUP_REQUESTS S, READERS R WHERE S.USER_ID = R.USER_ID ORDER BY REQUEST_ID DESC";
          $res = $conn->query($query);
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        $name = $row["FNAME"] . " " . $row["LNAME"];
                        $path = $row['PFP_PATH'];
$status = $row['STATUS'];
$actionButtons = "";

if ($status === 'Pending') {
    $actionButtons = "
        <a href='approve_requests.php?approve={$row['REQUEST_ID']}' class='approve-button'>Approve</a>
        &nbsp;
        <a href='approve_requests.php?reject={$row['REQUEST_ID']}' class='approve-button' style='background:linear-gradient(to right, #f7786b, #ffa07a);'>Reject</a>
    ";
} else {
    $actionButtons = "<span style='color:gray;'>No action needed</span>";
}

echo "<tr>                        
    <td>{$row['REQUEST_ID']}</td>
    <td class='pfp_table_img'><img src='{$row['PFP_PATH']}' alt=''></td>
    <td>{$row['USER_ID']}</td>
    <td>{$row['FNAME']} {$row['LNAME']}</td>
    <td>{$row['SEM']}</td>
    <td>{$row['CLG_ROLL']}</td>
    <td>{$row['DEPARTMENT']}</td>
    <td>{$status}</td>
    <td>$actionButtons</td>
</tr>";

                    }
                } else {
                    echo "<tr style='height:62px;'><td colspan='9' style='color:#787878;'>- - - No signup requests - - -</td></tr>";
                }
                ?>

            </table>
        </div>
    </main>
</body>

</html>