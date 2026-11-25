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

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    $services = $conn->query("SELECT * FROM services ORDER BY service_name")->fetchAll(PDO::FETCH_ASSOC);

    // $staff = $conn->query("SELECT s.staff_id, u.full_name, s.specialization, s.rate, s.availability 
    //                        FROM staff s 
    //                        JOIN users u ON s.user_id = u.user_id
    //                        WHERE s.availability = 'available'")->fetchAll(PDO::FETCH_ASSOC);
    
    $themes = $conn->query("SELECT * FROM themes ORDER BY theme_name")->fetchAll(PDO::FETCH_ASSOC);

    $query = "
        SELECT 
            event_id,
            event_name,
            event_date,
            event_time,
            venue_name,
            GROUP_CONCAT(service_name SEPARATOR ', ') AS services,
            SUM(rate) AS total_rate,
            status
        FROM booking_details
        WHERE user_id = :user_id
        GROUP BY event_id, event_name, event_date, event_time, venue_name, theme_name, status
        ORDER BY event_date DESC
    ";


    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch payments
    // $stmt = $conn->prepare("
    //     SELECT p.payment_id, p.amount, p.payment_date, p.status, s.service_name
    //     FROM payments p
    //     JOIN services s ON p.service_id = s.service_id
    //     WHERE p.user_id = ?
    //     ORDER BY p.payment_date DESC
    // ");
    // $stmt->execute([$user_id]);
    // $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch notifications
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
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <!-- <link rel="stylesheet" href="../bootstrap/bootstrap.css"> -->
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(to left, rgba(6,6,6,0.6), rgba(6,6,6,0.6)),
                        url('../img/hompage.jpeg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            margin-left: 260px; 
            color:white;
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
        .card { 
            margin-bottom: 20px; 
            
        }
        .card-header { 
            font-weight: bold; 
        }
        .container .row {
            position: default;
            margin-left: 50px; 
            padding: 15px;
        
        }

        .theme-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 10px;
            overflow: hidden;
        }

        .theme-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .carousel-inner img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .price-tag {
            font-weight: bold;
            color: #28a745;
        }

        /* .row{
            margin-left:0px;
        } */

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
    <div class="container mt-4">
        <h2>Welcome, <?= htmlspecialchars($customer['full_name']) ?> 👋</h2>
        <p>Email: <?= htmlspecialchars($customer['email']) ?></p>

        <!-- Notifications -->
        <div class="card">
            <div class="card-header">🔔 Notifications</div>
            <div class="card-body">
                <?php if ($notifications): ?>
                    <ul>
                        <?php foreach ($notifications as $n): ?>
                            <li><?= htmlspecialchars($n['message']) ?> 
                                <small class="text-muted">(<?= $n['created_at'] ?>)</small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No notifications yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Themes -->
      
        <div class="container">
            <h2 class="mb-4 text-start">Available Themes</h2>
            <div class="row">
                <?php if ($themes): ?>
                    <?php foreach ($themes as $theme): ?>
                        <?php
                        $stmt = $conn->prepare("SELECT image_path FROM theme_images WHERE theme_id = ?");
                        $stmt->execute([$theme['theme_id']]);
                        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div class="col-md-5 mb-4">
                            <div class="card theme-card shadow-sm">
                                <?php if ($images): ?>
                                    <div id="carousel<?= $theme['theme_id'] ?>" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php foreach ($images as $index => $img): ?>
                                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                    <img src="../uploads/<?= htmlspecialchars($img['image_path']) ?>" class="d-block w-100" alt="Theme Image">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (count($images) > 1): ?>
                                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $theme['theme_id'] ?>" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $theme['theme_id'] ?>" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <img src="../img/no-image.jpg" class="d-block w-100" style="height:220px; object-fit:cover;">
                                <?php endif; ?>

                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($theme['theme_name']) ?></h5>
                                    <p class="card-text text-muted"><?= htmlspecialchars($theme['category']) ?></p>
                                    <p class="card-text"><?= htmlspecialchars($theme['description']) ?></p>
                                    <p class="price-tag">₱<?= number_format($theme['price'], 2) ?></p>
                                    <form action="book_service.php" method="POST">
                                        <input type="hidden" name="theme_id" value="<?= $theme['theme_id'] ?>">
                                        <button type="submit" class="btn btn-primary w-100">Select Theme</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">No themes available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Services -->
        <div class="card">
            <div class="card-header">📋 Available Services</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <!-- <th>Price</th> -->
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($services as $svc): ?>
                        <tr>
                            <td><?= htmlspecialchars($svc['service_name']) ?></td>
                            <td><?= htmlspecialchars($svc['description']) ?></td>
                            <!-- <td>₱<?= number_format($svc['price'],2) ?></td> -->
                            <td><a href="book_service.php?service_id=<?= $svc['service_id'] ?>" class="btn btn-primary btn-sm">Book</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Staff -->
        <!-- <div class="card">
            <div class="card-header">👨‍💼 Available Staff</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead><tr><th>Name</th><th>Specialization</th><th>Rate</th><th>Availability</th></tr></thead>
                    <tbody>
                    <?php foreach ($staff as $st): ?>
                        <tr>
                            <td><?= htmlspecialchars($st['full_name']) ?></td>
                            <td><?= htmlspecialchars($st['specialization']) ?></td>
                            <td>₱<?= number_format($st['rate'],2) ?></td>
                            <td><span class="badge bg-success"><?= ucfirst($st['availability']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> -->

        <!-- My Bookings -->
        <div class="card">
            <div class="card-header">📅 My Bookings</div>
            <div class="card-body">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service Name</th>
                        <!-- <th>Staff Name</th> -->
                        <th>Venue</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($bookings)): ?>
                        <?php foreach($bookings as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['event_name']) ?></td>
                                <td><?= htmlspecialchars($b['event_date']) ?></td>
                                <td><?= htmlspecialchars($b['event_time']) ?></td>
                                <td><?= htmlspecialchars($b['services']) ?></td>
                                <!-- <td><?= htmlspecialchars($b['staff_name']) ?></td> -->
                                <td><?= htmlspecialchars($b['venue_name']) ?></td>
                                <td><?= htmlspecialchars($b['status']) ?></td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">You have no bookings yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Payments -->
        <!-- <div class="card">
            <div class="card-header">💳 Payment History</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Service</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td><?= $p['payment_id'] ?></td>
                            <td><?= htmlspecialchars($p['service_name']) ?></td>
                            <td>₱<?= number_format($p['amount'],2) ?></td>
                            <td><?= $p['payment_date'] ?></td>
                            <td><?= ucfirst($p['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> -->

        <!-- <a href="logout.php" class="btn btn-danger">Logout</a> -->
    </div>
    <script src="../bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
