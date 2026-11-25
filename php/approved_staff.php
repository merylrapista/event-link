<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $staff_id = $_GET['staff_id'] ?? null;
    $action = $_GET['action'] ?? null;

   $staff_id = $_GET['staff_id'] ?? null;
    $action = $_GET['action'] ?? null;

    if ($staff_id && $action) {
        if ($action === 'approve') {
            $status = 'approved';
            $availability = 'available';
        } elseif ($action === 'reject') {
            $status = 'rejected';
            $availability = 'inactive';
        }

        $stmt = $conn->prepare("UPDATE staff SET status = ?, availability = ? WHERE staff_id = ?");
        $stmt->execute([$status, $availability, $staff_id]);
    }


    header("Location: staff.php"); 
    exit();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
