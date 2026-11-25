<?php
if (!isset($_GET['id'])) {
    die("⚠️ No service ID provided.");
}

$service_id = $_GET['id'];

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM services WHERE service_id = :id");
    $stmt->execute([':id' => $service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        die("❌ Service not found.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST['service_name']);
        $description = trim($_POST['description']);
        $rate = trim($_POST['rate']);
        $status = $_POST['status'];

        if (!empty($name)) {
            $update = $conn->prepare("
                UPDATE services 
                SET service_name = :name, description = :desc,rate = :rate, status = :status 
                WHERE service_id = :id
            ");
            $update->execute([
                ':name' => $name,
                ':desc' => $description,
                ':rate' => $rate,
                ':status' => $status,
                ':id' => $service_id
            ]);

            header("Location: services.php?updated=1");
            exit;
        } else {
            $error = "⚠️ Service name cannot be empty.";
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Service</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
<style>
body {
    background-color: #f8f9fa;
}
.container {
    max-width: 600px;
}
.card {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="text-center mb-4">✏️ Edit Service</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-warning"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Service Name</label>
                <input type="text" name="service_name" class="form-control" value="<?= htmlspecialchars($service['service_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($service['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Rate</label>
                <textarea name="rate" class="form-control" rows="3"><?= htmlspecialchars($service['rate']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?= $service['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $service['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="services.php" class="btn btn-secondary">⬅ Back</a>
               <a href="services.php"><button type="submit" class="btn btn-primary">💾 Save Changes</button></a> 
            </div>
        </form>
    </div>
</div>
<script src="../bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
