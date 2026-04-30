<?php

$host = 'localhost';
$db = 'LMS';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $user_id = $_POST['user_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $clg_roll = $_POST['clg_roll'];
    $sem = $_POST['sem'];
    $department = $_POST['department'];
    $ph_no = $_POST['ph_no'];
    $address = $_POST['address'];
    $email = $_POST['email'];

    $pfp_path = null;
    if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist, with write permissions
        }
        $pfp_path = $upload_dir . basename($_FILES["pfp"]["name"]);
        if (!move_uploaded_file($_FILES["pfp"]["tmp_name"], $pfp_path)) {
            // Handle file upload error
            echo "Error uploading file.";
            $pfp_path = null; // Reset path if upload failed
        }
    }

    $sql = "INSERT INTO READERS (USER_ID, FNAME, LNAME, CLG_ROLL, SEM, DEPARTMENT, PH_NO, ADDRESS, EMAIL, PFP_PATH) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssisssss", $user_id, $fname, $lname, $clg_roll, $sem, $department, $ph_no, $address, $email, $pfp_path);

    if ($stmt->execute()) {
        echo "<center><h1>Student inserted successfully.</h1></center>";
        echo '<center><h3><a href="add_students.html">Insert another student</a></h3></center><br>';
        echo '<center><h3><a href="index.html">Go to Home Page</a></h3><center>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <style>
        /* Go Back Styles */
        .back {
            color: black;
            margin-bottom: 0px;
            border-radius: 150px;
            margin-left: 80px;
            text-decoration: none;
            display: flex;
            gap: 10px;
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
            margin-top: 6px;
            padding: 20px 0px 20px 20px;
        }
        body {            
            background: linear-gradient(to right, rgb(250, 209, 224),rgb(177, 186, 249));
            min-height: 100vh;

        }
        .body {
            margin: 10px;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .form-container {
            background-color: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 550px;
            box-sizing: border-box;
        }

        .heading {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 1.5em;
            font-weight: bold;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 0.95em;
        }
        .image-upload-container {
            margin-top: 20px;
            margin-bottom: 20px;
            padding:10px;
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
            object-fit: cover;
            display: none;
            padding: 100px;
            justify-content:center;
            align-items: center;
        }
        img {
            justify-content:center;
            align-items: center;
            padding:20px; 
        }
         input[type="file"] {
            border: 1px solid #ccc; 
            border-radius: 6px;
            padding: 10px;
            margin-top: 5px;
            justify-content:center;
            align-items: center;
            background-color: #f9f9f9;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            padding: 12px 15px; 
            margin-bottom: 20px; 
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            color: #333;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus,
        select:focus {
            border-color: #8a2be2;
        }
        input::placeholder,
        textarea::placeholder {
            color: #aaa;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
            max-height: 150px;
        }
        
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-repeat: no-repeat;
            background-position: right 15px top 50%;
            background-size: 12px;
            padding-right: 30px;
        }

        input[type="submit"] {
            background: linear-gradient(to right,rgb(247, 115, 229),rgb(86, 83, 236)); 
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>    
    <a href="adminHomePage.php" class="back">
          <img src="uploads/required_pics/back.png" alt="">
          <p>Go Back</p>
    </a>
    <div class="body">

        <div class="form-container">
            <div class="heading"> <h2>Student Registration</h2></div>
            <form action="" method="POST" enctype="multipart/form-data">
            <label>User ID</label>
            <input type="text" name="user_id" maxlength="6" required placeholder="Enter User ID">

            <label>First Name</label>
            <input type="text" name="fname" maxlength="20" required placeholder="Enter first name">
            
            <label>Last Name</label>
            <input type="text" name="lname" maxlength="20" required placeholder="Enter last name">

            <label>College Roll</label>
            <input type="text" name="clg_roll" maxlength="6" required placeholder="Enter college roll">
            
            <label>Semester</label>
            <select name="sem" required>
                <option value="">-- Select Sem --</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
            </select>
            
            <label>Department</label>
            <input type="text" name="department" maxlength="20" required placeholder="Enter department">

            <label>Phone Number</label>
            <input type="text" name="ph_no" maxlength="10" pattern="\d{10}" required placeholder="Enter phone number">

            <label>Address</label>
            <textarea name="address" maxlength="100" required placeholder="Your address here..."></textarea>
            
            <label>Email</label>
            <input type="email" name="email" maxlength="50" required placeholder="Enter your email address">
            
            <div class="image-upload-container">
            <label for="fileInput">Profile Picture</label>
            <div class="image-box" id="previewBox" onclick="document.getElementById('fileInput').click();">
                <img id="previewImage" alt="Image Preview">
                <span id="previewText" style="color: #aaa; display: block;">Click to upload</span>
            </div>
            <input type="file" id="fileInput" name="pfp" accept="image/*" style="display: none;"> 
        </div>
        
        <input type="submit" value="Submit">
    </form>
    <script>
        const fileInput = document.getElementById('fileInput');
        const previewImage = document.getElementById('previewImage');
        
    fileInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file && file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(e) {
              previewImage.src = e.target.result;
          previewImage.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
</div>
</div>
</body>
</html>