<?php
  session_start();
  if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: index.html");
    exit();
  }    
  require_once("Connection.php");
  
  if($_SERVER['REQUEST_METHOD']=="POST" &&  isset($_POST['Edit'])) {
    $isbn=$_POST['isbn'];
    $editQuery = "SELECT * from BOOKS where ISBN = '" . $isbn ."';";
    $editresult = mysqli_query($conn, $editQuery);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
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
    <?php    
        if (mysqli_num_rows($editresult) > 0) {            
            $book=mysqli_fetch_assoc($editresult);?>
            <form action="" method="POST" enctype="multipart/form-data">
                <h2>Edit Book</h2>
                <input type="hidden" name="ISBN" value="<?php echo $isbn;?>" pattern="\d{13}">
                
                <label>Title</label>
                <input type="text" name="Title" value="<?php echo $book['Title'];?>" required>
                
                <label>Quantity</label>
                <input type="number" name="quantity" min="0" value="<?php echo $book['Quantity'];?>" required>
                
                <label>Author 1</label>
                <input type="text" name="Author_1" value="<?php echo $book['Author_1'];?>" required >
                
                <label>Author 2</label>
                <input type="text" name="Author_2" value="<?php echo $book['Author_2'];?>" >
                
                <label>Author 3</label>
                <input type="text" name="Author_3" value="<?php echo $book['Author_3'];?>" >
                
                <label>Edition</label>
                <input type="text" name="Edition" value="<?php echo $book['Edition'];?>" >
                
                <label>Price</label>
                <input type="number" name="Price" value="<?php echo $book['Price'];?>" min="10" step="0.01" required>
                
                <label>Publisher</label>
                <input type="text" name="Publisher" value="<?php echo $book['Publisher'];?>" >
                
                <label>Category</label>
                <input type="text" name="Category" value="<?php echo $book['Category'];?>" required>
                
                <div class="image-upload-container">
                <label for="fileInput">Cover Image</label>
                <div class="image-box" id="previewBox" onclick="document.getElementById('fileInput').click();">
                    <img id="previewImage" alt="Image Preview">
                    <span id="previewText" style="color: #aaa; display: block;">Click to upload</span>
                </div>
                <input type="file" id="fileInput" name="cover_image" accept="image/*" style="display: none;"> 
                </div>

                <input type="submit" value="Edit Book" name="EditBook">
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
            <?php
        }else {
            echo "<script>
            alert('⚠️ Some Error Occured...');
            window.location.href = 'adminHomePage.php';
            </script>";      
        }
    }?>
</body>
</html>
<?php

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['EditBook'])) {
    
        $isbn = $_POST['ISBN'];
        $title = $_POST['Title'];
        $author1 = $_POST['Author_1'];
        $author2 = $_POST['Author_2'] ?? null;
        $author3 = $_POST['Author_3'] ?? null;
        $edition = $_POST['Edition'] ?? null;
        $price = $_POST['Price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $publisher = $_POST['Publisher'] ?? null;
        $category = $_POST['Category'] ?? null;

        //Fetch Cover_Image_Path 
        $targetDir = "uploads/book_covers/";
        $imageName = !empty($_FILES["cover_image"]["name"]) ? basename($_FILES["cover_image"]["name"]) : null;
        $targetFile = $targetDir . $imageName;

        // UPDATE book        
        try {
            if ($imageName) {
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $targetFile)) {
                    $update = $conn->prepare("UPDATE BOOKS SET Title = ?, Author_1 = ?, 
                        Author_2 = ?, Author_3 = ?, Edition = ?, Price = ?, Publisher = ?, 
                        Category = ?, Quantity = ?, Cover_image_path = ? WHERE ISBN = ?");
                    $update->bind_param("sssssssssss", $title, $author1,$author2,
                        $author3,$edition,$price,$publisher,$category,$quantity,$targetFile,$isbn);
                }
                else {
                    echo "<script>
                        alert('⚠️ Failed to upload cover image');
                        window.location.href = 'adminHomePage.php';
                        </script>"; 
                }                 
            } else {
                $update = $conn->prepare("UPDATE BOOKS SET Title = ?, Author_1 = ?, 
                    Author_2 = ?, Author_3 = ?, Edition = ?, Price = ?, Publisher = ?, 
                    Category = ?, Quantity = ? WHERE ISBN = ?");
                $update->bind_param("ssssssssss", $title, $author1,$author2,
                    $author3,$edition,$price,$publisher,$category,$quantity,$isbn);    
            }    
            
            if ($update->execute()) {
                echo "<script>
                    alert('✅ Book Edited successfully.');
                    window.location.href = 'adminHomePage.php';
                    </script>";
            } else {              
                echo "<script>
                    alert('⚠️ Failed to Edit Book');
                    window.location.href = 'adminHomePage.php';
                    </script>";      
            }
        } catch(mysqli_sql_exception $e) {               
            echo "<script>
                alert('⚠️ Some Error Occured...');
                window.location.href = 'adminHomePage.php';
                </script>";      
        }
    }
    mysqli_close($conn);
?>