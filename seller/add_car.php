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
    $make         = trim($_POST['make'] ?? '');
    $model        = trim($_POST['model'] ?? '');
    $year         = (int) ($_POST['year'] ?? 0);
    $price        = (float) ($_POST['price'] ?? 0);
    $mileage      = (int) ($_POST['mileage'] ?? 0);
    $fuelType     = trim($_POST['fuel_type'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $description  = trim($_POST['description'] ?? '');

    if ($make === "" || $model === "" || $year <= 0 || $price <= 0) {
        $err = "Please fill all required fields correctly.";
    } else {
        $imagePath = "/carconnect/assets/images/default_car.jpg";

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['image']['name'];
                $tmpName  = $_FILES['image']['tmp_name'];
                $fileSize = $_FILES['image']['size'];

                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ["jpg", "jpeg", "png", "webp"];

                if (!in_array($ext, $allowed, true)) {
                    $err = "Only JPG, JPEG, PNG, and WEBP files are allowed.";
                } elseif ($fileSize > 5 * 1024 * 1024) {
                    $err = "Image size must be less than 5MB.";
                } else {
                    $newName = uniqid("car_", true) . "." . $ext;
                    $uploadDir = __DIR__ . "/../uploads/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $targetAbs = $uploadDir . $newName;
                    $targetRel = "/carconnect/uploads/" . $newName;

                    if (move_uploaded_file($tmpName, $targetAbs)) {
                        $imagePath = $targetRel;
                    } else {
                        $err = "Failed to upload image.";
                    }
                }
            } else {
                $err = "Image upload error.";
            }
        }

        if ($err === "") {
            $status = "pending";

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO car_listings 
                (seller_id, make, model, year, price, mileage, fuel_type, transmission, description, image_path, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "issidisssss",
                    $sellerId,
                    $make,
                    $model,
                    $year,
                    $price,
                    $mileage,
                    $fuelType,
                    $transmission,
                    $description,
                    $imagePath,
                    $status
                );

                if (mysqli_stmt_execute($stmt)) {
                    $ok = "Car listing added successfully. Waiting for admin approval.";
                } else {
                    $err = "Database error: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
            } else {
                $err = "Prepare failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<h1>Add Car</h1>

<?php if ($err): ?>
    <div class="alert"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>

<?php if ($ok): ?>
    <div class="alert success"><?php echo htmlspecialchars($ok); ?></div>
<?php endif; ?>

<form class="form" method="POST" enctype="multipart/form-data">
    <label>Make</label>
    <input class="input" type="text" name="make" required>

    <label>Model</label>
    <input class="input" type="text" name="model" required>

    <label>Year</label>
    <input class="input" type="number" name="year" required>

    <label>Price</label>
    <input class="input" type="number" step="0.01" name="price" required>

    <label>Mileage</label>
    <input class="input" type="number" name="mileage">

    <label>Fuel Type</label>
    <input class="input" type="text" name="fuel_type" placeholder="Petrol / Diesel / CNG">

    <label>Transmission</label>
    <input class="input" type="text" name="transmission" placeholder="Manual / Automatic">

    <label>Description</label>
    <textarea class="input" name="description" rows="5"></textarea>

    <label>Car Image</label>
    <input class="input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">

    <div style="margin-top: 14px;">
        <button class="btn primary" type="submit">Add Car</button>
    </div>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>