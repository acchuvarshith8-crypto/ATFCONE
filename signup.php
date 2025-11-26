<?php
include "db.php";

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users(fullname,email,username,password)
        VALUES('$fullname','$email','$username','$password')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Signup Successful! Login Now.'); window.location='login.html';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
