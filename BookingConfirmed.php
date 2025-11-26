<?php
include "auth.php";
include "db.php";

function gen_booking_id(){ 
    return 'ATFC'.strtoupper(bin2hex(random_bytes(4))); 
}

// ⭐ RATING STAR FUNCTION
function render_stars($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - ($full + $half);

    $html = "";
    for ($i = 0; $i < $full; $i++) $html .= "<span style='color:gold;font-size:20px'>★</span>";
    if ($half) $html .= "<span style='color:gold;font-size:20px'>⯨</span>";  // half star
    for ($i = 0; $i < $empty; $i++) $html .= "<span style='color:#999;font-size:20px'>★</span>";
    return $html;
}

// HANDLE INSERT WHEN COMING FROM TRIPSUMMARY
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pickup = $_POST['pickup'] ?? '';
    $drop = $_POST['drop'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $vehicle = $_POST['vehicle'] ?? '';

    $rate = (int)($_POST['rate'] ?? 0);
    $est_km = floatval($_POST['est_km'] ?? 0);
    $est_fare = floatval($_POST['est_fare'] ?? 0);

    // DRIVER DATA (WITH RATING)
    $driverMap = [
        'regular' => ['name'=>'Ramesh Kumar','mobile'=>'+91 98765 11223','photo'=>'drivers/ramesh.jpg','exp'=>'8 yrs','rating'=>4.6,'ratings_count'=>122],
        'suv'     => ['name'=>'Vikram Singh','mobile'=>'+91 99887 77666','photo'=>'drivers/vikram.jpg','exp'=>'7 yrs','rating'=>4.8,'ratings_count'=>188],
        'luxury'  => ['name'=>'Arjun Verma','mobile'=>'+91 90000 55331','photo'=>'drivers/arjun.jpg','exp'=>'6 yrs','rating'=>4.9,'ratings_count'=>201],
        'bike'    => ['name'=>'Sonia Patel','mobile'=>'+91 91234 55678','photo'=>'drivers/sonia.jpg','exp'=>'4 yrs','rating'=>4.4,'ratings_count'=>93],
        'auto'    => ['name'=>'Mohammed Shahid','mobile'=>'+91 99333 77889','photo'=>'drivers/mohammed.jpg','exp'=>'5 yrs','rating'=>4.3,'ratings_count'=>71]
    ];

    $driver = $driverMap[$vehicle];

    $booking_id = gen_booking_id();
    $user = $_SESSION['user'] ?? $_SESSION['user_id'] ?? null;
    $status = 'confirmed';

    // INSERT INTO DATABASE
    $stmt = $conn->prepare("INSERT INTO bookings 
    (user, booking_id, pickup, drop_loc, trip_date, trip_time, cab_type, rate_per_km, est_distance_km, est_fare, 
    driver_name, driver_mobile, driver_photo, driver_experience, driver_rating, rating_count, status)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

   $stmt->bind_param(
    "sssssssddsssssdis",
    $user, $booking_id, $pickup, $drop, $date, $time,
    $vehicle, $rate, $est_km, $est_fare,
    $driver['name'], $driver['mobile'], $driver['photo'], $driver['exp'],
    $driver['rating'], $driver['ratings_count'], $status
);

    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    header("Location: BookingConfirmed.php?id=".$id);
    exit;
}

// DISPLAY BOOKING DETAILS
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$booking = null;
if ($id) {
    $res = $conn->query("SELECT * FROM bookings WHERE id=$id LIMIT 1");
    $booking = $res->fetch_assoc();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Booking Confirmed — ATFC</title>
<style>
body{
  margin:0;font-family:Segoe UI,Arial;
  background:linear-gradient(135deg,#034954,#056d63);
  color:#fff;padding:28px
}
.wrap{max-width:980px;margin:auto}
.card{
  background:rgba(255,255,255,0.12);
  padding:22px;border-radius:18px;
  backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,0.05);
  box-shadow:0 12px 30px rgba(0,0,0,0.25)
}
.driver{display:flex;gap:18px;align-items:center;margin-top:10px}
.driver img{
  width:140px;height:140px;border-radius:14px;object-fit:cover;
  box-shadow:0 4px 20px rgba(0,0,0,0.25)
}
.btn{
  padding:12px 16px;border-radius:12px;border:none;
  cursor:pointer;font-weight:800;font-size:14px
}
.track{background:linear-gradient(90deg,#27a2ff,#005eff);color:#fff}
.again{background:linear-gradient(90deg,#28a745,#1ea34a);color:#fff}
.assistant{
  background:rgba(255,255,255,0.92);color:#063;
  padding:12px;border-radius:12px;font-weight:700
}
</style>
</head>
<body>
<div class="wrap">
<div class="card">

<?php if (!$booking): ?>

  <h2>No booking found</h2>
  <p><a style="color:white" href="BookCab.php">Book a ride</a></p>

<?php else: ?>

  <h2>✅ Booking Confirmed</h2>

  <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?></p>

  <div class="driver">
    <img src="<?php echo $booking['driver_photo']; ?>" 
         onerror="this.src='assets/default-driver.jpg'">

    <div class="info">
      <h3 style="margin:0"><?php echo $booking['driver_name']; ?></h3>

      <p>Experience: <?php echo $booking['driver_experience']; ?></p>
      <p>Contact: <?php echo $booking['driver_mobile']; ?></p>

      <!-- ⭐ DRIVER RATING -->
      <p>
        Rating: <?php echo render_stars($booking['driver_rating']); ?>
        (<?php echo $booking['rating_count']; ?> reviews)
      </p>

      <p>ETA: <strong id="eta">7 min</strong></p>
    </div>
  </div>

  <div style="margin-top:16px">
    <p><strong>Pickup:</strong> <?php echo $booking['pickup']; ?></p>
    <p><strong>Drop:</strong> <?php echo $booking['drop_loc']; ?></p>
    <p><strong>Vehicle:</strong> <?php echo ucfirst($booking['cab_type']); ?> (₹<?php echo $booking['rate_per_km']; ?>/km)</p>
    <p><strong>Estimated Distance:</strong> <?php echo $booking['est_distance_km']; ?> km</p>
    <p><strong>Estimated Fare:</strong> ₹<?php echo $booking['est_fare']; ?> — pay later</p>
  </div>

  <div style="margin-top:18px;display:flex;gap:12px;flex-wrap:wrap">
    <button class="btn track" onclick="location.href='drivertrack.php?id=<?php echo $booking['id']; ?>'">Track Driver</button>
    <button class="btn again" onclick="location.href='BookCab.php'">Book Another Ride</button>
    <button class="assistant" onclick="location.href='AIAssistant.php?booking_id=<?php echo $booking['id'];?>'">Ask Assistant</button>
    <button class="assistant" onclick="location.href='index.php'">🏠 Home</button>
  </div>

<?php endif; ?>

</div>
</div>

<script>
let sec = 7*60;
const etaEl = document.getElementById('eta');

setInterval(() => {
    sec--;
    const mm = Math.floor(sec/60);
    etaEl.textContent = mm > 0 ? mm+' min' : sec+' sec';
}, 1000);
</script>

</body>
</html>
