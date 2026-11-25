<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'], $_GET['action'])) {
    header("Location: bookings.php");
    exit();
}

$event_id = (int)$_GET['id'];
$action = $_GET['action'];

$conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($action === 'confirm') {
    $new_status = 'Confirmed';
} elseif ($action === 'cancel') {
    $new_status = 'Cancelled';
} else {
    header("Location: bookings.php");
    exit();
}

$stmt = $conn->prepare("UPDATE events SET status = ? WHERE event_id = ?");
$stmt->execute([$new_status, $event_id]);

header("Location: bookings.php");
exit();
