<?php include "auth.php"; ?>
<?php
$pickup = htmlspecialchars($_GET['pickup'] ?? '');
$stop1 = htmlspecialchars($_GET['stop1'] ?? '');
$stop2 = htmlspecialchars($_GET['stop2'] ?? '');
$drop   = htmlspecialchars($_GET['drop'] ?? '');
$date   = htmlspecialchars($_GET['date'] ?? '');
$time   = htmlspecialchars($_GET['time'] ?? '');
$booking_type = htmlspecialchars($_GET['booking_type'] ?? 'now');

$vehicles = [
  ['key'=>'regular','title'=>'Regular','rate'=>20,'img'=>'car-regular.jpg','desc'=>'Comfortable city rides'],
  ['key'=>'suv','title'=>'SUV','rate'=>40,'img'=>'car-suv.jpg','desc'=>'Spacious & comfortable'],
  ['key'=>'luxury','title'=>'Luxury Sedan','rate'=>80,'img'=>'car-luxury.jpg','desc'=>'Premium service'],
  ['key'=>'bike','title'=>'Bike','rate'=>18,'img'=>'vehicle-bike.jpg','desc'=>'Fast solo rides'],
  ['key'=>'auto','title'=>'Auto','rate'=>14,'img'=>'vehicle-auto.jpg','desc'=>'Budget short trips'],
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Select Vehicle — ATFC</title>
<style>
/* keep your previous style or paste same CSS from earlier SelectVehicle */
body{font-family:Segoe UI;background:linear-gradient(135deg,#0ba29d,#06796e);color:#fff;padding:28px}
.wrap{max-width:1100px;margin:auto}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px;}
.card{background:rgba(255,255,255,0.12);padding:16px;border-radius:14px;}
.card img{width:100%;height:150px;object-fit:cover;border-radius:12px;}
.select{margin-top:12px;padding:11px 12px;border-radius:12px;border:none;background:linear-gradient(90deg,#27a2ff,#005eff);color:white;font-weight:800;cursor:pointer;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h2>Select Your Vehicle</h2>
    <div class="meta">
      Pickup: <strong><?php echo $pickup;?></strong> • Drop: <strong><?php echo $drop;?></strong> • <?php echo $date;?> <?php echo $time;?>
    </div>
  </div>
  <div class="grid">
    <?php foreach($vehicles as $v): ?>
      <div class="card">
        <img src="assets/<?php echo htmlspecialchars($v['img']);?>" alt="vehicle">
        <div class="title"><?php echo $v['title'];?></div>
        <div class="rate">₹<?php echo $v['rate'];?> / km</div>
        <div style="margin-top:6px;opacity:0.92"><?php echo $v['desc'];?></div>

        <form method="get" action="TripSummary.php" style="margin-top:12px">
          <input type="hidden" name="pickup" value="<?php echo $pickup; ?>">
          <input type="hidden" name="stop1" value="<?php echo $stop1; ?>">
          <input type="hidden" name="stop2" value="<?php echo $stop2; ?>">
          <input type="hidden" name="drop" value="<?php echo $drop; ?>">
          <input type="hidden" name="date" value="<?php echo $date; ?>">
          <input type="hidden" name="time" value="<?php echo $time; ?>">
          <input type="hidden" name="booking_type" value="<?php echo $booking_type; ?>">
          <input type="hidden" name="vehicle" value="<?php echo $v['key']; ?>">
          <input type="hidden" name="rate" value="<?php echo $v['rate']; ?>">
          <button class="select" type="submit">Select</button>
        </form>

      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>

