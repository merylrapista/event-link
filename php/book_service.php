<?php
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['theme_id'])) {
    $_SESSION['selected_theme'] = $_POST['theme_id'];
}

if (!isset($_SESSION['selected_theme'])) {
    echo "<p class='text-danger'>No theme selected. Please go back and select a theme.</p>";
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM themes WHERE theme_id = ?");
    $stmt->execute([$_SESSION['selected_theme']]);
    $selected_theme = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$selected_theme) {
        echo "<p class='text-danger'>Selected theme not found. Please go back and select a theme.</p>";
        exit();
    }

    $services = $conn->query("SELECT * FROM services ORDER BY service_name")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Book a Service</title>
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
            color: white;
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
            margin-left: 260px; 
            padding: 20px;
        }

        .card {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 10px;
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.03);
            background: rgba(255,255,255,0.15);
        }

        .card-title {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .btn-select.active {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .btn-select.active::after {
            content: " ✓";
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></h3>
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


    <div class="container mt-4">
         <h2 class="mb-4">Selected Theme</h2>
        <div class="card mb-4 p-3" style="background: rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:white;">
            <h5><?= htmlspecialchars($selected_theme['theme_name'] ?? 'N/A') ?></h5>
            <p><?= htmlspecialchars($selected_theme['description'] ?? '-') ?></p>
            <p><strong>Price:</strong> ₱<?= number_format($selected_theme['price'] ?? 0, 2) ?></p>
        </div>

        <h2 class="mb-4">Available Services</h2>
        <?php if (!empty($services)): ?>
            <form method="post" action="confirm_booking.php">
                <div class="row">
                    <?php foreach ($services as $service): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($service['service_name']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($service['description']) ?></p>
                                    <?php if (isset($service['rate'])): ?>
                                        <p><strong>Rate:</strong> ₱<?= number_format($service['rate'], 2) ?></p>
                                    <?php endif; ?>
                                    <button type="button" 
                                            class="btn btn-outline-light btn-select w-100" 
                                            onclick="toggleSelect(this, <?= $service['service_id'] ?>)">
                                        Select
                                    </button>
                                    <input type="checkbox" name="services[]" value="<?= $service['service_id'] ?>" class="d-none">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Book Selected Services</button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-danger">No services are currently available.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleSelect(button, id) {
            const checkbox = button.nextElementSibling;
            button.classList.toggle('active');
            checkbox.checked = !checkbox.checked;
        }
    </script>
</body>
</html>
