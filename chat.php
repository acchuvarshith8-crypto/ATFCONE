<?php
require_once __DIR__ . '/db_helper.php';
require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$msg = $input['message'] ?? '';
if (!$msg) { echo json_encode(['reply'=>'No message']); exit; }

$pdo = get_sqlite();
$user = $_SESSION['user_id'] ?? 'guest';

// store user message
$stmt = $pdo->prepare("INSERT INTO chats (user_id, role, content, ts) VALUES (?, 'user', ?, ?)");
$stmt->execute([$user, $msg, time()]);

// proxy to server bridge
$bridge = (isset($_SERVER['HTTP_HOST']) ? ( (isset($_SERVER['HTTPS'])? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] ) : 'http://localhost') . dirname($_SERVER['SCRIPT_NAME']) . '/../server_php_bridge.php';
$ch = curl_init($bridge);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['prompt'=>$msg]));
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) $reply = 'Error contacting backend: ' . $err;
else {
    $decoded = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['reply'])) $reply = $decoded['reply'];
    else if (is_string($response)) $reply = $response;
    else $reply = 'No reply';
}

// save assistant reply
$stmt = $pdo->prepare("INSERT INTO chats (user_id, role, content, ts) VALUES (?, 'assistant', ?, ?)");
$stmt->execute([$user, $reply, time()]);

echo json_encode(['reply'=>$reply]);
?>
