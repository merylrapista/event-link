<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $service_id = $_GET['id'] ?? null;

    if ($service_id) {
        $stmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
    }

    header("Location: services.php");
    exit();

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
