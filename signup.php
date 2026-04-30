<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Signup - MMC COLLEGE LIBRARY</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #cbc1f7, #d6f7f6);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        @keyframes fadeInScaleUp {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        @keyframes waveShimmer {
            0% { left: -100%; }
            50% { left: 120%; }
            100% { left: 120%; }
        }

        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 600px;
            height: 500px;
            overflow: hidden;
            animation: fadeInScaleUp 0.7s ease-out forwards;
        }

        .header-wave {
            background: linear-gradient(135deg, #7B60F0, #48D1CC);
            color: white;
            padding: 40px 20px 60px 20px;
            border-bottom-left-radius: 50% 40px;
            border-bottom-right-radius: 50% 40px;
            margin-bottom: 30px;
            position: relative; /* For shimmer */
            overflow: hidden; /* For shimmer */
        }
        
        .header-wave::before { /* Shimmer effect */
            content: '';
            position: absolute;
            top: 0;
            left: -100%; 
            width: 75%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: skewX(-25deg);
            animation: waveShimmer 3.5s infinite 1.2s ease-in-out; /* Delayed start */
        }

        .header-wave h2 {
            margin: 0;
            font-size: 2em; 
            font-weight: 600;
            position: relative; /* To stay above shimmer */
            z-index: 1;
        }

        #signup {
            padding: 0px 30px 30px 30px;
            text-align: center;
        }

        #signup form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        
        #signup div label {
            display: block;
            text-align: left;
            color: #555;
            font-weight: 500;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box; 
            font-size: 1em;
            background-color: #fdfdfd;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #7B60F0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(123, 96, 240, 0.25);
        }
        
        button[type="submit"] {
            background: linear-gradient(to right, #6549d5, #48c1ba);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 500;
            transition: all 0.3s ease; /* Keep 'all' or specify opacity, box-shadow, transform */
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            width: 100%;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            opacity: 0.85;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .home {
            margin-top: 25px;
            font-size: 0.9em;
        }

        .home a {
            color: #7B60F0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease, text-decoration 0.3s ease;
        }

        .home a:hover {
            text-decoration: underline;
            color: #5a3fdb; /* Darken link color on hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-wave">
            <h2>Student Signup</h2>
        </div>
        <div id="signup">
            <form action="signup.php" method="POST">
                <div>
                    <label for="student_id">Student ID:</label>
                    <input type="text" id="student_id" name="student_id" required placeholder="Enter your Student ID">
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password">
                </div>
                <button type="submit">Sign Up</button>
                <div class="home"><a href="index.html">Back to Home</a></div>
            </form>
        </div>
    </div>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "LMS";

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    // Check if USER_ID exists in READERS
    $stmt = $conn->prepare("SELECT * FROM READERS WHERE USER_ID = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Already in LOGIN_READERS?
        $checkLogin = $conn->prepare("SELECT * FROM LOGIN_READERS WHERE LOGIN_ID = ?");
        $checkLogin->bind_param("s", $student_id);
        $checkLogin->execute();
        $loginResult = $checkLogin->get_result();

        if ($loginResult->num_rows > 0) {
            echo "<script>alert('This Student ID is already registered. Please log in.'); window.history.back();</script>";
        } else {
            // Check if a request already exists
            $checkRequest = $conn->prepare("SELECT * FROM SIGNUP_REQUESTS WHERE USER_ID = ? AND STATUS = 'Pending'");
            $checkRequest->bind_param("s", $student_id);
            $checkRequest->execute();
            $requestResult = $checkRequest->get_result();

            if ($requestResult->num_rows > 0) {
                echo "<script>alert('Signup request already sent. Please wait for approval.'); window.history.back();</script>";
            } else {
                // Insert new signup request
                $insertRequest = $conn->prepare("INSERT INTO SIGNUP_REQUESTS (USER_ID, PASSWORD) VALUES (?, ?)");
                $insertRequest->bind_param("ss", $student_id, $password);
                if ($insertRequest->execute()) {
                    echo "<script>alert('Signup request submitted successfully. Wait for librarian approval.'); window.location.href='index.html';</script>";
                } else {
                    echo "<script>alert('Failed to submit request. Please try again.'); window.history.back();</script>";
                }
                $insertRequest->close();
            }
            $checkRequest->close();
        }
        $checkLogin->close();
    } else {
        echo "<script>alert('Your Student ID is not in the college records. Signup rejected.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>