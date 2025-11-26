<?php
include "auth.php";

$pickup = htmlspecialchars($_GET['pickup'] ?? '');
$drop   = htmlspecialchars($_GET['drop'] ?? '');
$date   = htmlspecialchars($_GET['date'] ?? '');
$time   = htmlspecialchars($_GET['time'] ?? '');
$vehicle = htmlspecialchars($_GET['vehicle'] ?? '');
$rate   = (int)($_GET['rate'] ?? 0);

// temporary estimated distance
$distance = rand(6, 18);
$fare = $distance * $rate;

// driver mapping
$drivers = [
  'regular' => ['name'=>'Ramesh Kumar','mobile'=>'+91 98765 11223','photo'=>'ramesh.jpg','exp'=>'8 yrs', 'rating'=>4.7, 'count'=>132],
  'suv'     => ['name'=>'Vikram Singh','mobile'=>'+91 99887 77666','photo'=>'vikram.jpg','exp'=>'7 yrs', 'rating'=>4.8, 'count'=>201],
  'luxury'  => ['name'=>'Arjun Verma','mobile'=>'+91 90000 55331','photo'=>'arjun.jpg','exp'=>'6 yrs', 'rating'=>4.9, 'count'=>355],
  'bike'    => ['name'=>'Sonia Patel','mobile'=>'+91 91234 55678','photo'=>'sonia.jpg','exp'=>'4 yrs', 'rating'=>4.6, 'count'=>98],
  'auto'    => ['name'=>'Mohammed Shahid','mobile'=>'+91 99333 77889','photo'=>'mohammed.jpg','exp'=>'5 yrs', 'rating'=>4.5, 'count'=>76]
];

$d = $drivers[$vehicle];

function buildStars($rating) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) $stars .= "⭐";
        else if ($rating >= $i - 0.5) $stars .= "✨";
        else $stars .= "☆";
    }
    return $stars;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Trip Summary — ATFC</title>
<style>
body{background:#f4f7fb;font-family:Segoe UI,Arial;margin:0;padding:30px;color:#222}
.card{
  max-width:650px;margin:auto;background:white;border-radius:14px;padding:24px;
  box-shadow:0 6px 20px rgba(0,0,0,0.08);
}
.row{margin:10px 0;font-size:16px}
.btn{
  background:#0078d7;color:#fff;padding:12px;border:none;border-radius:8px;
  cursor:pointer;font-weight:bold;width:100%;margin-top:16px;
}
.driver{
  display:flex;gap:14px;align-items:center;margin-top:12px;
  padding:12px;background:#f9fbff;border-radius:8px;
  border-left:4px solid #0078d7;
}
.driver img{width:75px;height:75px;border-radius:10px;object-fit:cover}
.rating{font-size:18px;margin-top:4px;color:#f5a623}
</style>
</head>
<body>

<div class="card">
<h2>Trip Summary</h2>

<div class="row"><b>Pickup:</b> <?= $pickup ?></div>
<div class="row"><b>Drop:</b> <?= $drop ?></div>
<div class="row"><b>Date:</b> <?= $date ?> at <?= $time ?></div>
<div class="row"><b>Vehicle:</b> <?= ucfirst($vehicle) ?> (₹<?= $rate ?>/km)</div>
<div class="row"><b>Estimated Distance:</b> <?= $distance ?> km</div>
<div class="row"><b>Estimated Fare:</b> ₹<?= $fare ?></div>

<h3>Driver arriving soon</h3>
<div class="driver">
  <img src="drivers/<?= $d['photo'] ?>">
  <div>
    <div><b><?= $d['name'] ?></b></div>
    <div>📞 <?= $d['mobile'] ?></div>
    <div>Experience: <?= $d['exp'] ?></div>
    <div class="rating"><?= buildStars($d['rating']) ?> (<?= $d['rating'] ?> ⭐ | <?= $d['count'] ?> ratings)</div>
  </div>
</div>

<form action="BookingConfirmed.php" method="post">
  <input type="hidden" name="pickup" value="<?= $pickup ?>">
  <input type="hidden" name="drop" value="<?= $drop ?>">
  <input type="hidden" name="date" value="<?= $date ?>">
  <input type="hidden" name="time" value="<?= $time ?>">
  <input type="hidden" name="vehicle" value="<?= $vehicle ?>">

  <input type="hidden" name="rate" value="<?= $rate ?>">
  <input type="hidden" name="est_km" value="<?= $distance ?>">
  <input type="hidden" name="est_fare" value="<?= $fare ?>">

  <!-- ⭐ NEW: pass rating info -->
  <input type="hidden" name="driver_rating" value="<?= $d['rating'] ?>">
  <input type="hidden" name="rating_count" value="<?= $d['count'] ?>">

  <button class="btn" type="submit">Confirm Booking</button>
</form>

</div>

</body>
</html>
