<?php
session_start();
if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: login_admin.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "LMS");
if (!isset($conn) || !$conn) {
    $conn = new stdClass(); 
    $conn->prepare = function($query) {
        $stmt = new stdClass();
        $stmt->bind_param = function(...$params) use ($stmt) {  return $stmt; };
        $stmt->execute = function() use (&$stmt) { $stmt->error = ''; return true; };
        $stmt->get_result = function() {
            $result = new stdClass();
            $result->num_rows = 0;
            return $result;
        };
        return $stmt;
    };
    $conn->query = function($query) { };
    $conn->close = function() {};
    $conn->error = null; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['Name'] ?? null; //NAME for Librarian table
    $phone = $_POST['Phone'] ?? null; //Phone for Librarian table
    $password = $_POST['Password'] ?? null;//PASSWORD for Librarian_login
    $check = $conn->prepare("SELECT L_ID FROM LIBRARIAN WHERE Name = ? and Phone= ?");
    $check->bind_param("ss", $name, $phone);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
            echo "<script>
                alert('⚠️ Admin already exists.');
            </script>";
    } else {

        $insert_admin= $conn->prepare("INSERT INTO LIBRARIAN (Name,Phone) VALUES (?, ?)");
        $insert_admin->bind_param("ss",$name, $phone);
        $insert_login=$conn->prepare("INSERT INTO LIBRARIAN_LOGIN(PASSWORD) VALUES(?)");
        $insert_login->bind_param("s",$password);
        if ($insert_admin->execute() && $insert_login->execute()) {            
            $id_query="select * from librarian_login order by L_ID desc limit 1";
            $result=mysqli_query($conn,$id_query);
            $arr = mysqli_fetch_assoc($result);
            $id=$arr['L_ID'];
            echo "<script>
                alert('✅ New admin added successfully. The Admin Id is : ".$id."');
                if(confirm('Add another admin?')) {
                    window.location.href = 'add_admin.php';
                } else {
                    window.location.href = 'adminHomePage.php';
                }
            </script>";
        } else {
            echo "<script>alert('⚠️ Error while adding new admin');</script>";
        }
        $insert->close();
    }

    $check->close();
    if (is_object($conn) && method_exists($conn, 'close')) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Admin</title>
    <style>
        
        /* Go Back Styles */
        .back {
            color: black;
            margin-left: 40px;
            border-radius: 150px;
            width: 150px;
            text-decoration: none;
            display: flex;
            gap: 10px;
            justify-self:center;
            position:sticky;
            top:20px;
            left:20px;
        }

        .back p {
            font-size: 27px;
            align-self: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .back img {
            height: 42px;
            margin-top: 24px;
        }

        .back:hover {
            transform: scale(1.2);
            animation: scale 1.5s ease infinite;
        }

        @keyframes scale {
            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1.2);
            }
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right,rgb(185, 184, 233),rgb(152, 154, 218),rgb(84, 93, 221));
            padding: 40px 20px;
            margin: 0;
            display: flex;
            flex-direction: column;
            /* align-items: center; */
            min-height: 100vh;
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 2.5em; 
            font-weight: bold;
            margin-bottom: 30px;
        }
        form {
            background: #fff;
            padding: 30px 40px; 
            border-radius: 15px;
            max-width: 500px;
            width: 100%;
            margin: auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        label {
            display: block;
            margin-top: 20px;
            margin-bottom: 8px;
            color: #555; 
            font-size: 0.9em;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            margin-top: 5px;
            border: none;
            border-bottom: 2px solid #ddd;
            border-radius: 0;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
            font-size: 1em;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-bottom-color: #5c67f2; 
            outline: none;
        }
       
        ::placeholder { 
            color: #aaa;
            opacity: 1; 
        }
        :-ms-input-placeholder { 
            color: #aaa;
        }
        ::-ms-input-placeholder { 
            color: #aaa;
        }

        .image-upload-container {
            margin-top: 20px;
            margin-bottom: 20px;
        }

    .image-box {
            width: 100%; 
            max-width: 200px; 
            height: 200px;
            background-color: #f8f8f8; 
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 10px auto 15px;
            cursor: pointer;
        }

        .image-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: none;
            padding: 100px;
            justify-content:center;
            align-items: center;
        }
        
       
        input[type="file"] {
            border: 1px solid #ccc; 
            border-radius: 6px;
            padding: 10px;
            margin-top: 5px;
            background-color: #f9f9f9;
        }
        
        input[type="submit"] {
            /* background: linear-gradient(to right, #ef5eab, #5c67f2); */
            background: linear-gradient(to right,rgb(153, 179, 253),rgb(101, 135, 230),rgb(58, 91, 235));
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 30px; 
            padding: 15px 25px;
            font-size: 1.1em; 
            font-weight: bold;
            border-radius: 25px;
            width: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        input[type="submit"]::after { 
            content: ' →';
            margin-left: 8px;
            font-size: 1.2em;
        }
        input:invalid {
        }
        
    </style>
</head>
<body>
    <a href="adminHomePage.php" class="back">
          <img src="uploads/required_pics/back.png" alt="">
          <p>Go Back</p>
    </a>
    <form action="add_admin.php" method="POST"><!--enctype="multipart/form-data"-->
        <h2>Add New Admin</h2>
        
        <label for="Name">Name</label>
        <input type="text" id="name" name="Name" placeholder="name title" required>

        <label for="Phone">Phone</label>
        <input type="text" name="Phone" pattern="[0-9]{10}" placeholder="Enter 10-digit phone number" required>

        <label for="Password">Create Password</label>
        <input type="text" name="Password" 
        pattern="^[a-zA-Z]+[0-9]+[^a-zA-Z0-9]+.*$"
       placeholder="Must be at least 6 characters long format:(A-Z)(0-9)(special char)"
       minlength="6"
       required>

        <input type="submit" value="Submit">
    </form>

</body>
</html>