<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MyLearning</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    

    <div class="signup-container">
        <h2>Create an Account</h2>
        <form action="instructor_signup_handler.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text"  name="username" required><br><br>

            <label for="email">Email:</label>
            <input type="email"  name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password"  name="password" required><br><br>

            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="instructor_login.php">Log in</a></p>
    </div>
</body>
</html>