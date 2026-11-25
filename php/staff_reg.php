<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $full_name = $_POST['fname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $contact = $_POST['num'] ?? '';
    $specialization = $_POST['Specialization'] ?? '';
    $service_id = $_POST['service_id'] ?? null;

    $stmt_service = $conn->prepare("SELECT service_id FROM services WHERE service_name = :name LIMIT 1");
    $stmt_service->execute([':name' => $specialization]);
    $service = $stmt_service->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        echo "<script>alert('Selected specialization does not match any service.'); window.history.back();</script>";
        exit();
    }

    $service_id = $service['service_id'];


    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'staff';

    $sql = $conn->prepare("INSERT INTO users (full_name, email, password, role, contact_number) 
                            VALUES (:full_name, :email, :password, :role, :contact)");
    $sql->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':password'=> $hashed_password,
        ':role'=> $role,
        ':contact' => $contact
    ]);

    $user_id = $conn->lastInsertId();

    $sql2 = $conn->prepare("INSERT INTO staff (user_id, service_id, specialization, availability, status) 
                            VALUES (:user_id, :service_id, :specialization, 'available', 'pending')");
    $sql2->execute([
        ':user_id' => $user_id,
        ':service_id' => $service_id,
        ':specialization' => $specialization
    ]);

    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = 'staff';
    $_SESSION['full_name'] = $full_name;

    header("Location: staff_dashboard.php");
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
