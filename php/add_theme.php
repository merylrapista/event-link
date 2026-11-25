<?php

try {
    $conn = new PDO("mysql:host=localhost;dbname=event_management", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$category = $_GET['category'] ?? '';

if ($category) {
    $sql = $conn->prepare("
        SELECT t.theme_id, t.theme_name, t.category, t.description, t.price, i.image_path
        FROM themes t
        LEFT JOIN theme_images i ON t.theme_id = i.theme_id
        WHERE t.category = ?
        ORDER BY t.theme_id
    ");
    $sql->execute([$category]);
    $data = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
} else {
    $sql = $conn->query("
        SELECT t.theme_id, t.theme_name, t.category, t.description, t.price, i.image_path
        FROM themes t
        LEFT JOIN theme_images i ON t.theme_id = i.theme_id
        ORDER BY t.theme_id
    ");
    $data = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme_name  = $_POST['theme_name'];
    $category    = $_POST['category'];
    $description = $_POST['description'];
    $price       = $_POST['price'];

    $sql = $conn->prepare("INSERT INTO themes (theme_name, category, description, price) VALUES (?, ?, ?, ?)");
    $sql->execute([$theme_name, $category, $description, $price]);
    $theme_id = $conn->lastInsertId();

    if (!empty($_FILES['images']['name'][0])) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        foreach ($_FILES['images']['name'] as $key => $fileName) {
            $tmpName = $_FILES['images']['tmp_name'][$key];
            if ($fileName === "" || !is_uploaded_file($tmpName)) continue;

            $uniqueName = uniqid() . '_' . basename($fileName);
            $targetFile = $targetDir . $uniqueName;

            if (move_uploaded_file($tmpName, $targetFile)) {
                $sqlImg = $conn->prepare("INSERT INTO theme_images (theme_id, image_path) VALUES (?, ?)");
                $sqlImg->execute([$theme_id, $uniqueName]);
            }
        }
    } 
    header("Location: themes.php");
    exit();   
}

    $sql = $conn->query("
        SELECT t.theme_id, t.theme_name, t.category, t.description, t.price, i.image_path
        FROM themes t
        LEFT JOIN theme_images i ON t.theme_id = i.theme_id
        ORDER BY t.theme_id
    ");
    $data = $sql->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
