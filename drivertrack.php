<?php
include "auth.php";
include "db.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$booking = null;
if ($id) {
  $res = $conn->query("SELECT * FROM bookings WHERE id=$id LIMIT 1");
  $booking = $res->fetch_assoc();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Track Driver — ATFC</title>
<style>
body{margin:0;font-family:Segoe UI;background:linear-gradient(135deg,#034954,#056d63);color:#fff;padding:28px}
.wrap{max-width:920px;margin:auto}
.card{background:rgba(255,255,255,0.12);padding:20px;border-radius:16px;backdrop-filter:blur(12px);box-shadow:0 12px 28px rgba(0,0,0,0.18)}
.btn{padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:800;background:linear-gradient(90deg,#27a2ff,#005eff);color:#fff;margin-right:10px}
.small{background:#fff;color:#063;padding:8px 12px;border-radius:8px;border:none}
#map{width:100%;height:360px;border-radius:12px;background:linear-gradient(90deg,#e6f7ff,#d9f3ff);position:relative;overflow:hidden}
.route{position:absolute;left:20px;right:20px;top:180px;height:6px;background:#c5e8ff;border-radius:6px}
.car{width:40px;height:40px;border-radius:50%;background:#ffdd57;display:flex;align-items:center;justify-content:center;border:3px solid #333;position:absolute;left:10px;top:160px;font-size:20px}
.profileBox{position:absolute;right:18px;top:18px;background:white;color:#063;padding:10px;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,0.12)}
.modal{position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;color:#111;padding:18px;border-radius:12px;box-shadow:0 16px 50px rgba(0,0,0,0.3);display:none;z-index:2000}
.modal input[type=number]{width:60px}
</style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <?php if (!$booking): ?>
        <h3>No active booking</h3>
        <p><a href="BookCab.php" style="color:#fff">Create a booking</a></p>
      <?php else: ?>
        <h2>Tracking — Booking <?= htmlspecialchars($booking['booking_id']) ?></h2>
        <p><b>Driver:</b> <?= htmlspecialchars($booking['driver_name']) ?> — <?= htmlspecialchars($booking['driver_mobile']) ?></p>

        <div id="map">
          <div class="route"></div>
          <div id="car" class="car">🚕</div>

          <div class="profileBox">
            <img src="<?= htmlspecialchars($booking['driver_photo']) ?>" style="width:48px;height:48px;border-radius:8px;vertical-align:middle;margin-right:8px">
            <div style="display:inline-block;vertical-align:middle">
              <div style="font-weight:800"><?= htmlspecialchars($booking['driver_name']) ?></div>
              <div style="font-size:12px"><?= htmlspecialchars($booking['driver_experience']) ?> experience</div>
              <div style="margin-top:6px"><button class="small" onclick="showDriverProfile()">View</button></div>
            </div>
          </div>
        </div>

        <p style="margin-top:14px">ETA: <span id="eta"><?= intval($booking['eta_minutes'] ?? 7) ?></span> min</p>

        <div style="margin-top:12px">
          <button class="btn" onclick="location.href='BookingConfirmed.php?id=<?= $booking['id'] ?>'">Back</button>
          <button class="btn" onclick="location.href='index.php'">Home</button>
          <button class="btn" onclick="location.href='cancel.php?id=<?= $booking['id'] ?>'">Cancel</button>
          <button class="btn" onclick="openRate(<?= $booking['id'] ?>)">Rate Driver</button>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Driver Profile Modal -->
  <div id="driverModal" class="modal">
    <h3><?= htmlspecialchars($booking['driver_name'] ?? '') ?></h3>
    <img src="<?= htmlspecialchars($booking['driver_photo'] ?? 'assets/default-driver.jpg') ?>" style="width:120px;height:120px;border-radius:12px;display:block;margin-bottom:12px">
    <div><b>Experience:</b> <?= htmlspecialchars($booking['driver_experience'] ?? '') ?></div>
    <div><b>Phone:</b> <?= htmlspecialchars($booking['driver_mobile'] ?? '') ?></div>
    <div style="margin-top:8px"><b>Vehicle:</b> <?= htmlspecialchars($booking['cab_type'] ?? '') ?></div>
    <div style="margin-top:12px"><button onclick="closeDriverProfile()">Close</button></div>
  </div>

  <!-- Rating Modal -->
  <div id="rateModal" class="modal">
    <h3>Rate your driver</h3>
    <form method="post" action="rate.php">
      <input type="hidden" name="id" id="rateBookingId" value="">
      <div style="margin-top:10px">
        <label>Rating (1-5):</label>
        <input type="number" name="rating" min="1" max="5" required>
      </div>
      <div style="margin-top:12px">
        <button type="submit">Submit</button>
        <button type="button" onclick="closeRate()">Cancel</button>
      </div>
    </form>
  </div>

<script>
let car = document.getElementById('car');
let pos = 10;
let eta = parseInt(document.getElementById('eta').innerText || '7', 10);

// move car across route (fake)
const max = 520;
const interval = setInterval(()=>{
  pos += 4;
  if (pos > max) { pos = max; clearInterval(interval); onArrive(); }
  car.style.left = pos + 'px';
  eta -= 0.03;
  document.getElementById('eta').innerText = Math.max(0, Math.ceil(eta));
}, 300);

function onArrive(){
  // show rating prompt after arrival
  setTimeout(()=> openRate(<?= intval($booking['id'] ?? 0) ?>), 600);
}

function showDriverProfile(){
  document.getElementById('driverModal').style.display = 'block';
}
function closeDriverProfile(){
  document.getElementById('driverModal').style.display = 'none';
}

function openRate(id){
  document.getElementById('rateBookingId').value = id;
  document.getElementById('rateModal').style.display = 'block';
}
function closeRate(){ document.getElementById('rateModal').style.display = 'none'; }
</script>
</body>
</html>
