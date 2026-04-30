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
    $msg = false;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Request a Book</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="studentPageStyle.css">
    <style>
        .form-container {
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            padding-bottom: 21px;
            max-width: 500px;
            width: 500px;
            margin: 0 auto;
            margin-top: 10px;
            margin-bottom: 20px;
            box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin: 0 0 20px 0;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #9372f5;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top:37px;
        }

        button:hover {
            background-color:rgba(64, 37, 163, 0.92);
        }

        .options {
            display: none;
            margin-top: 20px;
            text-align: center;
        }

        .options button {
            width: 45%;
            margin: 5px;
            background-color: #007bff;
        }

        .options button:hover {
            background-color: #0056b3;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: bold;
        }
        main {
            margin-top: 60 px; 
            margin-left: 110px; 
            padding: 20px; 
            width:100vw; 
            height:110vh;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside>
        <?php include "sidebar.php" ?>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- START: Book Dashboard section -->
        <!-- <div id="dashboard-section" class="content-section" style="display: block;"> -->
            <div class="form-container">
                <h2 style="margin-bottom:2px;">Request a Book</h2>
                <form id="requestForm" method="POST" action="#">
                    <label for="bookName">Book Name:</label>
                    <input type="text" id="bookName" name="bookName" required>

                    <label for="author1">Author Name 1:</label>
                    <input type="text" id="author1" name="author1" required>

                    <label for="author2">Author Name 2 (optional):</label>
                    <input type="text" id="author2" name="author2">

                    <label for="author3">Author Name 3 (optional):</label>
                    <input type="text" id="author3" name="author3">

                    <label for="isbn">ISBN (optional):</label>
                    <input type="text" id="isbn" name="isbn">

                    <label for="category">Book Category:</label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                        <option value="Bengali">Bengali</option>
                        <option value="English">English</option>
                        <option value="History">History</option>
                        <option value="Geography">Geography</option>
                        <option value="Math">Math</option>
                        <option value="Physics">Physics</option>
                        <option value="Chemistry">Chemistry</option>
                        <option value="Zoology">Zoology</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Statistics">Statistics</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Commerce">Commerce</option>
                        <option value="Economics">Economics</option>
                    </select>

                    <label for="edition">Book Edition (optional):</label>
                    <input type="text" id="edition" name="edition">

                    <button type="submit" style="margin-bottom:10px;">Request Book</button>
                </form>
                <div class="options" id="afterSubmitOptions">
                    <p>Request submitted successfully.</p>
                    <button onclick="goHome()">Ok</button>
                </div>
            </div>
        <!-- </div> -->
        <!-- END: Book Dashboard section -->

        <!--</div>end of book container-->
        <!-- </div>  -->
        <!--end of main-->
    </main>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // $req_id = $_POST['req_id'] ?? '';
        $bookName = $_POST['bookName'] ?? '';
        $authorName1 = $_POST['author1'] ?? '';
        $authorName2 = $_POST['author2'] ?? '';
        $authorName3 = $_POST['author3'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $category = $_POST['category'] ?? '';
        $edition = $_POST['edition'] ?? '';

        $stmt = $conn->prepare("INSERT INTO request_books (book_name, author_name_1, author_name_2, author_name_3, isbn, user_id, category, edition) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $bookName, $authorName1, $authorName2, $authorName3, $isbn, $login_id, $category, $edition);
        // $stmt->execute();
        if ($stmt->execute()) {
            echo "<script>
                    alert('✅ Book requested successfully.');
                    window.location.href = 'request.php';
                    </script>";
        } else {
            echo "<script>
                    alert('⚠️ Failed to request Book');
                    window.location.href = 'request.php';
                    </script>";
        }
        $stmt->close();
        $conn->close();
    }
    ?>
    <script>
        function goHome() {
            window.location.href = "request.php";
        }
    </script>
</body>

</html>