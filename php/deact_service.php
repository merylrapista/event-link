<?php
if (!isset($_GET['id'])) {
    die("⚠️ No service ID provided.");
}

$service_id = $_GET['id'];

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT status FROM services WHERE service_id = :id");
    $stmt->execute([':id' => $service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        die("❌ Service not found.");
    }

    $new_status = ($service['status'] === 'active') ? 'inactive' : 'active';

    $update = $conn->prepare("UPDATE services SET status = :status WHERE service_id = :id");
    $update->execute([':status' => $new_status, ':id' => $service_id]);

    header("Location: services.php?status_updated=1");
    exit;

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
