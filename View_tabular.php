<?php
session_start();
if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}
require_once("Connection.php");
function showStudentsTabular($result)
{ ?>
    <div class="table-card">
        <table>
            <tr>
                <th>Profile</th>
                <th>ID</th>
                <th>Name</th>
                <th>Sem</th>
                <th>College Roll</th>
                <th>Department</th>
                <th>Phone No</th>
                <th>Address</th>
                <th>Email</th>
            </tr>
            <?php
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $arr = mysqli_fetch_assoc($result);
                $name = $arr["FNAME"] . " " . $arr["LNAME"];
                $path = $arr['PFP_PATH']; ?>
                <tr>
                    <td class="pfp_table_img"><img src="<?php echo "$path"; ?>" alt=""></td>
                    <td class="center"><?php echo $arr['USER_ID']; ?></td>
                    <td class="center"><?php echo $name; ?></td>
                    <td class="center"><?php echo $arr['SEM']; ?></td>
                    <td class="center"><?php echo $arr['CLG_ROLL']; ?></td>
                    <td class="center"><?php echo $arr['DEPARTMENT']; ?></td>
                    <td class="center"><?php echo $arr['PH_NO']; ?></td>
                    <td><?php echo $arr['ADDRESS']; ?></td>
                    <td><?php echo $arr['EMAIL']; ?></td>
                </tr> <?php
            } ?>
        </table>
    </div>
    <?php
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
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

        .pfp_table_img img {
            width: 67px;
            height: 67px;
            border-radius: 65px;
        }

        th {
            background-color: rgb(132 156 174 / 50%);
            padding: 12px 16px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }

        td {
            background-color: rgba(196, 195, 169, 0.47);
            padding: 12px 16px;
            font-size: 17px;
            border-bottom: 1px solid #e1e8f0;
        }

        .center {
            text-align: center;
        }

        tr:not(:first-child):hover {
            background-color: rgba(249, 249, 249, 0.46);
        }
        tr:last-child td{
            border-bottom: none;
        } 
        .logo-container {
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
            height: 50px;
            width: 50px;
            margin-left: 37px;
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
            right: -37%;
            margin-left: -35px;
            opacity: 0;
            transition: opacity 0.4s;
            font-size: 13px;
        }

        .logo-container:hover .toolttext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>
    <header>
        <form class="search-box" action="" method="POST">
            <input type="text" name="searchInput" placeholder="Search">
            <button type="submit" name="Search"><img src="uploads/required_pics/search.png" alt=""></button>
        </form>
        <form action="View_Student.php" method="post">
            <button name="View_change" class="logo-container" style="height: 50px;width: 50px; background: none;">
                <img src="uploads/required_pics/overview.png" alt="Logo">
                <span class="toolttext">Change View</span>
            </button>
        </form>
    </header>
    <aside>
        <?php include "side.php"; ?>
    </aside>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['Search'])) {
                $search = '%' . trim($_POST["searchInput"]) . '%';
                $reportsearch = "SELECT * from READERS R, LOGIN_READERS L WHERE R.USER_ID=L.LOGIN_ID AND (FNAME LIKE ? OR LNAME LIKE ? OR CLG_ROLL LIKE ? OR R.USER_ID LIKE ? OR DEPARTMENT LIKE ?) ORDER BY R.DEPARTMENT;";
                $stmt = $conn->prepare($reportsearch);
                $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
                $stmt->execute();
                $report_result = $stmt->get_result(); ?>
                <a href="View_tabular.php" class="back" style="margin:50px 0px 0px 50px; width:149px">
                    <img src="uploads/required_pics/back.png" alt="">
                    <p>Go Back</p>
                </a><?php
                if ($report_result->num_rows > 0) {
                    echo "<br><h2 class='searchResult'>🔍 Search Results for '" . $_POST["searchInput"] . "'</h2>";
                    showStudentsTabular($report_result);
                } else {
                    echo "<h2 class='searchResult'>No results found for '<strong>" . $_POST["searchInput"] . "</strong>'</h2>";
                }
                echo "</div><br><br>";
                exit();
            }
        }
        $query = "SELECT * from READERS R, LOGIN_READERS L WHERE R.USER_ID=L.LOGIN_ID  ORDER BY R.DEPARTMENT;";
        $result = mysqli_query($conn, $query);
        showStudentsTabular($result);
        ?>
    </main>
    <?php
    mysqli_close($conn);
    ?>
</body>
</html>