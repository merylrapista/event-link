<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management","root","");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $full_name = $_POST['fname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $contact = $_POST['num'] ?? '';

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Passwords do not match!');
                window.history.back();
              </script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if($stmt->rowCount() > 0){
        echo "<script>
                alert('This email is already registered. Please use another one.');
                window.history.back();
              </script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'customer';

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

    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    $_SESSION['full_name'] = $full_name;

    header("Location: customer_dashboard.php");
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
