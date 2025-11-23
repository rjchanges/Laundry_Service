<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

include 'config.php';

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$email = "";
$error = "";
$success = "";

// Fetch current user data
$sql = "SELECT email FROM users WHERE id = :id";
if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(':id', $user_id);
    if ($stmt->execute()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $user['email'];
    }
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);

    // Validate email
    if (empty($new_email)) {
        $error = "Please enter an email.";
    } else {
        // Prepare an update statement
        $sql = "UPDATE users SET email = :email";
        $params = [':email' => $new_email];

        // If password is provided, hash it and add to update
        if (!empty($new_password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $params[':id'] = $user_id;

        if ($stmt = $pdo->prepare($sql)) {
            if ($stmt->execute($params)) {
                $success = "Profile updated successfully.";
                $email = $new_email; // Update displayed email
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
            color: #333;
        }
        .container {
            max-width: 600px;
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
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; 
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .error { color: red; }
        .success { color: green; }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Profile</h1>
        </div>
        <div class="form-container">
            <?php if (!empty($error)) echo '<div class="message error">' . $error . '</div>'; ?>
            <?php if (!empty($success)) echo '<div class="message success">' . $success . '</div>'; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo htmlspecialchars($username); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form-group">
                    <input type="submit" value="Update Profile">
                </div>
            </form>
            <div class="back-link">
                <a href="customer_dashboard.php">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>