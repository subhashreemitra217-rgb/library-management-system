<?php
session_start();
if (!isset($_SESSION['user'])) {
  session_destroy();
  header("Location: index.html");
  exit();
}

//For Mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';


require_once("Connection.php");
function showReports($result, $isRem)
{ ?>
  <div class="table-card">
    <table>
      <tr>
        <th>Report No.</th>
        <th>User Id</th>
        <th>Name</th>
        <th>Book Name</th>
        <th>Action</th>
        <th>Issue Date</th>
        <th>Expected Return Date</th>
        <th>Return Date</th>
        <th>Fine Amount</th>
        <th>Fine Paid</th>
        <?php if ($isRem) {
          echo "<th>Send Reminder</th>";
        } ?>
      </tr>
      <?php
      if ($result->num_rows == 0) {
        echo "<tr style='height:62px;'><td colspan='10' style='color:#787878;'>- - - No Data Found - - -</td><tr></table>";
        return;
      }
      date_default_timezone_set('Asia/Kolkata');
      $today = date('Y-m-d');
      for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        $arr = mysqli_fetch_assoc($result);
        $name = $arr["FNAME"] . " " . $arr["LNAME"];

        $Return_date = $arr['Return_date'] ?? " - ";
        if ($Return_date == "0000-00-00") {
          $Return_date = " - ";
        }

        $nullreturn = $Return_date == " - " ? true : false;
        $hasFine = (
          ($nullreturn && $arr['Expected_return_date'] < $today) ||
          (!$nullreturn && $arr['Expected_return_date'] < $arr['Return_date'])
        ) ? true : false;

        $Fine_amount = "₹" . $arr['Fine_amount'];
        if (($arr['Fine_amount'] === NULL || $arr['Fine_amount'] == "0.00") && $hasFine && ($arr['Issue_return'] == 'Issued' || $arr['Issue_return'] == 'Reissued')) {
          $expectedReturnDate = new DateTime($arr['Expected_return_date']);
          $todayDate = new DateTime($today);
          $interval = $todayDate->diff($expectedReturnDate);
          $days = (int) $interval->format('%a');
          if ($days != 0) {
            $due_date = $days - 1;
            $amount = ($days - 1) * 3;
            $amount = number_format($amount, 2); //to convert 9 -> 9.00
          }
          $Fine_amount = "₹" . $amount;
        } else if ($arr['Fine_amount'] === NULL || $arr['Fine_amount'] == "0.00") {
          $Fine_amount = " - ";
        }
        $Expected_Return_date = $arr['Expected_return_date'] ?? " - ";

        $Fine_paid = $arr['Fine_paid'];
        if ($arr['Fine_paid'] != 'yes' && $hasFine) {
          $Fine_paid = "No";
        } elseif ($arr['Fine_paid'] == null) {
          $Fine_paid = " - ";
        } ?>
        <tr>
          <td><?php echo $arr['Reg_no'] ?></td>
          <td><?php echo $arr['USER_ID'] ?></td>
          <td><?php echo $name ?></td>
          <td><?php echo $arr['TITLE'] ?></td>
          <td><?php echo $arr['Issue_return'] ?></td>
          <td><?php echo $arr['Issue_date'] ?></td>
          <td><?php echo $Expected_Return_date ?></td>
          <td><?php echo $Return_date ?></td>
          <td><?php echo $Fine_amount ?></td>
          <td><?php echo $Fine_paid ?></td>
          <?php if ($isRem) { ?>
            <td>
              <form action="" method="post">
                <input type="hidden" name="report_id" value="<?php echo $arr['Reg_no']; ?>">
                <input type="hidden" name="amount" value="<?php echo $Fine_amount; ?>">
                <input type="hidden" name="due_date" value="<?php echo $due_date; ?>">
                <button name="reminder_btn" id="reminder" style="background: none; border:none;">
                  <img src="uploads/required_pics/bell.png" alt="Send Reminder" height="30px">
                </button>
              </form>
            </td><?php
          } ?>
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
  <title>Logbook</title>
  <link rel="stylesheet" href="searchboxStyle.css">
  <style>
    #reminder:hover {
      transform: scale(1.2);
    }

    #reminder {
      transition: 0.3s ease-in-out;
    }

    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: black;
    }

    html {
      height: 100%;
    }

    header {
      gap: 11px;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: linear-gradient(to right, var(--light), var(--middle), rgb(79, 166, 109));
    }

    main {
      justify-self: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin-top: 60px;
      margin-left: 110px;
      padding: 50px;
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
      height: 4em;
    }

    tr:not(:first-child):hover {
      background-color: rgba(249, 249, 249, 0.46);
    }

    tr:last-child td {
      border-bottom: none;
    }

    .back-button {
      text-align: center;
      margin-top: 20px;
    }

    .logo-container {
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
      height: 50px;
      width: 50px;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      border: none;
      cursor: pointer;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .logo-container img {
      width: 30px;
      height: 30px;
      display: block;
      transition: transform 0.4s ease;
    }

    .logo-container:hover img {
      transform: scale(1.1);
    }

    .toolttext {
      visibility: hidden;
      background-color: #333;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px;
      position: absolute;
      z-index: 100;
      bottom: -75%;
      right: -57%;
      margin-left: -35px;
      opacity: 0;
      transition: opacity 0.4s;
      font-size: 13px;
    }

    .logo-container:hover .toolttext {
      visibility: visible;
      opacity: 1;
    }

    .date-form {
      margin-top: 10px;
      background-color: rgb(78, 78, 86);
      padding: 15px;
      border-radius: 5px;
      display: none;
      position: absolute;
      z-index: 150;
      bottom: -209%;
      right: -121%;
    }

    .date-form input[type="date"],
    input[type="month"] {
      padding: 5px;
      padding-right: 10px;
      text-align: center;
      font-size: 14px;
      background-color: rgb(143, 198, 126);
      color: rgb(0, 0, 0);
      border: none;
      border-radius: 20px;
      width: 140px;
      margin-bottom: 5px;
    }

    .date-form button {
      background-color: rgb(165, 171, 220);
      border: none;
      margin-top: 5px;
      padding: 5px 10px;
      border-radius: 20px;
      cursor: pointer;
    }

    .date-form button:hover {
      background-color: rgb(183, 189, 233);
      transform: scale(1.1);
    }

    .other_search {
      display: flex;
      gap: 5px;
      margin-left: 30px;
    }
  </style>
  <script>
    window.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("dateForm");
      const resetBtn = document.getElementById("resetBtn");
      const submitBtn = document.getElementById("submitBtn");
      const logoIcon = document.getElementById("dateIcon");

      logoIcon.addEventListener("click", (e) => {
        e.stopPropagation();
        form.style.display = (form.style.display === "block") ? "none" : "block";
      });

      form.addEventListener("click", (e) => {
        e.stopPropagation();
      });

      resetBtn.addEventListener("click", () => {
        form.style.display = "none";
      });

      submitBtn.addEventListener("click", () => {
        form.style.display = "none";
      });
    });
    window.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("monthForm");
      const resetBtn = document.getElementById("resetBtn2");
      const submitBtn = document.getElementById("submitBtn2");
      const logoIcon = document.getElementById("monthIcon");

      logoIcon.addEventListener("click", (e) => {
        e.stopPropagation();
        form.style.display = (form.style.display === "block") ? "none" : "block";
      });

      form.addEventListener("click", (e) => {
        e.stopPropagation();
      });

      resetBtn.addEventListener("click", () => {
        form.style.display = "none";
      });

      submitBtn.addEventListener("click", () => {
        form.style.display = "none";
      });
    });
  </script>
