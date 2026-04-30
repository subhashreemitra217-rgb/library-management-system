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
    $isbn = $_POST['ISBN'] ?? null; 
    $title = $_POST['Title'] ?? null;
    $author1 = $_POST['Author_1'] ?? null;
    $author2 = $_POST['Author_2'] ?? null;
    $author3 = $_POST['Author_3'] ?? null;
    $edition = $_POST['Edition'] ?? null;
    $price = $_POST['Price'] ?? null;
    $publisher = $_POST['Publisher'] ?? null;
    $category = $_POST['Category'] ?? null;
    $targetDir = "uploads/book_covers/";
    $imageName = !empty($_FILES["cover_image"]["name"]) ? uniqid() . "_" . basename($_FILES["cover_image"]["name"]) : null;
    $targetFile = $imageName ? $targetDir . $imageName : null;
    $uploadOk = 1;
    $check = $conn->prepare("SELECT Quantity FROM BOOKS WHERE ISBN = ?");
    $check->bind_param("s", $isbn);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE BOOKS SET Quantity = Quantity + 1 WHERE ISBN = ?");
        $updateStmt->bind_param("s", $isbn);
        if ($updateStmt->execute()) {
            echo "<script>
                alert('Book already exists. Quantity increased by 1.');
                if(confirm('Add another book?')) {
                    window.location.href = 'add_book.php';
                } else {
                    window.location.href = 'adminHomePage.php';
                }
            </script>";
        } else {
            echo "<script>alert('⚠️ Error updating book quantity: " . $updateStmt->error . "');</script>";
        }
        $updateStmt->close();
    } else {
        if ($imageName && isset($_FILES["cover_image"]["tmp_name"]) && $_FILES["cover_image"]["size"] > 0) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $targetFile)) {
                echo "<script>alert('⚠️ Failed to upload cover image.');</script>";
                $targetFile = null;
            }
        } else {
            $targetFile = null;
        }

        $insert = $conn->prepare("INSERT INTO BOOKS (ISBN, Title, Author_1, Author_2, Author_3, Edition, Price, Publisher, Category, Quantity, Cover_image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
        $insert->bind_param("ssssssdsss", $isbn, $title, $author1, $author2, $author3, $edition, $price, $publisher, $category, $targetFile);

        if ($insert->execute()) {
            echo "<script>
                alert('✅ New book added successfully.');
                if(confirm('Add another book?')) {
                    window.location.href = 'add_book.php';
                } else {
                    window.location.href = 'adminHomePage.php';
                }
            </script>";
        } else {
            echo "<script>alert('⚠️ Error while adding the book. " . $insert->error . "');</script>";
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
    <title>Add New Book</title>
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
            background: linear-gradient(to right,rgb(249, 200, 226),rgb(177, 182, 245));
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
            background: linear-gradient(to right, #ef5eab, #5c67f2);
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
    <form action="add_book.php" method="POST" enctype="multipart/form-data">
        <h2>Add New Book</h2>
        
        <label for="isbn">ISBN (13 digits)</label>
        <input type="text" id="isbn" name="ISBN" required pattern="\d{13}" placeholder="Enter book ISBN">

        <label for="title">Title</label>
        <input type="text" id="title" name="Title" required placeholder="Enter book title">

        <label for="author1">Author 1</label>
        <input type="text" id="author1" name="Author_1" required placeholder="Enter primary author">

        <label for="author2">Author 2 (Optional)</label>
        <input type="text" id="author2" name="Author_2" placeholder="Enter second author">

        <label for="author3">Author 3 (Optional)</label>
        <input type="text" id="author3" name="Author_3" placeholder="Enter third author">

        <label for="edition">Edition</label>
        <input type="text" id="edition" name="Edition" required placeholder="Edition">

        <label for="price">Price </label>
        <input type="number" id="price" name="Price" step="0.01" required placeholder="price">

        <label for="publisher">Publisher</label>
        <input type="text" id="publisher" name="Publisher" required placeholder="Enter publisher name">

        <label for="category">Category </label>
        <input type="text" id="category" name="Category" required placeholder="Category">
        
        <div class="image-upload-container">
            <label for="fileInput">Cover Image</label>
            <div class="image-box" id="previewBox" onclick="document.getElementById('fileInput').click();">
                <img id="previewImage" alt="Image Preview">
                <span id="previewText" style="color: #aaa; display: block;">Click to upload</span>
            </div>
            <input type="file" id="fileInput" name="cover_image" accept="image/*" style="display: none;"> 
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

</body>
</html>