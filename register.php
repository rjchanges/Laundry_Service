<?php
include 'config.php';

// Initialize variables
$error = '';
$username = '';
$email = '';
$password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Check if Username OR Email already exists
    $check_sql = "SELECT id FROM users WHERE username = :username OR email = :email";
    
    if ($check_stmt = $pdo->prepare($check_sql)) {
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        
        if ($check_stmt->execute()) {
            if ($check_stmt->rowCount() > 0) {
                // If we found a row, it means the username or email is taken
                $error = "That username or email is already registered.";
            } else {
                // 2. No duplicates found, proceed with Insert
                $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                
                if ($stmt = $pdo->prepare($sql)) {
                    $param_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Bind parameters
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $param_password);
                    
                    if ($stmt->execute()) {
                        // Redirect to login page on success
                        header("location: login.php");
                        exit;
                    } else {
                        $error = "Something went wrong. Please try again later.";
                    }
                }
            }
        } else {
            $error = "Oops! Something went wrong checking credentials.";
        }
        unset($check_stmt);
    }
    
    // Close connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .container {
            max-width: 500px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            background-color: #4CAF50;
            color: #fff;
            padding: 20px 0;
            border-radius: 10px 10px 0 0;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .form-container {
            padding: 20px;
        }
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding doesn't break layout */
        }
        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #ffe6e6;
            border: 1px solid red;
            border-radius: 5px;
        }
        .back-link {
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
            <h1>Register</h1>
        </div>
        <div class="form-container">
            <?php if (!empty($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
            <h2>Create an Account</h2>
            <form method="post" action="">
                <label for="username" style="display:block; text-align:left;">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                
                <label for="email" style="display:block; text-align:left;">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                
                <label for="password" style="display:block; text-align:left;">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <input type="submit" value="Register">
            </form>
            <div class="back-link">
                <a href="index.php">Back to Home</a> | <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>