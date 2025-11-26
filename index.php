<?php
session_start();
$isLoggedIn = isset($_SESSION['user']) && $_SESSION['user'];
include "db.php";
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>ATFC — Home</title>

<style>
:root{
  --card-bg: rgba(255,255,255,0.88);
  --glass: rgba(255,255,255,0.65);
  --accent:#0ba29d;
  --accent-2:#3dbb9c;
  --muted:#6b7280;
  --shadow: 0 12px 40px rgba(6,22,25,0.12);
}

/* Background */
body{
  margin:0;
  font-family:Inter, "Segoe UI", Arial, sans-serif;
  background: url('assets/hero-bg.jpg') center/cover no-repeat fixed;
  color:#072b2a;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding-top:55px;
}

/* Banner */
.scroll-banner{
  position:fixed;top:0;left:0;width:100%;height:45px;
  background:rgba(255,255,255,0.45);
  backdrop-filter:blur(8px);
  display:flex;align-items:center;overflow:hidden;
  border-bottom:1px solid rgba(0,0,0,0.08);
  z-index:999;
}
.scroll-text{
  white-space:nowrap;
  font-weight:700;font-size:15px;color:#01302f;
  animation: scroll-left 18s linear infinite;
  padding-left:100%;
}
@keyframes scroll-left{from{transform:translateX(0);}to{transform:translateX(-100%);}}

/* Overlay */
.overlay{
  position:fixed;inset:0;
  background:linear-gradient(180deg,rgba(255,255,255,0.18),rgba(255,255,255,0.22));
  pointer-events:none;
}

/* Layout */
.container{
  width:100%;max-width:1200px;padding:36px;
  display:grid;grid-template-columns:1fr 420px;gap:32px;
  align-items:start;
}

