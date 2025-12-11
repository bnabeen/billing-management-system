<!DOCTYPE html>
<html>
<head>
    <title>BMS Signup</title>
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

            <h2>Create Account</h2>
            <p>Get started with your new account</p>

            <div id="errorMsg" class="error"></div>

            <form id="signupForm" action="../controllers/authController.php" method="POST">

                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-box">
                        <input type="text" id="phone" name="phone" placeholder="Enter phone number">
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
                        <input type="password" id="password" name="password" placeholder="Create a password">
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-box">
                        <input type="password" id="confirm" name="confirm" placeholder="Confirm your password">
                    </div>
                </div>

                <button class="btn" type="submit" name="signup">Sign Up</button>

            </form>

            <p class="auth-link">
                Already have an account?
                <a href="index.php">Login</a>
            </p>

        </div>

    </div>

</div>

<script src="../assets/js/main.js"></script>
<?php include '../includes/footer.php'; ?>
