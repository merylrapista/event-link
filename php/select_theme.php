<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $themes = $conn->query("SELECT * FROM themes ORDER BY theme_name")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['theme'])) {
    $_SESSION['selected_theme'] = $_POST['theme']; 
    header("Location: booking_services.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Theme</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
</head>
<body>
<div class="container mt-4">
    <h2>Select a Theme</h2>

    <?php if (!empty($themes)): ?>
        <form method="post">
            <?php foreach ($themes as $theme): ?>
                <div class="card p-3 mb-3">
                    <label>
                        <input type="radio" name="theme" value="<?= $theme['theme_id'] ?>" required>
                        <strong><?= htmlspecialchars($theme['theme_name'] ?? 'N/A') ?></strong> - ₱<?= number_format($theme['rate'] ?? 0, 2) ?>
                    </label>
                    <p><?= htmlspecialchars($theme['description'] ?? '-') ?></p>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Next: Select Services</button>
        </form>
    <?php else: ?>
        <p class="text-danger">No themes available. Please contact the administrator.</p>
    <?php endif; ?>
</div>
</body>
</html>
