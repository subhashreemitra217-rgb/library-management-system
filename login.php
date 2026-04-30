<?php
session_start(); // Start session for login tracking

require_once("Connection.php");

// Check if POST variables are set
if (isset($_POST['role'], $_POST['username'], $_POST['password'])) {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role === 'librarian') {
        $stmt = $conn->prepare("SELECT * FROM LIBRARIAN_login Ll,LIBRARIAN L WHERE L.L_ID=Ll.L_ID and Ll.L_ID = ? AND PASSWORD = ?");
        $stmt->bind_param("ss", $username, $password);
    } elseif ($role === 'student') {
        //if not in readers
        $stmt = $conn->prepare("SELECT * FROM READERS WHERE user_ID = ?");
        $stmt->bind_param("s", $username);        
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            echo "<script>alert('⚠️ Invalid User'); window.location.href = 'index.html';</script>";
            exit();
        }
        //if in readers but not in login_readers        
        $stmt = $conn->prepare("SELECT * FROM LOGIN_READERS WHERE LOGIN_ID = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            echo "<script>alert('⚠️ Please Sign up First'); window.location.href = 'signup.php'</script>";
            exit();
        }
        $stmt = $conn->prepare("SELECT * FROM LOGIN_READERS WHERE LOGIN_ID = ? AND PASSWORD = ?");
        $stmt->bind_param("ss", $username, $password);
    } else {
        die("Invalid role.");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
    // Login successful
        $_SESSION['role'] = $role;
        $_SESSION['login_success'] = true; 

    if ($role === 'librarian') {
        $res=mysqli_fetch_assoc($result);
        $_SESSION['user'] = $res['Name'];
        header("Location: adminHomePage.php");
    } else {        
        $_SESSION['username'] = $username;
        header("Location: studentHomePage.php");
    }
    exit;
}
    else {
        echo "<script>alert('❌ Invalid login credentials'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('⚠️ All fields are required'); window.history.back();</script>";
}

$conn->close();
?>
