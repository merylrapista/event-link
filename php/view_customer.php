<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['id'])) {
        die("Customer ID missing.");
    }

    $user_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        die("Customer not found.");
    }

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Customer</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
</head>
<body>
<div class="container mt-4">
    <h2>Customer Details</h2>
    <table class="table table-bordered">
        <tr><th>ID</th><td><?= htmlspecialchars($customer['user_id']) ?></td></tr>
        <tr><th>Full Name</th><td><?= htmlspecialchars($customer['full_name']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($customer['email']) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($customer['status']) ?></td></tr>
        <tr><th>Created At</th><td><?= htmlspecialchars($customer['created_at']) ?></td></tr>
    </table>
    <a href="customers.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>
