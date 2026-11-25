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
    e.event_date,
    GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services,
    SUM(bd.rate) AS total_rate
    FROM events e
    JOIN users u ON e.customer_id = u.user_id
    LEFT JOIN themes t ON e.theme_id = t.theme_id
    LEFT JOIN venues v2 ON e.venue_id = v2.venue_id
    LEFT JOIN event_services es ON e.event_id = es.event_id
    LEFT JOIN services s ON es.service_id = s.service_id
    LEFT JOIN booking_details bd ON e.event_id = bd.event_id
    WHERE e.status = 'Confirmed'
    GROUP BY e.event_id
    ORDER BY e.event_date ASC;

";
$events = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <style>
        body { 
            font-family: Arial; 
            display: flex;
            background: linear-gradient(to left, rgba(6,6,6,0.6), rgba(6,6,6,0.6)),
                url('../img/hompage.jpeg') no-repeat center center;
            background-size: cover;
        }
        .sidebar {
            width: 250px; 
            background: #343a40; 
            color: white; 
            padding-top: 20px; 
            height: 100vh;
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
        }
        .sidebar a:hover, .sidebar a.active { 
            background: #495057; 
        }
        .container {
            padding: 20px;
        }
        .container h2{
            color:white;
            font-weight:bolder;
        }
        .table{
            width: 95%;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="themes.php">Manage Themes</a>
        <a href="catering.php">Manage Catering</a>
        <!-- <a href="venue.php">Manage Themes</a> -->
        <a href="services.php">Manage Services</a>
        <a href="customers.php">Customers</a>
        <a href="events.php">Events</a>
        <a href="bookings.php">Bookings</a>
        <!-- <a href="staff.php">Staff</a> -->

    </div>

    <div class="container">
        <h2>Events</h2>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Event</th>
                    <th>Theme</th>
                    <th>Venue</th>
                    <th>Services</th>
                    <th>Event Date</th>
                    <th>Rate</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['event_id']) ?></td>
                    <td><?= htmlspecialchars($event['customer']) ?></td>
                    <td><?= htmlspecialchars($event['event_name']) ?></td>
                    <td><?= htmlspecialchars($event['theme_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($event['venue_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($event['services'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td>₱<?= number_format($event['total_rate'], 2) ?></td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<!-- 
    <script>
    function editEvent(id){
        window.location.href = 'edit_event.php?id=' + id;
    }
    function cancelEvent(id){
        if(confirm('Are you sure you want to cancel this event?')){
            window.location.href = 'cancel_event.php?id=' + id;
        }
    }
    </script> -->

</body>
</html>
