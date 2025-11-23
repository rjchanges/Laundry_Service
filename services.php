<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

include 'config.php';

// Fetch available services
$sql = "SELECT * FROM services";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle order placement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $service_id = $_POST['service_id'];
    $quantity = $_POST['quantity'];
    $status = "Pending";

    $sql = "INSERT INTO orders (user_id, service_id, quantity, status) VALUES (:user_id, :service_id, :quantity, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        // Store the order ID in the session for payment processing
        $_SESSION['order_id'] = $pdo->lastInsertId();
        header("location: payment.php");
        exit;
    } else {
        echo "<script>alert('Something went wrong. Please try again later.'); window.location.href='services.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .services {
            margin-top: 20px;
        }
        .services h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .service {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .service h3 {
            margin: 0 0 10px;
        }
        .service p {
            margin: 0 0 20px;
        }
        .service form {
            display: flex;
            justify-content: space-between;
        }
        .service form input[type="number"] {
            width: 50%;
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .service form input[type="submit"] {
            width: 40%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .service form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Services</h1>
        </div>
        <div class="services">
            <h2>Select a Service</h2>
            <?php foreach ($services as $service): ?>
                <div class="service">
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p>Price: $<?php echo htmlspecialchars($service['price']); ?></p>
                    <form method="post" action="">
                        <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                        <input type="number" name="quantity" min="1" placeholder="Quantity" required>
                        <input type="submit" value="Order">
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="back-link">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>