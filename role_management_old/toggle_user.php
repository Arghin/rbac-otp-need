<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Don't allow disabling yourself
    if ($_SESSION['user']['id'] == $id) {
        die("You can't toggle your own status.");
    }

    $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $update = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $update->bind_param("si", $newStatus, $id);
        $update->execute();
    }
}

header("Location: manage_users.php");
exit;
?>
