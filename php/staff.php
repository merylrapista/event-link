<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT s.staff_id, u.full_name, s.specialization, s.rate, s.availability, s.status
        FROM staff s
        JOIN users u ON s.user_id = u.user_id
        ORDER BY u.full_name
    ";
    $staffList = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Management</title>
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
        }
        .sidebar a:hover { 
            background: #495057; 
        }

        .card img { 
            width: 100px; 
            height: 100px; 
            object-fit: cover; 
            margin: 2px; 
        }

        .table {
            width: 50%;         
            margin: 20px auto;   
            table-layout: fixed; 
            height:70%; 
        }
        
        h2{
            color:white;
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
        <a href="staff.php">Staff</a>

    </div>
    <h2>Staff Management</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Specialization</th>
                <th>Rate</th>
                <th>Availability</th>
                <th>Portfolio</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($staffList as $staff): ?>
            <tr>
                <td><?= $staff['staff_id'] ?></td>
                <td><?= htmlspecialchars($staff['full_name']) ?></td>
                <td><?= $staff['specialization'] ? htmlspecialchars($staff['specialization']) : '-' ?></td>
                <td><?= $staff['rate'] ? '₱'.number_format($staff['rate'],2) : '-' ?></td>
                <td>
                    <?= $staff['availability'] == 'available' 
                        ? '<span class="badge bg-success">Available</span>' 
                        : ($staff['availability'] == 'booked' 
                            ? '<span class="badge bg-warning">Booked</span>' 
                            : '<span class="badge bg-secondary">Inactive</span>') ?>
                    </td>

                <td>
                    <?php
                    $stmt2 = $conn->prepare("SELECT image_path FROM staff_portfolio WHERE staff_id = ?");
                    $stmt2->execute([$staff['staff_id']]);
                    $portfolio = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                    if($portfolio){
                        foreach($portfolio as $img){
                            echo '<img src="'.htmlspecialchars($img['image_path']).'" alt="Portfolio">';
                        }
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td><?= ucfirst($staff['status']) ?></td>
                <td>
                    <?php if($staff['status'] == 'pending'): ?>
                        <a href="approved_staff.php?staff_id=<?= $staff['staff_id'] ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
                        <a href="approved_staff.php?staff_id=<?= $staff['staff_id'] ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
