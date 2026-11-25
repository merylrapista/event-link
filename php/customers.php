<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $search = $_GET['search'] ?? '';

    if ($search) {
        $stmt = $conn->prepare("
            SELECT * FROM users 
            WHERE (name LIKE ? OR email LIKE ?) AND role='customer' 
            ORDER BY user_id DESC
        ");
        $sql->execute(["%$search%", "%$search%"]);
        $customers = $sql->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sql = $conn->query("SELECT * FROM users WHERE role='customer' ORDER BY user_id DESC");
        $customers = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
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

        .container { 
            margin-left: 27px; 
            padding-top: 20px; 
        }
        .search-bar { 
            max-width: 400px; 
            margin-bottom: 20px; 
        }

        .navig h2{
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
        <!-- <a href="staff.php">Staff</a> -->

    </div>

    <div class="container">
        <div class="navig d-flex justify-content-between align-items-center mb-3">
            <h2>Customers</h2>
            <form method="GET" class="search-bar">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

        </div>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($customers) > 0): ?>
                    <?php foreach ($customers as $index => $cust): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($cust['full_name']) ?></td>
                            <td><?= htmlspecialchars($cust['email']) ?></td>
                            <td><?= htmlspecialchars($cust['contact_number']) ?></td>
                            <td><?= isset($cust['status']) ? ucfirst($cust['status']) : 'Active' ?></td>
                            <td>
                                <a href="view_customer.php?id=<?= $cust['user_id'] ?>" class="btn btn-sm btn-info">View</a>
                                <?php if (!isset($cust['status']) || $cust['status'] == 'active'): ?>
                                    <a href="react.php?id=<?= $cust['user_id'] ?>&action=deactivate" class="btn btn-sm btn-warning">Deactivate</a>
                                <?php else: ?>
                                    <a href="react.php?id=<?= $cust['user_id'] ?>&action=activate" class="btn btn-sm btn-success">Reactivate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No customers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="../bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
