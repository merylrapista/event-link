<?php
session_start();

// Redirect if not logged in as customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = $_SESSION['user_id'];

    // Fetch customer info
    $stmt = $conn->prepare("SELECT full_name, email, contact_number FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
        .main-content {
            margin-left: 260px;
            padding: 30px;
            color: #fff;
        }
        .card {
            background-color: rgba(255,255,255,0.85);
            border-radius: 15px;
            padding: 20px;
            color: #000;
            max-width: 600px;
        }
        .card h2 {
            margin-bottom: 20px;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Welcome, <?= htmlspecialchars($customer['full_name']) ?></h3>
        <a href="customer_dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="notifications.php">Notifications</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="book_service.php">Book Service</a>
        <a href="payments.php">Payments</a>
        <a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>My Profile</h2>
            <form action="update_profile.php" method="POST">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($customer['full_name']) ?>" required>

                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>" required>

                <label for="contact_number" class="form-label">Phone Number</label>
                <input type="text" id="contact_number" name="contact_number" class="form-control" value="<?= htmlspecialchars($customer['contact_number']) ?>">

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