</head>

<body>
  <header>
    <form class="search-box" action="" method="POST">
      <input type="text" name="searchInput" placeholder="Search">
      <button type="submit" name="Search"><img src="uploads/required_pics/search.png" alt=""></button>
    </form>
    <div class="other_search">
      <div class="logo-container" onclick="showForm(event)">
        <img src="uploads/required_pics/date.png" alt="Logo" id="dateIcon">
        <span class="toolttext" id="tooltip">Search By Date</span>
        <form id="dateForm" class="date-form" action="" method="post">
          <input type="date" name="dateSearch" id="dateInput"><br>
          <button style="margin-right: 10px;" type="submit" name="SubmitDate" id="submitBtn">Submit</button>
          <button type="reset" id="resetBtn">Cancel</button>
        </form>
      </div>
      <div class="logo-container" onclick="showForm(event)">
        <img src="uploads/required_pics/month.png" alt="Logo" id="monthIcon">
        <span class="toolttext">Search By Month</span>
        <form id="monthForm" class="date-form" action="" method="post">
          <input type="month" name="monthSearch" id="monthInput"><br>
          <button style="margin-right: 10px;" type="submit" name="SubmitMonth" id="submitBtn2">Submit</button>
          <button type="reset" id="resetBtn2">Cancel</button>
        </form>
      </div>
      <form action="" method="post">
        <button name="Non-paid" class="logo-container" style="height: 50px;width: 50px; background: none;">
          <img src="uploads/required_pics/calculator-money.png" alt="Logo">
          <span class="toolttext">Non-paid Fines</span>
        </button>
      </form>
    </div>
  </header>
  <aside>
    <?php include "side.php"; ?>
  </aside>
  <main>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['Search']) && !empty(trim($_POST['searchInput']))) {
        $search = '%' . trim($_POST["searchInput"]) . '%';
        $reportsearch = "SELECT R.*,B.TITLE,RE.FNAME,RE.LNAME FROM READERS RE, LOGIN_READERS L, REPORTS R, BOOKS B 
                         WHERE RE.USER_ID=L.LOGIN_ID AND L.LOGIN_ID=R.USER_ID AND B.ISBN=R.BOOK_NO AND 
                         (TITLE LIKE ? OR FNAME LIKE ? OR LNAME LIKE ? OR L.LOGIN_ID LIKE ?
                         OR R.Reg_no LIKE ?) ORDER BY Reg_no DESC;";
        $stmt = $conn->prepare($reportsearch);
        $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
        $stmt->execute();
        $report_result = $stmt->get_result(); ?>
        <a href="Report_table.php" class="back">
          <img src="uploads/required_pics/back.png" alt="">
          <p>Go Back</p>
        </a><?php
        if ($report_result->num_rows > 0) {
          echo "<h2 class='searchResult'>🔍 Search Results for '" . $_POST["searchInput"] . "'</h2>";
          showReports($report_result, false);
        } else {
          echo "<h2 class='searchResult'>No results found for '" . $_POST["searchInput"] . "'</h2>";
        }
        echo "<br><br>";
        exit();
      }
      if (isset($_POST['SubmitDate']) && !empty(trim($_POST['dateSearch']))) {
        $date = $_POST['dateSearch'];
        $datereportsearch = "SELECT R.*,B.TITLE,RE.FNAME,RE.LNAME FROM READERS RE, LOGIN_READERS L, REPORTS R, BOOKS B 
                            WHERE RE.USER_ID=L.LOGIN_ID AND L.LOGIN_ID=R.USER_ID AND B.ISBN=R.BOOK_NO AND 
                            (Issue_date = ? OR  Return_date  = ? OR Expected_return_date = ?) ORDER BY Reg_no DESC;";
        $stmt = $conn->prepare($datereportsearch);
        $stmt->bind_param("sss", $date, $date, $date);
        $stmt->execute();
        $report_result = $stmt->get_result(); ?>
        <a href="Report_table.php" class="back">
          <img src="uploads/required_pics/back.png" alt="">
          <p>Go Back</p>
        </a><?php
        if ($report_result->num_rows > 0) {
          echo "<h2 class='searchResult'>Reports of " . $date . "</h2>";
          showReports($report_result, false);
        } else {
          echo "<h2 class='searchResult'>No results found for " . $date . "</h2>";
        }
        echo "<br><br>";
        exit();
      }
      if (isset($_POST['SubmitMonth']) && !empty(trim($_POST['monthSearch']))) {
        $year_month = $_POST['monthSearch'];
        $year = substr($year_month, 0, 4);
        $month = substr($year_month, 5, 2);
        $months = [
          "01" => "January",
          "02" => "February",
          "03" => "March",
          "04" => "April",
          "05" => "May",
          "06" => "June",
          "07" => "July",
          "08" => "August",
          "09" => "September",
          "10" => "October",
          "11" => "November",
          "12" => "December"
        ];
        $monthreportsearch = "SELECT R.*,B.TITLE,RE.FNAME,RE.LNAME FROM READERS RE, LOGIN_READERS L, REPORTS R, BOOKS B 
                              WHERE RE.USER_ID=L.LOGIN_ID AND L.LOGIN_ID=R.USER_ID AND B.ISBN=R.BOOK_NO AND (
                              (MONTH(Issue_date) = ? AND YEAR(Issue_date) = ?) OR 
                              (MONTH(Return_date) = ? AND YEAR(Return_date) = ?) OR 
                              (MONTH( Expected_return_date) = ? AND YEAR( Expected_return_date) = ?)) 
                              ORDER BY Reg_no DESC;";
        $stmt = $conn->prepare($monthreportsearch);
        $stmt->bind_param("ssssss", $month, $year, $month, $year, $month, $year);
        $stmt->execute();
        $month_report_result = $stmt->get_result(); ?>
        <a href="Report_table.php" class="back">
          <img src="uploads/required_pics/back.png" alt="">
          <p>Go Back</p>
        </a><?php
        if ($month_report_result->num_rows > 0) {
          $msg = "Reports of " . $months[$month] . "  " . $year;
          echo "<h2 class='searchResult'>" . $msg . "</h2>";
          showReports($month_report_result, false);
        } else {
          echo "<h2 class='searchResult'>No results found for " . $months[$month] . "  " . $year . "</h2>";
        }
        echo "<br><br>";
        exit();
      }
      if (isset($_POST['Non-paid'])) {
        $query = "SELECT R.*,B.TITLE,RE.FNAME,RE.LNAME FROM READERS RE, LOGIN_READERS L,REPORTS R, BOOKS B 
                  WHERE RE.USER_ID=L.LOGIN_ID AND L.LOGIN_ID=R.USER_ID AND B.ISBN=R.BOOK_NO 
                  AND Expected_return_date < CURDATE() AND (Issue_return = 'Issued' OR Issue_return = 'Reissued'  )ORDER BY Reg_no;";
        $result = mysqli_query($conn, $query); ?>
        <a href="Report_table.php" class="back">
          <img src="uploads/required_pics/back.png" alt="">
          <p>Go Back</p>
        </a><?php
        if (mysqli_num_rows($result) > 0) {
          echo "<h2 class='searchResult'>Non-Paid Fines</h2>";
          showReports($result, true);
        } else {
          echo "<h2 class='searchResult'>No Fines </h2>";
        }
        echo "<br><br>";
        exit();
      }
      if (isset($_POST['reminder_btn'])) {
        $reportid = $_POST['report_id'];
        $reportsearch = "SELECT R.*,B.TITLE,RE.FNAME,RE.EMAIL FROM READERS RE, LOGIN_READERS L,REPORTS R, BOOKS B WHERE RE.USER_ID=L.LOGIN_ID AND L.LOGIN_ID=R.USER_ID AND B.ISBN=R.BOOK_NO AND R.REG_NO=? order by Reg_no";
        $stmt = $conn->prepare($reportsearch);
        $stmt->bind_param("s", $reportid);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $fname = $result['FNAME'];
        $email = $result['EMAIL'];
        $book = $result['TITLE'];
        $issue_date = $result['Issue_date'];
        $exp_date = $result['Expected_return_date'];
        $amount = $_POST['amount'];
        $due_date = $_POST['due_date'];

        // Send Email
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
          $mail->addAddress($email);
          $mail->Subject = 'Overdue Fine Notice';

          $mail->Body = "Dear " . $fname . ",

We hope this message finds you well.

This is a reminder that the book " . $book . ", which you borrowed from the library, has incurred a due fine. Please find the details below:

- 📘 **Issue Date:** " . $issue_date . "
- 📅 **Expected Return Date:** " . $exp_date . "
- ⏰ **Days Overdue:** " . $due_date . " days
- 💰 **Fine Amount:** " . $amount . "

We kindly request you to clear your fine at the earliest to avoid any further penalties or restrictions on your account.

If you believe this message was sent in error or you have already returned the item, please reach out to the library help desk immediately.

Thank you for your attention.

Best regards,  
Library Admin  
MMCC COLLEGE
";

          $mail->send();
          echo "<script>
                  alert('✅ Reminder sent successfully!');
                  var form = document.createElement('form');
                  form.method = 'POST';
                  form.action = 'Report_table.php';
                  var input = document.createElement('input');
                  input.type = 'hidden';
                  input.name = 'Non-paid';
                  input.value = '1';
                  form.appendChild(input);
                  document.body.appendChild(form);
                  form.submit();
                </script>";
          // echo "<script>alert(' ✅ Reminder sent successfully!'); window.location.href='Report_table.php';</script>";
        } catch (Exception $e) {
          echo "<script>alert(' ⚠️ Failed to send Reminder'); window.location.href='Report_table.php';</script>";
        }

      }
    }
    $query = "SELECT R.*,B.TITLE,RE.FNAME,RE.LNAME FROM READERS RE, LOGIN_READERS L,REPORTS R, BOOKS B WHERE RE.USER_ID=L.LOGIN_ID AND L.LOGIN_ID=R.USER_ID AND B.ISBN=R.BOOK_NO ORDER BY Reg_no DESC;";
    $result = mysqli_query($conn, $query);
    showReports($result, false);
    ?>
  </main>
</body>

</html>

<?php mysqli_close($conn); ?>