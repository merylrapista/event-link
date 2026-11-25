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

    $query = "
            SELECT 
            bd.event_id,
            bd.event_name,
            bd.event_date,
            bd.event_time,
            bd.venue_name,
            bd.theme_name,
            GROUP_CONCAT(bd.service_name SEPARATOR ', ') AS services,
            SUM(bd.rate) + IFNULL(t.price, 0) AS total_rate,
            bd.status
        FROM booking_details bd
        LEFT JOIN themes t ON bd.theme_name = t.theme_name
        WHERE bd.user_id = :user_id
        GROUP BY bd.event_id, bd.event_name, bd.event_date, bd.event_time, bd.venue_name, bd.theme_name, t.price, bd.status
        ORDER BY bd.event_date DESC
        
    ";


    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">

</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">My Bookings</h2>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">You have no bookings yet.</div>
        <?php else: ?>
            <table class="table table-striped align-middle shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Theme</th>
                        <th>Service</th>
                        <th>Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $row): ?>
                        <tr>
                           <td><?= htmlspecialchars($row['event_name']) ?></td>
                            <td><?= htmlspecialchars($row['event_date']) ?></td>
                            <td><?= htmlspecialchars($row['event_time']) ?></td>
                            <td><?= htmlspecialchars($row['venue_name']) ?></td>
                            <td><?= htmlspecialchars($row['theme_name']) ?></td>
                            <td><?= htmlspecialchars($row['services']) ?></td>
                            <td>₱<?= number_format($row['total_rate'], 2) ?></td>

                            <td>
                                <span class="badge bg-<?= 
                                    $row['status'] == 'approved' ? 'success' : 
                                    ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
