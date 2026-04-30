<?php
session_start();
if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}
require_once("Connection.php");
function showStudents($result)
{
    echo "<div class='all-students'>";
    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        $arr = mysqli_fetch_assoc($result);
        $name = $arr["FNAME"] . " " . $arr["LNAME"];
        $path = $arr['PFP_PATH']; ?>
        <div class="card">
            <div class="parent">
                <div class="image">
                    <img src="<?php echo "$path"; ?>" alt="">
                </div>
                <div class="details">
                    <div class="det">
                        <h1><?php echo "$name"; ?></h1>
                        <p><strong>Student ID :</strong> <?php echo $arr['USER_ID']; ?></p>
                        <p><strong>Sem :</strong> <?php echo $arr['SEM']; ?></p>
                        <p><strong>College Roll :</strong> <?php echo $arr['CLG_ROLL']; ?></p>
                        <p><strong>Department :</strong> <?php echo $arr['DEPARTMENT']; ?></p>
                    </div>
                </div>
            </div>
            <div class="long_details">
                <p><strong>Phone Number :</strong> <?php echo $arr['PH_NO']; ?></p>
                <p><strong>Address :</strong> <?php echo $arr['ADDRESS']; ?></p>
                <p><strong>Email :</strong> <?php echo $arr['EMAIL']; ?></p>
            </div>
        </div><?php
    }
    echo "</div>";
}
?>
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

        p,
        strong,
        h1 {
            color: rgba(32, 31, 31, 0.968);
        }

        main .card {
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            height: auto;
            box-shadow: 1px 7px 5px rgba(0, 0, 0, 0.648);
            display: grid;
            padding: 10px 20px 30px 10px;
            border-radius: 30px;
            width: 400px;
            transition-property: all;
            transition-duration: 0.3s;
            transition-timing-function: ease-in-out;
        }

        main .card:hover {
            transform: scale(1.1);
            background-color: #68625b2a;
        }

        main .card .image {
            height: 110px;
            width: 110px;
            border-radius: 100px;
            transform: translatey(35px);
            margin-left: 20px;
            margin-right: 8px;
            margin-top: 20px;
        }

        main .card .parent {
            display: flex;
        }

        main .card .image img {
            width: 100%;
            height: 100%;
            border-radius: 150px;
            object-fit: contain;
        }

        main .card .details {
            text-align: left;
            line-height: 25px;
        }

        main .card .details p,
        .long_details {
            font-size: 15px;
        }

        main .card .details p {
            padding-left: 20px;
        }

        .details h1 {
            font-size: 22px;
            text-align: center;
            padding-left: 10px;
            font-family: Georgia, 'Times New Roman', Times, serif;
            margin: 2rem 0 1rem 0;
            position: relative;
            left: -15px;
        }

        .long_details {
            text-align: left;
            padding-left: 3.3rem;
            padding-right: 1rem;
            line-height: 2em;
            margin-top: 8px;
        }
        .all-students {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 35px;
            margin-top: 30px;
            padding: 20px 20px 20px 50px;
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
        <form action="View_tabular.php" method="post">
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
                <a href="View_Student.php" class="back" style="margin:50px 0px 0px 50px; width:149px">
                    <img src="uploads/required_pics/back.png" alt="">
                    <p>Go Back</p>
                </a><?php
                if ($report_result->num_rows > 0) {
                    echo "<br><h2 class='searchResult'>🔍 Search Results for '" . $_POST["searchInput"] . "'</h2>";
                    showStudents($report_result);
                } else {
                    echo "<h2 class='searchResult'>No results found for '<strong>" . $_POST["searchInput"] . "</strong>'</h2>";
                }
                echo "</div><br><br>";
                exit();
            }
        }
        $query = "SELECT * from READERS R, LOGIN_READERS L WHERE R.USER_ID=L.LOGIN_ID  ORDER BY R.DEPARTMENT;";
        $result = mysqli_query($conn, $query);
        showStudents($result);
        ?>
    </main>
    <?php
    mysqli_close($conn);
    ?>
</body>

</html>