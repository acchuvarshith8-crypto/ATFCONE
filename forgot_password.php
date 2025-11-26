<?php
include "db.php";
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $res = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");
    if ($res && $res->num_rows) {
        $token = bin2hex(random_bytes(3)); // short token for demo
        $expires = date('Y-m-d H:i:s', time() + 15*60);
        $conn->query("UPDATE users SET reset_token='$token', reset_expires='$expires' WHERE email='$email'");
        $msg = "Use this code to reset password (demo only): $token — expires at $expires";
    } else {
        $msg = "Email not found.";
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Forgot Password</title></head><body>
  <h2>Forgot Password</h2>
  <?php if ($msg): ?><p><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <form method="post">
    <input type="email" name="email" placeholder="Your account email" required>
    <button type="submit">Send Reset Code (demo)</button>
  </form>
  <p><a href="reset_password.php">I have a code / Reset password</a></p>
</body></html>
