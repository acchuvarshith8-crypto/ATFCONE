<?php
include "auth.php";
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $user = $_SESSION['user'] ?? null;
    if ($id && $rating >=1 && $rating <=5 && $user) {
        $stmt = $conn->prepare("UPDATE bookings SET rating=? WHERE id=? AND user=?");
        $stmt->bind_param("iis", $rating, $id, $user);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: BookingConfirmed.php?id=".$id);
    exit;
}
?>
