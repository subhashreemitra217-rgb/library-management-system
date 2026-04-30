<?php
session_start();
include 'Connection.php';
//1. fetch student ID
$login_id = $_SESSION['username'] ?? '';
if (!isset($_SESSION['username'])) {
  session_destroy();
  header("Location: index.html");
  exit();
}
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
$stmt = $conn->prepare("SELECT B.*,Reg_no, BOOK_NO, Issue_return, Issue_date, Expected_return_date, Return_date, Fine_amount, Fine_paid FROM REPORTS R,Books B WHERE B.ISBN=R.BOOK_NO AND USER_ID = ? ORDER BY reg_no DESC");
$stmt->bind_param("s", $login_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Library Card</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="studentPageStyle.css">
  <style>
    main {
      width: 100%;
      margin-right: 60px;
      margin-left: 170px;
    }

    .table-card {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      padding: 30px;
      width: 100%;
      /*max-width: 1000px;*/
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background-color: #d3d3d3;
      padding: 12px 16px;
      text-align: left;
      font-weight: bold;
      font-size: 16px;
    }

    td {
      background-color: #ffffff;
      padding: 12px 16px;
      font-size: 15px;
    }
  </style>
</head>

<body>
  <aside>
    <?php include "sidebar.php" ?>
  </aside>

  <!-- Main Content -->
  <main>

    <div id="dashboard-section" class="content-section"
      style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
      <!-- START: Book Dashboard section -->

      <h2>Library Card</h2><br>
      <div class="table-card">
        <!--<h2>Book Issue Report</h2>-->
        <?php if ($result->num_rows > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Report No</th>
                <th>Title</th>
                <th>Status</th>
                <th>Issue Date</th>
                <th>Expected Return</th>
                <th>Return Date</th>
                <th>Fine Amount</th>
                <th>Fine Paid</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()):
                date_default_timezone_set('Asia/Kolkata');
                $today = date('Y-m-d');
                $Return_date = $row['Return_date'] ?? "—";
                if ($Return_date == "0000-00-00") {
                  $Return_date = "—";
                }
                $nullreturn = $Return_date == "—" ? true : false;
                $hasFine = (
                  ($nullreturn && $row['Expected_return_date'] < $today) ||
                  (!$nullreturn && $row['Expected_return_date'] < $row['Return_date'])
                ) ? true : false;

                $Fine_amount = "₹" . $row['Fine_amount'];
                if (($row['Fine_amount'] === NULL || $row['Fine_amount'] == "0.00") && $hasFine && ($row['Issue_return'] == 'Issued' || $row['Issue_return'] == 'Reissued')) {
                  $expectedReturnDate = new DateTime($row['Expected_return_date']);
                  $todayDate = new DateTime($today);
                  $interval = $todayDate->diff($expectedReturnDate);
                  $days = (int) $interval->format('%a');
                  if ($days != 0) {
                    $due_date = $days - 1;
                    $amount = ($days - 1) * 3;
                    $amount = number_format($amount, 2); //to convert 9 -> 9.00
                  }
                  $Fine_amount = "₹" . $amount;
                } else if ($row['Fine_amount'] === NULL || $row['Fine_amount'] == "0.00") {
                  $Fine_amount = "—";
                }
                $Fine_paid = $row['Fine_paid'];
                if ($row['Fine_paid'] != 'yes' && $hasFine) {
                  $Fine_paid = "No";
                } elseif ($row['Fine_paid'] == null) {
                  $Fine_paid = "—";
                }
                ?>
                <tr>
                  <td><?php echo $row['Reg_no']; ?></td>
                  <td><?php echo $row['Title']; ?></td>
                  <td><?php echo $row['Issue_return']; ?></td>
                  <td><?php echo $row['Issue_date']; ?></td>
                  <td><?php echo $row['Expected_return_date']; ?></td>
                  <td><?php echo $Return_date?></td>
                  <td><?php echo $Fine_amount ?></td>
                  <td><?php echo $Fine_paid ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No report data found.</p>
        <?php endif; ?>
      </div>
      <!--</div>end of book container-->
  </main><!--end of main-->
</body>

</html>