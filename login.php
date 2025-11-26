<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // sanitize to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    // find user
    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {

        // verify password
        if (password_verify($password, $row['password'])) {

            // important security fix
            session_regenerate_id(true);

            // store user session
            $_SESSION['user'] = $row['username'];

            header("Location: index.php");
            exit;
        }
    }

    echo "<script>alert('Invalid username or password'); window.location='login.html';</script>";
}
?>
