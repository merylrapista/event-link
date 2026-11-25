<?php
$conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
    SELECT 
        e.event_id,
        e.event_name,
        u.full_name AS customer,
        t.theme_name,
        v2.venue_name,
        e.event_date AS booking_date,
        e.status,
        GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services
    FROM events e
    JOIN users u ON e.customer_id = u.user_id
    LEFT JOIN themes t ON e.theme_id = t.theme_id
    LEFT JOIN venues v2 ON e.venue_id = v2.venue_id
    LEFT JOIN event_services es ON e.event_id = es.event_id
    LEFT JOIN services s ON es.service_id = s.service_id
    WHERE e.status = 'Pending'
    GROUP BY e.event_id
    ORDER BY e.event_date DESC
";

$bookings = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Bookings</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <style>
        body { font-family: Arial; display: flex; background: linear-gradient(to left, rgba(6,6,6,0.6), rgba(6,6,6,0.6)), url('../img/hompage.jpeg') no-repeat center center; background-size: cover; }
        .sidebar { width: 250px; background: #343a40; color: white; padding-top: 20px; height: 100vh; }
        .sidebar h3 { padding-left: 20px; margin-bottom: 20px; font-weight: bolder; }
        .sidebar a { display: block; color: white; padding: 10px 20px; margin-bottom: 5px; border-radius: 5px; text-decoration: none; }
        .sidebar a:hover { background: #495057; }
        .container { padding: 20px; flex: 1; }
        .status-badge { padding: 4px 8px; border-radius: 4px; color: white; font-weight: 500; }
        .status-pending { background-color: #ffc107; }
        .status-confirmed { background-color: #2d6a4f; }
        .status-cancelled { background-color: #dc3545; }
        .container h2{ color:white; font-weight:bolder; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="themes.php">Manage Themes</a>
        <a href="catering.php">Manage Catering</a>
        <a href="services.php">Manage Services</a>
        <a href="customers.php">Customers</a>
        <a href="events.php">Events</a>
        <a href="bookings.php">Bookings</a>
    </div>

    <div class="container">
        <h2>Pending Bookings</h2>
        <?php if(count($bookings) === 0): ?>
            <p style="color:white;">No pending bookings at the moment.</p>
        <?php else: ?>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Event</th>
                    <th>Theme</th>
                    <th>Venue</th>
                    <th>Services</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['event_id']) ?></td>
                    <td><?= htmlspecialchars($booking['customer']) ?></td>
                    <td><?= htmlspecialchars($booking['event_name']) ?></td>
                    <td><?= htmlspecialchars($booking['theme_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['venue_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['services'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                            <?= htmlspecialchars($booking['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="update_booking_status.php?id=<?= $booking['event_id'] ?>&action=confirm" class="btn btn-success btn-sm">Confirm</a>
                        <a href="update_booking_status.php?id=<?= $booking['event_id'] ?>&action=cancel" class="btn btn-danger btn-sm">Reject</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>
    </div>

    <script src="../bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
