<?php include "auth.php"; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Book a Ride — ATFC</title>
<style>
:root{--accentA:#0ba29d;--accentB:#c3ec52;--glass:rgba(255,255,255,0.12)}
body{margin:0;font-family:Segoe UI,Arial;background:linear-gradient(135deg,#034954,#056d63);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:28px}
.card{width:100%;max-width:720px;background:var(--glass);padding:26px;border-radius:20px;box-shadow:0 20px 50px rgba(0,0,0,0.22);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.06)}
h1{margin:0 0 8px;color:#fff}
.form-row{display:flex;gap:12px}
.input{width:100%;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.06);color:#fff;outline:none}
.input:focus{box-shadow:0 8px 30px rgba(0,170,255,0.12);border-color:#1db7ff}
.btn{margin-top:14px;padding:12px 16px;border-radius:14px;border:none;font-weight:800;color:#012;background:linear-gradient(90deg,var(--accentA),var(--accentB));cursor:pointer;box-shadow:0 14px 30px rgba(11,162,157,0.18)}
.small{font-size:13px;color:rgba(255,255,255,0.9);margin-top:6px}
@media(max-width:720px){ .form-row{flex-direction:column} }
.schedule-row{display:flex;gap:10px;align-items:center;margin-top:10px}
</style>
</head>
<body>
  <div class="card">
    <h1>Book a Ride</h1>
    <p class="small">Enter pickup and drop, add optional stops, schedule or book now. Payment after ride.</p>

    <!-- Note: auth.php ensures user is logged in for this page -->
    <form action="SelectVehicle.php" method="get" id="bookForm">
      <label style="display:block;margin-top:12px;color:#fff;font-weight:700">Pickup</label>
      <input class="input" name="pickup" required placeholder="e.g., Anna Nagar, Chennai" />

      <label style="display:block;margin-top:12px;color:#fff;font-weight:700">Stop 1 (optional)</label>
      <input class="input" name="stop1" placeholder="Optional stop 1" />

      <label style="display:block;margin-top:12px;color:#fff;font-weight:700">Stop 2 (optional)</label>
      <input class="input" name="stop2" placeholder="Optional stop 2" />

      <label style="display:block;margin-top:12px;color:#fff;font-weight:700">Drop</label>
      <input class="input" name="drop" required placeholder="e.g., Chennai Airport" />

      <div class="form-row" style="margin-top:12px">
        <input class="input" type="date" name="date" required />
        <input class="input" type="time" name="time" required />
      </div>

      <div class="schedule-row">
        <label style="color:#fff;font-weight:700">Booking type:</label>
        <label style="color:#fff"><input type="radio" name="booking_type" value="now" checked> Now</label>
        <label style="color:#fff"><input type="radio" name="booking_type" value="schedule"> Schedule</label>
        <small style="color:#fff;margin-left:8px">If Schedule, use chosen date/time</small>
      </div>

      <button class="btn" type="submit">Next — Select Vehicle</button>
    </form>
  </div>
</body>
</html>
