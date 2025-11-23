<?php
include 'config.php';

// Initialize variables to store error messages and user input
$error = '';
$username = '';
$password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare a select statement
    $sql = "SELECT id, username, password FROM users WHERE username = :username";
    
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':username', $param_username);
        
        // Set parameters
        $param_username = $username;
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Check if the username exists
            if ($stmt->rowCount() == 1) {
                // Fetch the user data
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, so start a new session
                    session_start();
                    
                    // Store data in session variables
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // FIX: Redirect user to customer_dashboard.php instead of services.php
                    header("location: customer_dashboard.php");
                } else {
                    $error = "The password you entered was not valid.";
                }
            } else {
                $error = "No account found with that username.";
            }
        } else {
            $error = "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        unset($stmt);
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
    <title>Login</title>
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
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .back-link {
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
            <h1>Login</h1>
        </div>
        <div class="form-container">
            <?php if (!empty($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
            <h2>Login to Your Account</h2>
            <form method="post" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Login">
            </form>
            <div class="back-link">
                <a href="index.php">Back to Home</a> | <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</body>
</html>