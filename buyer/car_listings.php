<?php
require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

/* FILTERS */
$q = trim($_GET['q'] ?? '');
$brand = trim($_GET['brand'] ?? '');
$min = (int)($_GET['min'] ?? 0);
$max = (int)($_GET['max'] ?? 0);

/* QUERY BUILD */
$sql = "SELECT id,make,model,year,price,image_path FROM car_listings WHERE status='approved'";
$params = [];
$types = "";

/* SEARCH */
if($q){
  $sql .= " AND (make LIKE ? OR model LIKE ?)";
  $types .= "ss";
  $like = "%$q%";
  $params[] = $like;
  $params[] = $like;
}

/* BRAND */
if($brand){
  $sql .= " AND make=?";
  $types .= "s";
  $params[] = $brand;
}

/* PRICE */
if($min > 0){
  $sql .= " AND price >= ?";
  $types .= "i";
  $params[] = $min;
}

if($max > 0){
  $sql .= " AND price <= ?";
  $types .= "i";
  $params[] = $max;
}

$sql .= " ORDER BY created_at DESC";

/* EXECUTE */
$stmt = mysqli_prepare($conn,$sql);
if($params){
  mysqli_stmt_bind_param($stmt,$types,...$params);
}
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<h1>🚗 Browse Cars</h1>

<!-- FILTER FORM -->

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">

<input class="input" name="q" placeholder="Search brand/model" value="<?php echo e($q); ?>">

<select class="input" name="brand">
<option value="">All Brands</option>
<?php
$brands = ["Maruti","Tata","Mahindra","Hyundai","Honda","Toyota","Kia","BMW","Audi"];
foreach($brands as $b){
$sel = ($brand==$b)?"selected":"";
echo "<option $sel>$b</option>";
}
?>
</select>

<input class="input" name="min" type="number" placeholder="Min Price" value="<?php echo $min ?: ''; ?>">
<input class="input" name="max" type="number" placeholder="Max Price" value="<?php echo $max ?: ''; ?>">

<button class="btn primary">Filter</button>
<a class="btn" href="car_listings.php">Reset</a>

</form>

<!-- CAR GRID -->

<div class="grid">

<?php if(mysqli_num_rows($res)>0): ?>

<?php while($c=mysqli_fetch_assoc($res)): 
$img = $c['image_path'] ?: "/carconnect/assets/images/default_car.jpg";
?>

<div class="card">

<img src="<?php echo e($img); ?>" style="height:200px;object-fit:cover">

<div class="p">

<div style="font-weight:800;font-size:18px">
<?php echo e($c['make'])." ".e($c['model']); ?>
</div>

<div class="muted">
<?php echo e($c['year']); ?>
</div>

<div style="margin-top:8px;color:#00d2ff;font-weight:700">
₹<?php echo number_format((float)$c['price']); ?>
</div>

<div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">

<a class="btn primary"
href="car_details.php?id=<?php echo intval($c['id']); ?>">
View
</a>

<a class="btn"
href="wishlist.php?add=<?php echo intval($c['id']); ?>">
❤️ Wishlist
</a>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div style="text-align:center;width:100%">

<p class="muted" style="font-size:18px">
No cars found 🚗
</p>

<p class="muted">
Try changing filters or search again.
</p>

</div>

<?php endif; ?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>