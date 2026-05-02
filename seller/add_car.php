<?php
require_once __DIR__ . "/../core/middleware.php";
requireRole("seller");

require_once __DIR__ . "/../includes/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/header.php";

$sellerId = (int) $_SESSION['user_id'];
$err = "";
$ok = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $brand        = trim($_POST['brand'] ?? '');
    $model        = trim($_POST['model'] ?? '');
    $year         = (int) ($_POST['year'] ?? 0);
    $price        = (float) ($_POST['price'] ?? 0);
    $mileage      = (int) ($_POST['mileage'] ?? 0);
    $fuelType     = trim($_POST['fuel_type'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $category_id  = (int) ($_POST['category_id'] ?? 0); // ✅ NEW

    if ($brand === "" || $model === "" || $year <= 0 || $price <= 0 || $category_id <= 0) {
        $err = "Please fill all required fields properly.";
    } else {

        $imagePath = "/carconnect/assets/images/default_car.jpg";

        /* IMAGE UPLOAD */
        if (!empty($_FILES['image']['name'])) {

            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ["jpg","jpeg","png","webp"];

                if (!in_array($ext,$allowed)) {
                    $err = "Only JPG, PNG, WEBP allowed";
                } elseif ($_FILES['image']['size'] > 5*1024*1024) {
                    $err = "Max size 5MB allowed";
                } else {

                    $newName = uniqid("car_").".".$ext;
                    $uploadDir = _DIR_."/../uploads/";

                    if(!is_dir($uploadDir)){
                        mkdir($uploadDir,0777,true);
                    }

                    if(move_uploaded_file($_FILES['image']['tmp_name'],$uploadDir.$newName)){
                        $imagePath = "/carconnect/uploads/".$newName;
                    } else {
                        $err = "Upload failed";
                    }
                }
            } else {
                $err = "Upload error";
            }
        }

        /* INSERT */
        if ($err === "") {

            $status = "pending";

            $stmt = mysqli_prepare($conn,"
            INSERT INTO car_listings
            (seller_id, make, model, year, price, mileage, fuel_type, transmission, description, image_path, status, category_id)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "issidisssssi",
                $sellerId,
                $brand,
                $model,
                $year,
                $price,
                $mileage,
                $fuelType,
                $transmission,
                $description,
                $imagePath,
                $status,
                $category_id // ✅ NEW
            );

            if(mysqli_stmt_execute($stmt)){
                $ok = "Car added successfully. Waiting for admin approval.";
            } else {
                $err = "Database error";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<h1>🚗 Add Car Listing</h1>

<?php if($err): ?>
<div class="alert"><?php echo e($err); ?></div>
<?php endif; ?>

<?php if($ok): ?>
<div class="alert success"><?php echo e($ok); ?></div>
<?php endif; ?>


<form class="form" method="POST" enctype="multipart/form-data">

<!-- CATEGORY ✅ NEW -->
<label>Category</label>
<select class="input" name="category_id" required>

<option value="">Select Category</option>

<?php
$res = mysqli_query($conn,"SELECT * FROM car_categories ORDER BY name ASC");

while($cat = mysqli_fetch_assoc($res)){
echo "<option value='".$cat['id']."'>".e($cat['name'])."</option>";
}
?>

</select>


<!-- BRAND -->
<label>Brand</label>
<select class="input" name="brand" required>
<option value="">Select Brand</option>
<option>Maruti</option>
<option>Tata</option>
<option>Mahindra</option>
<option>Hyundai</option>
<option>Honda</option>
<option>Toyota</option>
<option>Kia</option>
<option>BMW</option>
<option>Audi</option>
</select>

<!-- MODEL -->
<label>Model</label>
<input class="input" type="text" name="model" required>

<!-- YEAR -->
<label>Manufacturing Year</label>
<input class="input" type="number" name="year" required>

<!-- PRICE -->
<label>Price (₹)</label>
<input class="input" type="number" step="0.01" name="price" required>

<!-- MILEAGE -->
<label>Mileage (km)</label>
<input class="input" type="number" name="mileage">

<!-- FUEL -->
<label>Fuel Type</label>
<select class="input" name="fuel_type">
<option>Petrol</option>
<option>Diesel</option>
<option>CNG</option>
<option>Electric</option>
</select>

<!-- TRANSMISSION -->
<label>Transmission</label>
<select class="input" name="transmission">
<option>Manual</option>
<option>Automatic</option>
</select>

<!-- DESCRIPTION -->
<label>Description</label>
<textarea class="input" name="description" rows="4"></textarea>

<!-- IMAGE -->
<label>Car Image</label>
<input class="input" type="file" name="image">

<button class="btn primary" style="margin-top:15px">
🚀 Add Car
</button>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>