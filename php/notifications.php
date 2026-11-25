<?php
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT full_name, email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(to left, rgba(6,6,6,0.6), rgba(6,6,6,0.6)),
                        url('../img/hompage.jpeg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 250px; 
            background: #343a40; 
            color: white; 
            padding-top: 20px; 
            height: 100vh; 
            position: fixed;
            top: 0;
            left: 0;
        }
        .sidebar h3 {
            color: white;
            padding-left: 20px;
            margin-bottom: 20px;
            font-weight: bolder;
        }
        .sidebar a { 
            display: block; 
            color: white; 
            padding: 10px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
            text-decoration: none; 
            transition: background 0.2s;
        }
        .sidebar a:hover { 
            background: #495057; 
        }

        .container {
            color: white;
            padding-left: 20px;
            margin-bottom: 20px;
            font-weight: bolder;
            margin-left:15%;
        }
    
</style>
</head>
<body>
    <div class="sidebar">
        <h3>Welcome, <?= htmlspecialchars($customer['full_name']) ?></h3>
            <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
            <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="book_service.php">Book Service</a></li>
            <li class="nav-item"><a class="nav-link" href="payments.php">Payments</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="container mt-4" >
        <h2>Notifications</h2>
        <?php if (!empty($notifications)): ?>
            <ul>
            <?php foreach ($notifications as $note): ?>
                <li <?= $note['is_read'] == 0 ? 'style="font-weight:bold;"' : '' ?>>
                    <?= htmlspecialchars($note['message']) ?> 
                    <small>(<?= $note['created_at'] ?>)</small>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No notifications.</p>
        <?php endif; ?>
    </div>

    
</body>
</html>