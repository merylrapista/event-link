<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $service_name = isset($_POST['service_name']) ? trim($_POST['service_name']) : '';
        $description  = isset($_POST['description']) ? trim($_POST['description']) : '';

        if (empty($service_name)) {
            $error = "Service name is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO services (service_name, description, status) VALUES (?, ?, 'active')");
            $stmt->execute([$service_name, $description]);

            $success = "✅ Service added successfully!";
            header("refresh:1;url=services.php");
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
<title>Add New Service</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
<style>
body { background-color: #f8f9fa; }
.container { max-width: 600px; margin-top: 50px; }
.card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4">➕ Add New Service</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="service_name" class="form-label">Service Name</label>
                <input type="text" name="service_name" id="service_name" class="form-control" placeholder="e.g. Photographer" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Brief description of the service" rows="3"></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="services.php" class="btn btn-secondary">⬅ Back</a>
                <button type="submit" class="btn btn-success">Save Service</button>
            </div>
        </form>
    </div>
</div>

<script src="../bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
