<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['id']) || !isset($_GET['action'])) {
        die("Invalid request.");
    }

    $user_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == "deactivate") {
        $status = "inactive";
    } elseif ($action == "activate") {
        $status = "active";
    } else {
        die("Invalid action.");
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->execute([$status, $user_id]);

    header("Location: customers.php");
    exit();

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
