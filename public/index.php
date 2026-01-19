<!DOCTYPE html>
<html>
<head>
    <title>BMS Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="center-page">

    <div class="auth-container">

        <!-- LEFT IMAGE -->
        <div class="auth-left">
            <img src="../assets/img/Kirana_Icon.png" alt="Kirana Image">
        </div>

        <!-- RIGHT FORM -->
        <div class="auth-right">

            <h2>Welcome Back</h2>
            <p>Login to your account</p>

            <div id="errorMsg" class="error" style="color: red; font-size: 13px; margin-bottom: 10px;"></div>
            <form id="loginForm" action="../controllers/authController.php" method="POST" onsubmit="return validateLogin()">

                <div class="form-group">
                    <label>Business Username</label>
                    <div class="input-box">
                        <input type="text" id="business_username" name="business_username" placeholder="Enter shop ID">
                    </div>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <input type="text" id="username" name="username" placeholder="Enter your username">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <input type="password" id="password" name="password" placeholder="Enter password">
                    </div>
                </div>

                <button class="btn" type="submit">Login</button>

            </form>

            <script>
                function validateLogin() {
                    const bus = document.getElementById('business_username').value.trim();
                    const user = document.getElementById('username').value.trim();
                    const pass = document.getElementById('password').value;
                    const err = document.getElementById('errorMsg');
                    
                    if (!bus || !user || !pass) {
                        err.innerText = "Please fill in all fields.";
                        return false;
                    }
                    if (pass.length < 4) {
                         err.innerText = "Password must be at least 4 characters.";
                         return false;
                    }
                    return true;
                }
            </script>

            <p class="auth-link">
                Don’t have an account?
                <a href="signup.php">Sign up</a>
            </p>

        </div>

    </div>

</div>

<script src="../assets/js/script.js"></script>
<?php include '../includes/footer.php'; ?>