/* Hero Section */
.hero{
  padding:42px;border-radius:18px;
  background:linear-gradient(180deg,rgba(255,255,255,0.75),rgba(255,255,255,0.82));
  box-shadow:var(--shadow);
  border:1px solid rgba(10,10,10,0.03);
}
.kicker{font-weight:700;color:var(--muted);margin-bottom:8px}
.title{font-size:40px;margin:0 0 10px;font-weight:800;color:#01302f}
.lead{margin:0 0 18px;color:#214b49}

/* Right Quick Book Card */
.bookcard{
  background:var(--card-bg);border-radius:16px;
  padding:18px;box-shadow:var(--shadow);
}
.input{
  width:100%;padding:10px;border-radius:10px;
  border:1px solid rgba(8,18,17,0.06);font-size:14px;
}
.primary{
  background:linear-gradient(90deg,var(--accent),var(--accent-2));
  color:white;padding:12px 14px;border-radius:12px;
  border:none;font-weight:800;cursor:pointer;
}

/* Top-Right Buttons */
.myBtn{
  position:fixed;
  top:10px;right:10px;
  background:white;
  padding:10px 16px;
  border-radius:12px;
  font-weight:800;
  cursor:pointer;
  box-shadow:0 4px 12px rgba(0,0,0,0.15);
  z-index:1500;
}

/* MyBookings dropdown */
#myPanel{
  position:fixed;
  top:60px;right:10px;
  width:300px;
  background:white;
  border-radius:12px;
  padding:16px;
  box-shadow:0 6px 20px rgba(0,0,0,0.25);
  display:none;
  z-index:1500;
}
.bookingItem{
  background:#f4f4f4;
  padding:12px;border-radius:10px;margin-top:10px;
}

/* Buttons inside dropdown */
.trackBtn{
  padding:6px 10px;border:none;border-radius:8px;
  background:#0078d7;color:white;font-weight:bold;cursor:pointer;
}
.payBtn{
  padding:6px 10px;border:none;border-radius:8px;
  background:#28a745;color:white;font-weight:bold;cursor:pointer;
}
.cancelBtn{
  padding:6px 10px;border:none;border-radius:8px;
  background:#ff3b30;color:white;font-weight:bold;cursor:pointer;margin-left:6px;
}
.rateBtn{
  padding:6px 10px;border:none;border-radius:8px;
  background:#ffcc00;color:black;font-weight:bold;cursor:pointer;margin-left:6px;
}
</style>
</head>

<body>

<!-- Top-right dynamic button -->
<?php if (!$isLoggedIn): ?>
    <div class="myBtn" onclick="location.href='login.html'">Sign In</div>
<?php else: ?>
    <div class="myBtn" onclick="toggleBookings()">MyBookings</div>
<?php endif; ?>

<!-- MyBookings Panel -->
<div id="myPanel">

  <h3>Your Bookings</h3>

  <!-- Logout -->
  <?php if ($isLoggedIn): ?>
    <button onclick="location.href='logout.php'"
      style="background:#ff3b30;color:white;padding:8px 12px;border:none;
             border-radius:8px;cursor:pointer;font-weight:bold;margin-bottom:10px;width:100%;">
      Logout
    </button>
  <?php endif; ?>

  <?php
  $user = $_SESSION['user'] ?? null;
  $book = [];

  if ($user){
      $q = $conn->query("SELECT * FROM bookings WHERE user='$user' ORDER BY id DESC LIMIT 5");
      while($r=$q->fetch_assoc()) $book[] = $r;
  }
  ?>

  <?php if (!$user): ?>
      <p>Please login to view bookings.</p>

  <?php elseif (count($book)==0): ?>
      <p>No active bookings.</p>

  <?php else: ?>
      <?php foreach($book as $b): ?>
        <div class="bookingItem">
          <b><?= htmlspecialchars($b['pickup']) ?></b> → 
          <b><?= htmlspecialchars($b['drop_loc']) ?></b><br>

          <?= htmlspecialchars($b['trip_date']) ?> at <?= htmlspecialchars($b['trip_time']) ?><br>
          <b>Status:</b> <?= htmlspecialchars($b['status'] ?? 'confirmed') ?><br>
          Fare: ₹<?= htmlspecialchars($b['est_fare']) ?><br><br>

          <button class="trackBtn" onclick="location.href='drivertrack.php?id=<?= $b['id'] ?>'">Track</button>
          <button class="payBtn" onclick="alert('Online payment coming soon!')">Pay Now</button>

          <?php if (($b['status'] ?? '') !== 'cancelled'): ?>
            <button class="cancelBtn" onclick="location.href='cancel.php?id=<?= $b['id'] ?>'">Cancel</button>
          <?php endif; ?>

          <?php if (empty($b['rating'])): ?>
            <button class="rateBtn" onclick="location.href='drivertrack.php?id=<?= $b['id'] ?>'">Rate</button>
          <?php else: ?>
            <span style="margin-left:8px;color:#333">Rated <?= $b['rating'] ?>/5</span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
function toggleBookings(){
  let p=document.getElementById('myPanel');
  p.style.display = (p.style.display==='block') ? 'none' : 'block';
}
</script>

<!-- Banner -->
<div class="scroll-banner">
  <div class="scroll-text">
    🚖 ATFC Premium Rides • Verified Drivers • Pay After Trip • Smart AI Assistant • 24/7 Support •
  </div>
</div>

<div class="overlay"></div>

<!-- Main container -->
<div class="container">

  <!-- Hero Section -->
  <div class="hero">
    <div class="kicker">ATFC • Premium Rides</div>
    <h1 class="title">Ride in comfort — anywhere, anytime</h1>
    <p class="lead">Verified drivers, upfront fares & instant booking.</p>

    <button class="primary" onclick="location.href='AIAssistant.php'">Ask Assistant</button>
  </div>

  <!-- Booking Card -->
  <div class="bookcard">
    <h3>Quick Book</h3>
    <form action="SelectVehicle.php" method="get">
      <label>Pickup</label>
      <input name="pickup" class="input" required>

      <label>Drop</label>
      <input name="drop" class="input" required>

      <div style="display:flex;gap:8px;margin-top:10px">
        <input type="date" name="date" class="input" required>
        <input type="time" name="time" class="input" required>
      </div>

      <button class="primary" type="submit" style="margin-top:12px">Select Vehicle</button>
    </form>
  </div>

</div>

</body>
</html>
