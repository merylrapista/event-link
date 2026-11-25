<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$selected_theme_id = $_SESSION['selected_theme'] ?? null;
$service_ids = $_POST['services'] ?? [];

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $themeStmt = $conn->query("SELECT theme_id, theme_name, description, price FROM themes ORDER BY theme_name");
    $allThemes = $themeStmt->fetchAll(PDO::FETCH_ASSOC);

    $selectedTheme = null;
    if ($selected_theme_id) {
        $stmt = $conn->prepare("SELECT * FROM themes WHERE theme_id = ?");
        $stmt->execute([$selected_theme_id]);
        $selectedTheme = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $selectedServices = [];
    if (!empty($service_ids)) {
        $placeholders = implode(',', array_fill(0, count($service_ids), '?'));
        $stmt = $conn->prepare("SELECT * FROM services WHERE service_id IN ($placeholders)");
        $stmt->execute($service_ids);
        $selectedServices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $total = ($selectedTheme['price'] ?? 0);
    foreach ($selectedServices as $s) $total += $s['rate'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_name'])) {

        $event_name = trim($_POST['event_name']);
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'];
        $venue_name = trim($_POST['venue']);
        $theme_name = $_POST['theme'] ?? ($selectedTheme['theme_name'] ?? null);

        if (empty($event_name) || empty($event_date) || empty($event_time) || empty($venue_name) || empty($theme_name) || empty($service_ids)) {
            echo "<script>alert('Please complete all fields.'); window.history.back();</script>";
            exit();
        }

        $venueStmt = $conn->prepare("SELECT venue_id FROM venues WHERE venue_name = ?");
        $venueStmt->execute([$venue_name]);
        $venue_id = $venueStmt->fetchColumn();
        if (!$venue_id) {
            $insertVenue = $conn->prepare("INSERT INTO venues (venue_name) VALUES (?)");
            $insertVenue->execute([$venue_name]);
            $venue_id = $conn->lastInsertId();
        }

        $themeStmt = $conn->prepare("SELECT theme_id FROM themes WHERE theme_name = ?");
        $themeStmt->execute([$theme_name]);
        $theme_id = $themeStmt->fetchColumn();
        if (!$theme_id) {
            echo "<script>alert('Selected theme not found.'); window.history.back();</script>";
            exit();
        }

        $eventStmt = $conn->prepare("INSERT INTO events (customer_id, event_name, event_date, event_time, venue_id, theme_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
        $eventStmt->execute([$user_id, $event_name, $event_date, $event_time, $venue_id, $theme_id]);
        $event_id = $conn->lastInsertId();

        $serviceStmt = $conn->prepare("INSERT INTO event_services (event_id, service_id) VALUES (?, ?)");
        foreach ($service_ids as $sid) {
            $serviceStmt->execute([$event_id, $sid]);
        }

        echo "<script>alert('Booking confirmed!'); window.location.href='my_bookings.php';</script>";
        exit();
    }

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Confirm Booking</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
<style>
body { font-family: Arial,sans-serif; background: linear-gradient(to left, rgba(6,6,6,0.6), rgba(6,6,6,0.6)), url('../img/hompage.jpeg') no-repeat center center; background-size: cover; color:white; padding:20px; }
.container{margin-top:30px;}
.card{background:rgba(255,255,255,0.1);padding:15px;border-radius:10px;margin-bottom:15px;color:white;}
.list-group-item{background:rgba(255,255,255,0.1);border:none;color:white;}
.btn-success{background-color:#28a745;border:none;} 
.btn-success:hover{background-color:#218838;}
.btn-secondary{background-color:#6c757d;border:none;}
</style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-light">Confirm Your Booking</h2>
    <form method="post">
        <div class="row">
            <div class="col-md-7">
                <div class="card p-3 mb-3">
                    <h4>Event Details</h4>
                    <div class="mb-3"><label for="event_name">Event Name</label><input type="text" id="event_name" name="event_name" class="form-control" required></div>
                    <div class="mb-3"><label for="event_date">Event Date</label><input type="date" id="event_date" name="event_date" class="form-control" required></div>
                    <div class="mb-3"><label for="event_time">Event Time</label><input type="time" id="event_time" name="event_time" class="form-control" required></div>
                    <div class="mb-3"><label for="venue">Venue</label><input type="text" id="venue" name="venue" class="form-control" placeholder="Enter your preferred venue" required><small class="text-light">You may type your own venue suggestion.</small></div>
                    <div class="mb-3">
                        <label for="theme">Theme</label>
                        <select id="theme" name="theme" class="form-control" required <?= $selected_theme_id ? 'disabled' : '' ?>>
                            <option value="">-- Select Theme --</option>
                            <?php foreach ($allThemes as $t): 
                                $selected = ($t['theme_id'] == $selected_theme_id) ? 'selected' : '';
                            ?>
                                <option value="<?= htmlspecialchars($t['theme_name']) ?>" <?= $selected ?>><?= htmlspecialchars($t['theme_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card p-3 mb-3">
                    <h4>Your Selection</h4>
                    <?php if ($selectedTheme): ?>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div><strong>Theme: <?= htmlspecialchars($selectedTheme['theme_name']) ?></strong><br><small><?= htmlspecialchars($selectedTheme['description']) ?></small></div>
                                <span>₱<?= number_format($selectedTheme['price'], 2) ?></span>
                            </li>
                        </ul>
                        <input type="hidden" name="theme" value="<?= htmlspecialchars($selectedTheme['theme_name']) ?>">
                    <?php endif; ?>
                    <?php if (!empty($selectedServices)): ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($selectedServices as $service): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><strong><?= htmlspecialchars($service['service_name']) ?></strong><br><small><?= htmlspecialchars($service['description']) ?></small></div>
                                    <span>₱<?= number_format($service['rate'], 2) ?></span>
                                </li>
                                <input type="hidden" name="services[]" value="<?= (int)$service['service_id'] ?>">
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <h5 class="text-end">Total: <strong>₱<?= number_format($total, 2) ?></strong></h5>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success px-4">Confirm Booking</button>
                        <a href="book_service.php" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>
