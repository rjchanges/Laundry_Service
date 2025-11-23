<?php
session_start();

include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use 'id' to match the session set in login.php
    $user_id = $_SESSION['id']; 
    $service_id = $_POST['service_id'];
    $quantity = $_POST['quantity'];
    $status = "Pending";

    $sql = "INSERT INTO orders (user_id, service_id, quantity, status) VALUES (:user_id, :service_id, :quantity, :status)";
    
    if ($stmt = $pdo->prepare($sql)) {
        // Bind parameters using PDO syntax
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            $_SESSION['order_id'] = $pdo->lastInsertId(); // Store the order ID in session for payment
            header("location: payment.php");
            exit;
        } else {
            echo "<script>alert('Something went wrong. Please try again later.'); window.location.href='customer_dashboard.php';</script>";
        }
        unset($stmt);
    }
    unset($pdo);
}
?>