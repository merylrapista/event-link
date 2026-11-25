<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact_number'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, contact_number = ? WHERE user_id = ?");
    $stmt->execute([$full_name, $email, $contact, $user_id]);

    $_SESSION['full_name'] = $full_name;

    echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
