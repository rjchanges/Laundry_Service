<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Service</title>
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
        .hero {
            background-image: linear-gradient(135deg, #ff7e5f, #feb47b);
            color: #fff;
            padding: 100px 0;
            text-align: center;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .hero h2 {
            font-size: 3rem;
            margin: 0;
        }
        .hero p {
            font-size: 1.2rem;
            margin: 10px 0;
        }
        .buttons {
            margin-top: 20px;
        }
        .buttons a {
            display: inline-block;
            margin: 0 10px;
            padding: 15px 30px;
            background-color: #ff6347;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover {
            background-color: #e05e43;
        }
        .features {
            display: flex;
            justify-content: space-around;
            padding: 50px 0;
            background-color: #f0f8ff;
        }
        .feature {
            text-align: center;
            padding: 20px;
            border: 2px solid #4CAF50;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .feature:hover {
            transform: translateY(-10px);
        }
        .feature h3 {
            font-size: 1.5rem;
            margin: 0 0 10px;
        }
        .feature p {
            font-size: 1rem;
            margin: 0;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our Laundry Service</h1>
        </div>
        <div class="hero">
            <h2>Get Your Clothes Clean and Fresh</h2>
            <p>Fast, reliable, and affordable laundry services at your doorstep.</p>
            <div class="buttons">
                <a href="register.php">Register</a>
                <a href="login.php">Customer Login</a>
                <a href="admin_login.php">Admin Login</a>
            </div>
        </div>
        <div class="features">
            <div class="feature">
                <img src="images/fast-service.jpg" alt="Fast Service" style="width: 100%; max-width: 300px; border-radius: 10px; margin-bottom: 20px;">
                <h3>Fast Service</h3>
                <p>Get your clothes cleaned and delivered back to you in no time.</p>
            </div>
            <div class="feature">
                <img src="images/quality-cleaning.jpg" alt="Quality Cleaning" style="width: 100%; max-width: 300px; border-radius: 10px; margin-bottom: 20px;">
                <h3>Quality Cleaning</h3>
                <p>Our advanced cleaning techniques ensure your clothes are spotless.</p>
            </div>
            <div class="feature">
                <img src="images/convenient.jpg" alt="Convenient" style="width: 100%; max-width: 300px; border-radius: 10px; margin-bottom: 20px;">
                <h3>Convenient</h3>
                <p>Order online and have your laundry picked up and delivered to your doorstep.</p>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2025 Laundry Service. All rights reserved.</p>
    </div>
</body>
</html>