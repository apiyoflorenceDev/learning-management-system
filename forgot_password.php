<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Body setup */
/* General page setup */
body {
    background-color: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    overflow: hidden; /* Prevent page scroll */
}

/* Form container */
form {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    height: auto;
    max-height: 90vh; /* Maximum height */
    overflow-y: auto; /* Allow scroll if content exceeds */
    box-sizing: border-box;
    transition: all 0.3s ease; /* Smooth transition for form scaling */
}

/* Form heading */
h3 {
    text-align: center;
    color: #333333;
    margin-bottom: 20px;
    font-size: 24px;
}

/* Labels for inputs */
label {
    display: block;
    margin-bottom: 8px;
    color: #555555;
    font-weight: 500;
}

/* Password input container */
.password-container {
    position: relative;
}

.password-container input[type="password"],
.password-container input[type="text"] {
    width: 100%;
    padding: 10px 40px 10px 10px;
    margin-bottom: 20px;
    border: 1px solid #cccccc;
    border-radius: 5px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.password-container input[type="password"]:focus,
.password-container input[type="text"]:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 8px rgba(74, 144, 226, 0.4);
}

/* Show password icon */
.password-container .toggle-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    transition: color 0.3s ease;
}

.password-container .toggle-password:hover {
    color: #4a90e2; /* Hover effect */
}

/* Submit button */
input[type="submit"] {
    width: 100%;
    background-color: #4a90e2;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

input[type="submit"]:hover {
    background-color: #357ABD;
    transform: scale(1.05);
}

/* Password strength text */
#strength {
    margin-top: -15px;
    margin-bottom: 15px;
    font-size: 14px;
    font-weight: bold;
}

/* Message for success/error */
.message {
    text-align: center;
    margin-bottom: 15px;
    font-weight: bold;
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
}

/* Mobile responsiveness */
@media (max-width: 480px) {
    form {
        padding: 20px;
    }

    h3 {
        font-size: 20px;
    }
}


    </style>
</head>
<body>

<form action="change_password_student.php" method="POST" onsubmit="return validateForm()">
    <h3>Change Password</h3>

    <div class="message" id="message"></div>

    <label>Current Password:</label>
    <div class="password-container">
        <input type="password" name="current_password" required id="currentPassword">
        <span class="toggle-password" onclick="togglePassword('currentPassword', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>

    <label>New Password:</label>
    <div class="password-container">
        <input type="password" name="new_password" required id="newPassword" oninput="checkStrength()">
        <span class="toggle-password" onclick="togglePassword('newPassword', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>
    <div id="strength"></div>

    <label>Confirm New Password:</label>
    <div class="password-container">
        <input type="password" name="confirm_new_password" required id="confirmPassword">
        <span class="toggle-password" onclick="togglePassword('confirmPassword', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>

    <input type="submit" value="Change Password">
</form>

<script>
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    const iconElement = icon.querySelector('i');
    if (field.type === "password") {
        field.type = "text";
        iconElement.classList.remove('fa-eye');
        iconElement.classList.add('fa-eye-slash');
    } else {
        field.type = "password";
        iconElement.classList.remove('fa-eye-slash');
        iconElement.classList.add('fa-eye');
    }
}

function checkStrength() {
    const strengthText = document.getElementById('strength');
    const password = document.getElementById('newPassword').value;
    let strength = "";

    if (password.length === 0) {
        strength = "";
    } else if (password.length < 6) {
        strength = "<span style='color: red;'>Weak</span>";
    } else if (password.match(/[A-Z]/) && password.match(/[0-9]/) && password.length >= 8) {
        strength = "<span style='color: green;'>Strong</span>";
    } else {
        strength = "<span style='color: orange;'>Medium</span>";
    }

    strengthText.innerHTML = "Password Strength: " + strength;
}

function validateForm() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const messageBox = document.getElementById('message');

    if (newPassword !== confirmPassword) {
        messageBox.innerHTML = "Passwords do not match!";
        messageBox.style.color = "red";
        return false;
    }

    messageBox.innerHTML = "Password changed successfully!";
    messageBox.style.color = "green";
    return true;
}
</script>

</body>
</html>
