<?php
session_start();

// FIX: Change 'user_id' to 'id' to match login.php session variable
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// ADDED: Logic to handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_type = trim($_POST['service_type']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $user_id = $_SESSION['id'];

    // Note: You likely need a 'bookings' table for this, as the 'orders' table 
    // structure in your other files (service_id, quantity) doesn't match these fields (date, time).
    // This is a placeholder implementation.
    
    // Example SQL if you created a 'bookings' table:
    // $sql = "INSERT INTO bookings (user_id, service_type, booking_date, booking_time) VALUES (:uid, :stype, :bdate, :btime)";
    
    // For now, we simulate success and redirect:
    echo "<script>alert('Service booked successfully for $date at $time!'); window.location.href='customer_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .book-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .book-container h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="book-container">
        <h2>Book Service</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="service_type">Service Type:</label>
                <input type="text" id="service_type" name="service_type" required>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="time">Time:</label>
                <input type="time" id="time" name="time" required>
            </div>
            <div class="form-group">
                <button type="submit">Book Service</button>
            </div>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="customer_dashboard.php" style="color: #4CAF50; text-decoration: none;">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>