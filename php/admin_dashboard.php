<?php
$conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");

$themes = $conn->query("SELECT COUNT(*) FROM themes")->fetchColumn();
$services = $conn->query("SELECT COUNT(*) FROM services")->fetchColumn();
$customers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$events = $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
$booking_details = $conn->query("SELECT COUNT(*) FROM booking_details WHERE status != 'confirmed'")->fetchColumn();
$cateringCount = $conn->query("SELECT COUNT(*) FROM catering")->fetchColumn();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css"/>

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
            /* position: fixed; */
            top: 0;
            left: 0;

        }
        .sidebar h3 {
            color: white;
            padding-left: 20px;
            margin-bottom: 20px;
            font-weight: bolder;
        }

        .container h1 {
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
        .box {
            border: 2px solid #2d6a4f;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.3);
            transition: transform 0.2s;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-left:10%;
            background-color: rgba(255, 255, 255, 0.85); 
            /* border-color: #2d6a4f; */
        }

        .box:hover {
            transform: translateY(-5px);
        }

        .icon {
            font-size: 40px;
            color: #2d6a4f;
        }

        .row{
            width: 90%;
            
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

    <div class="container mt-4">
        <h1 class="mb-4">Admin Dashboard</h1>
        <div class="row g-3">
            <div class="col-md-4 col-sm-6">
                <div class="card text-center p-4 box">
                    <i class="fas fa-palette icon"></i>
                    <h5 class="mt-2">Total Themes</h5>
                    <p class="mb-0"><?= $themes ?></p>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card text-center p-4 box">
                    <i class="fas fa-cogs icon"></i>
                    <h5 class="mt-2">Total Services</h5>
                    <p class="mb-0"><?= $services ?></p>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
            <div class="card text-center p-4 box">
                <i class="fas fa-users icon"></i>
                <h5 class="mt-2">Total Customers</h5>
                <p class="mb-0"><?= $customers ?></p>
            </div>
            </div>

            <div class="col-md-4 col-sm-6">
            <div class="card text-center p-4 box">
                <i class="fas fa-calendar-alt icon"></i>
                <h5 class="mt-2">Total Events</h5>
                <p class="mb-0"><?= $events ?></p>
            </div>
            </div>

            <div class="col-md-4 col-sm-6">
            <div class="card text-center p-4 box">
                <i class="fas fa-book icon"></i>
                <h5 class="mt-2">Total Bookings</h5>
                <p class="mb-0"><?= $booking_details ?></p>
            </div>
            </div>

            <div class="col-md-4 col-sm-6">
            <div class="card text-center p-4 box">
                 <i class="fas fa-concierge-bell icon"></i>
                <h5 class="mt-2">Total catering</h5>
                <p class="mb-0"><?= $cateringCount ?></p>
            </div>
            </div>
        </div>
    </div>

</body>

</html>
