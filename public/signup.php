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
        var username = document.getElementById('username').value.trim();
        var password = document.getElementById('password').value;
        var confirm = document.getElementById('confirm').value;
        var phoneError = document.getElementById('phoneError');
        var userError = document.getElementById('userError');
        var passError = document.getElementById('passError');
        
        // Reset errors
        phoneError.innerText = "";
        userError.innerText = "";
        passError.innerText = "";
        phoneError.style.color = 'red';
        passError.style.color = 'red';
        
        var isValid = true;

        // 1. Phone Validation
        if (!/^\d+$/.test(phone)) {
            phoneError.innerText = "Only numbers allowed.";
            isValid = false;
        } else if (phone.length > 10) {
            alert("Phone number cannot be more than 10 digits!");
            phoneError.innerText = "Max 10 digits allowed.";
            isValid = false;
        } else if (phone.length !== 10) {
            phoneError.innerText = "Must be exactly 10 digits.";
            isValid = false;
        } else if (!phone.startsWith("98") && !phone.startsWith("97")) {
            phoneError.innerText = "Must start with 98 or 97.";
            isValid = false;
        }

        // 2. Username Validation
        if (/^\d/.test(username)) {
            userError.innerText = "Cannot start with a number.";
            isValid = false;
        }
        if (username.length < 3) {
            userError.innerText = "Must be at least 3 characters.";
            isValid = false;
        }

        // 3. Password Validation (Strong/Weak)
        if (password.length < 6) {
            passError.innerText = "Weak Password: Must be at least 6 characters.";
            isValid = false;
        } else {
            // Check complexity
            var hasUpper = /[A-Z]/.test(password);
            var hasLower = /[a-z]/.test(password);
            var hasNum = /\d/.test(password);
            var hasSpl = /[!@#$%^&*]/.test(password);

            if (!hasUpper || !hasLower || !hasNum || !hasSpl) {
                passError.style.color = 'orange';
                passError.innerText = "Medium Strength: Add Upper, Lower, Number & Symbol for Strong.";
                // We allow medium but warn. If user insists on strict, uncommment below:
                // isValid = false; 
            } else {
                 passError.style.color = 'green';
                 passError.innerText = "Strong Password!";
            }
        }

        if (password !== confirm) {
            alert("Passwords do not match!");
            isValid = false;
        }

        return isValid;
    }

    // Real-time phone check
    document.getElementById('phone').addEventListener('input', function(e) {
        if (this.value.length > 10) {
            document.getElementById('phoneError').innerText = "Max 10 digits! Please check.";
            alert("You have entered more than 10 digits!");
            this.value = this.value.slice(0, 10);
        } else {
             document.getElementById('phoneError').innerText = "";
        }
    });
</script>
<!-- <script src="../assets/js/main.js"></script> -->
<?php include '../includes/footer.php'; ?>
</body>
</html>
