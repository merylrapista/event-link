<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        // $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        if($user['role'] === 'customer'){
            header("Location: customer_dashboard.php");
        // } elseif($user['role'] === 'staff'){
        //     header("Location: staff_dashboard.php");
        } elseif($user['role'] === 'admin'){
            header("Location: admin_dashboard.php");
        }
        exit();
    } else {
        echo "<script>
                alert('Invalid email or password');
                window.history.back();
              </script>";
        exit();
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
