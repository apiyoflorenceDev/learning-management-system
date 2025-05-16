<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <h2>Login</h2>
    <form action="login-handler.php" method="post">
        <label for="username">Username:</label>
        <input type="text"  name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password"  name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
</body>
</html>