<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

$stmt = $conn->query("SELECT * FROM services ORDER BY service_id DESC");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Services</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
<style>
body {
    background-color: #f8f9fa;
}
.container {
    max-width: 900px;
}
.card {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.table th {
    background: #212529;
    color: #fff;
}
</style>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <h2 class="text-center mb-4">🛠 Manage Services</h2>

        <form action="add_service.php" method="POST" class="row g-3 mb-4">
            <button type="submit" name="add_service" class="btn btn-primary">➕ Add Service</button>

        </form>
        <table class="table table-striped align-middle text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($services): ?>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td><?= $s['service_id'] ?></td>
                            <td><?= htmlspecialchars($s['service_name']) ?></td>
                            <td><?= htmlspecialchars($s['description']) ?></td>
                            <td><?= htmlspecialchars($s['rate']) ?></td>
                            <td>
                                <span class="badge <?= $s['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst($s['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_service.php?id=<?= $s['service_id'] ?>" class="btn btn-sm btn-info">✏️ Edit</a>
                                <a href="deact_service.php?id=<?= $s['service_id'] ?>" class="btn btn-sm btn-warning">
                                    <?= $s['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </a>
                                <a href="delete_service.php?id=<?= $s['service_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-muted">No services available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../bootstrap/bootstrap.bundle.js"></script>
</body>
</html>
