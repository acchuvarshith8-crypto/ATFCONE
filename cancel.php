<?php
include "auth.php";
include "db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = $_SESSION['user'] ?? null;

if ($id && $user) {
    $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user=?");
    $stmt->bind_param("is", $id, $user);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php");
exit;
