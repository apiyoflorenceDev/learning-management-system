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
        <form action="signup-handler.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text"  name="username" required><br><br>

            <label for="email">Email:</label>
            <input type="email"  name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password"  name="password" required><br><br>
            
            <label for="role">Select Role:</label>
             <select id="role" name="role" required>
              <option value="student">Student</option>
              <option value="instructor">Instructor</option>
             </select>
            
            

            <button type="submit">Sign Up</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>
