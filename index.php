<?php 
require_once __DIR__ . "/includes/header.php"; 
require_once __DIR__ . "/includes/db_connect.php"; 
require_once __DIR__ . "/includes/functions.php";
?>

<!-- ===================== -->
<!-- HERO SECTION -->
<!-- ===================== -->

<section class="hero">

<div>

<span class="badge">🚗 India’s Trusted Used Car Marketplace</span>

<h1>Buy & Sell Cars Easily</h1>

<p>
Discover verified listings from trusted sellers. 
Find your dream car or sell your vehicle quickly with zero middlemen.
</p>

<div style="display:flex;gap:15px;margin-top:20px;flex-wrap:wrap">

<a class="btn primary" href="/carconnect/buyer/car_listings.php">
Browse Cars
</a>

<a class="btn" href="/carconnect/seller/register.php">
Sell Your Car
</a>

</div>

</div>

<div class="card">

<img 
src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=900&q=80"
style="height:260px;object-fit:cover"
>

</div>

</section>


<!-- ===================== -->
<!-- BROWSE BY BRAND -->
<!-- ===================== -->

<section style="margin-top:60px">

<h2 style="text-align:center;margin-bottom:30px">
Browse Cars by Brand
</h2>

<div class="grid" style="grid-template-columns:repeat(6,1fr)">

<?php

$brands = [

["Maruti","https://logo.clearbit.com/marutisuzuki.com"],

["Tata","https://logo.clearbit.com/tatamotors.com"],

["Mahindra","https://logo.clearbit.com/mahindra.com"],

["Hyundai","https://logo.clearbit.com/hyundai.com"],

["Honda","https://logo.clearbit.com/honda.com"],

["Toyota","https://logo.clearbit.com/toyota.com"]

];

foreach($brands as $brand){

$name = $brand[0];
$logo = $brand[1];

echo '

<a href="/carconnect/buyer/car_listings.php?brand='.$name.'" 
class="card" 
style="text-align:center;padding:25px">

<img src="'.$logo.'" 
style="height:60px;width:auto;object-fit:contain;margin:auto">

<div class="p">

<div style="font-weight:700;margin-top:10px">
'.$name.'
</div>

</div>

</a>

';

}

?>

</div>

</section>


<!-- ===================== -->
<!-- FEATURED CARS -->
<!-- ===================== -->

<section style="margin-top:70px">

<h2 style="text-align:center;margin-bottom:30px">
Latest Cars
</h2>

<div class="grid">

<?php

$sql = "
SELECT id, make, model, year, price, mileage, fuel_type, transmission, image_path
FROM car_listings
WHERE status='approved'
ORDER BY created_at DESC
LIMIT 8
";

$res = mysqli_query($conn,$sql);

if(mysqli_num_rows($res) > 0){

while($c = mysqli_fetch_assoc($res)){

$img = $c['image_path'] 
? $c['image_path'] 
: "/carconnect/assets/images/default_car.jpg";

echo '

<div class="card">

<img src="'.$img.'" style="height:200px;object-fit:cover">

<div class="p">

<div style="font-weight:800;font-size:18px">
'.$c['make'].' '.$c['model'].'
</div>

<div class="muted">
'.$c['year'].'
</div>

<div style="margin-top:8px;font-weight:700;color:#00d2ff">
₹'.number_format($c['price']).'
</div>

<div style="margin-top:10px;font-size:13px;color:#aaa">

Mileage: '.$c['mileage'].' km  
<br>
Fuel: '.$c['fuel_type'].'  
<br>
Transmission: '.$c['transmission'].'

</div>

<div style="margin-top:12px">

<a class="btn primary"
href="/carconnect/buyer/car_details.php?id='.$c['id'].'">
View Details
</a>

</div>

</div>

</div>

';

}

}else{

echo '

<div style="text-align:center;width:100%">

<p class="muted" style="font-size:18px">
🚗 No cars available yet.
</p>

<p class="muted">
Cars will appear here once sellers add listings.
</p>

</div>

';

}

?>

</div>

</section>


<!-- ===================== -->
<!-- WHY CHOOSE US -->
<!-- ===================== -->

<section style="margin-top:80px">

<h2 style="text-align:center;margin-bottom:30px">
Why Choose Online Car Connect
</h2>

<div class="grid" style="grid-template-columns:repeat(3,1fr)">

<div class="card">

<div class="p">

<h3>🔒 Secure Transactions</h3>

<p class="muted">
Safe login system and protected transactions.
</p>

</div>

</div>


<div class="card">

<div class="p">

<h3>📊 Transparent Pricing</h3>

<p class="muted">
No hidden charges or middlemen involved.
</p>

</div>

</div>


<div class="card">

<div class="p">

<h3>⚡ Fast Selling</h3>

<p class="muted">
List your car and sell it quickly across India.
</p>

</div>

</div>

</div>

</section>


<?php require_once __DIR__ . "/includes/footer.php"; ?>