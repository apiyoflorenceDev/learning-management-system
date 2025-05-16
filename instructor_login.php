<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Body styling */
body {
    background: linear-gradient(to right,rgb(209, 234, 241),rgb(193, 210, 231));
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    overflow: hidden;
}

/* Form container */
form {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    width: 320px;
    text-align: center;
    position: relative;
    animation: fadeIn 1s ease-in-out;
}

/* Heading */
form h2 {
    margin-bottom: 20px;
    color: #333;
}

/* Fade in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Label styling */
form label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333333;
    text-align: left;
}

/* Input container with icons */
.input-container {
    position: relative;
    margin-bottom: 20px;
}

.input-container i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #0072ff;
}

.input-container input {
    width: 100%;
    padding: 10px 10px 10px 35px;
    border: 1px solid #cccccc;
    border-radius: 5px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

.input-container input:focus {
    border-color: #0072ff;
    outline: none;
}

/* Toggle password visibility */
.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #0072ff;
}

/* Submit button */
form input[type="submit"] {
    background-color: #0072ff;
    color: #ffffff;
    border: none;
    padding: 12px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s;
    font-weight: bold;
}

form input[type="submit"]:hover {
    background-color: #005fcc;
    transform: scale(1.05);
}

/* Forgot password link */
.forgot-password {
    display: block;
    margin-top: 15px;
    color: #0072ff;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.forgot-password:hover {
    color: #005fcc;
    text-decoration: underline;
}

/* Social login buttons */
.social-login {
    margin-top: 20px;
}

.social-login p {
    margin-bottom: 10px;
    color: #555;
}

.social-login button {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    color: white;
    font-weight: bold;
    transition: background-color 0.3s;
}

.google-btn {
    background-color: #db4437;
}

.google-btn:hover {
    background-color: #c23321;
}

.facebook-btn {
    background-color: #3b5998;
}

.facebook-btn:hover {
    background-color: #2d4373;
}

/* Responsive */
@media (max-width: 400px) {
    form {
        width: 90%;
        padding: 30px 20px;
    }
}

    </style>
</head>
<body>
<form method="POST" action="instructor_login_handler.php" onsubmit="showLoading()">
    <h2>Welcome Back</h2>

    <div class="input-container">
        <label for="email">Email:</label>
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" required>
    </div>

    <div class="input-container">
        <label for="password">Password:</label>
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" required>
        <span class="toggle-password" onclick="togglePassword()">
            <i class="fas fa-eye"></i>
        </span>
    </div>

    <input type="submit" value="Login" id="loginButton">

</form>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password i');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function showLoading() {
        const button = document.getElementById('loginButton');
        button.value = 'Logging in...';
        button.disabled = true;
    }
</script>
</body>
</html>
