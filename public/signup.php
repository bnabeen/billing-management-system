<!DOCTYPE html>
<html>
<head>
    <title>BMS Signup</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .hidden { display: none; }
        .error-text { color: red; font-size: 12px; margin-top: 5px; display: block; }
    </style>
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

            <form id="signupForm" action="../controllers/authController.php" method="POST" onsubmit="return validateForm()">
                
                <!-- Role Selection -->
                <div class="form-group">
                    <label>I am a:</label>
                    <div class="input-box">
                        <select id="role" name="role" onchange="toggleBusinessFields()">
                            <option value="owner">Business Owner (New Shop)</option>
                            <option value="staff">Staff (Existing Shop)</option>
                        </select>
                    </div>
                </div>

                <!-- Business Details -->
                <div id="newBusinessFields">
                    <div class="form-group">
                        <label>Business Name (Shop Name)</label>
                        <div class="input-box">
                            <input type="text" id="business_name" name="business_name" placeholder="e.g. Sumit Kirana Store">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Business Username (Unique ID for Shop)</label>
                    <div class="input-box">
                        <input type="text" id="business_username" name="business_username" placeholder="e.g. sumit_kirana">
                    </div>
                    <small style="color:#666; font-size:11px;">You will use this to identify your shop during login</small>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-box">
                        <input type="text" id="phone" name="phone" placeholder="Enter phone number (97/98..)">
                    </div>
                    <span id="phoneError" class="error-text"></span>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-box">
                        <input type="text" id="username" name="username" placeholder="Enter your username">
                    </div>
                    <span id="userError" class="error-text"></span>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-box">
                        <input type="password" id="password" name="password" placeholder="Create a password">
                    </div>
                    <span id="passError" class="error-text"></span>
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

<script>
    function toggleBusinessFields() {
        var role = document.getElementById('role').value;
        var newBiz = document.getElementById('newBusinessFields');
        if (role === 'staff') {
            newBiz.style.display = 'none';
        } else {
            newBiz.style.display = 'block';
        }
    }

    function validateForm() {
        var phone = document.getElementById('phone').value;
        var username = document.getElementById('username').value;
        var password = document.getElementById('password').value;
        var phoneError = document.getElementById('phoneError');
        var userError = document.getElementById('userError');
        var passError = document.getElementById('passError');
        
        phoneError.innerText = "";
        userError.innerText = "";
        passError.innerText = "";
        
        var isValid = true;

        // Phone Validation
        // - 10 digits
        // - Start with 97 or 98
        // - Only numbers
        if (!/^\d+$/.test(phone)) {
            phoneError.innerText = "Phone must contain only numbers.";
            isValid = false;
        } else if (phone.length !== 10) {
            phoneError.innerText = "Phone number must be exactly 10 digits.";
            isValid = false;
        } else if (!phone.startsWith("97") && !phone.startsWith("98")) {
            phoneError.innerText = "Phone number must start with 97 or 98.";
            isValid = false;
        }

        // Username Validation
        // - Not start with number
        if (/^\d/.test(username)) {
            userError.innerText = "Username cannot start with a number.";
            isValid = false;
        }

        // Password Validation
        // - Mix of chars? (Let's enforce at least 6 chars for now)
        // - Should not support email -> Check for '@'
        if (password.includes('@') && password.includes('.')) {
             // Heuristic for email
             passError.innerText = "Password cannot be an email address.";
             isValid = false;
        }
        if (password.length < 4) {
             // Basic length check
             passError.innerText = "Password too short.";
             isValid = false;
        }

        return isValid;
    }
</script>
<!-- <script src="../assets/js/main.js"></script> -->
<?php include '../includes/footer.php'; ?>
</body>
</html>
