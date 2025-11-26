<?php
require_once __DIR__ . '/db_helper.php';
require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json');

$pdo = get_sqlite();
$user = $_SESSION['user_id'] ?? 'guest';
$stmt = $pdo->prepare("SELECT role, content, ts FROM chats WHERE user_id = ? ORDER BY ts ASC");
$stmt->execute([$user]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['history'=>$rows]);
?>
