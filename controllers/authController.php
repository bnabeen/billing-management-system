<?php
session_start();
require "../config/db.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ------------------------ SIGNUP ------------------------
    if (isset($_POST['signup'])) {

        $role = $_POST['role']; // 'owner' or 'staff'
        $phone = $_POST['phone'];
        $username = $_POST['username'];
        $pass = $_POST['password'];
        $confirm = $_POST['confirm'];
        $biz_username = $_POST['business_username'];
        
        // Basic Validation
        if ($pass !== $confirm) {
            echo "<script>alert('Passwords do not match'); window.location='../public/signup.php';</script>";
            exit();
        }

        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $business_id = null;
        $status = 'active'; // Default active for owner

        if ($role === 'owner') {
            $biz_name = $_POST['business_name'];
            
            // Check if business username exists
            $check = mysqli_query($conn, "SELECT id FROM businesses WHERE username = '$biz_username'");
            if (mysqli_num_rows($check) > 0) {
                 echo "<script>alert('Business Username already exists. Please choose another.'); window.location='../public/signup.php';</script>";
                 exit();
            }

            // Create Business
            $q_biz = "INSERT INTO businesses (username, name, phone) VALUES ('$biz_username', '$biz_name', '$phone')";
            if (mysqli_query($conn, $q_biz)) {
                $business_id = mysqli_insert_id($conn);
            } else {
                echo "<script>alert('Error creating business: " . mysqli_error($conn) . "'); window.location='../public/signup.php';</script>";
                exit();
            }

        } else {
            // Staff
            $status = 'pending'; // Staff needs approval
            
            // Find Business
            $check = mysqli_query($conn, "SELECT id FROM businesses WHERE username = '$biz_username'");
            if (mysqli_num_rows($check) == 1) {
                $row = mysqli_fetch_assoc($check);
                $business_id = $row['id'];
            } else {
                echo "<script>alert('Business not found with username: $biz_username'); window.location='../public/signup.php';</script>";
                exit();
            }
        }

        // Create User
        // Check duplicate user in business
        $check_user = mysqli_query($conn, "SELECT id FROM users WHERE business_id = '$business_id' AND username = '$username'");
        if (mysqli_num_rows($check_user) > 0) {
             echo "<script>alert('Username already taken in this business.'); window.location='../public/signup.php';</script>";
             exit();
        }

        $query = "INSERT INTO users (business_id, phone, username, password, role, status) VALUES ('$business_id', '$phone', '$username', '$hashed', '$role', '$status')";
        
        if (mysqli_query($conn, $query)) {
            if ($role === 'owner') {
                echo "<script>alert('Business registered successfully! Please login.'); window.location='../public/index.php';</script>";
            } else {
                echo "<script>alert('Account created! Please wait for owner approval.'); window.location='../public/index.php';</script>";
            }
        } else {
            echo "<script>alert('Signup failed: " . mysqli_error($conn) . "'); window.location='../public/signup.php';</script>";
        }
    }


    // ------------------------ LOGIN ------------------------
    else {

        $biz_username = $_POST['business_username']; // Business ID
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Find Business First
        $res_biz = mysqli_query($conn, "SELECT id FROM businesses WHERE username = '$biz_username'");
        if (mysqli_num_rows($res_biz) == 0) {
            echo "<script>alert('Business not found'); window.location='../public/index.php';</script>";
            exit();
        }
        $biz_row = mysqli_fetch_assoc($res_biz);
        $business_id = $biz_row['id'];

        // Find User in that Business
        $query = "SELECT * FROM users WHERE business_id = '$business_id' AND username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                
                if ($user['status'] !== 'active') {
                    echo "<script>alert('Your account is pending approval from the Business Owner.'); window.location='../public/index.php';</script>";
                    exit();
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['business_id'] = $user['business_id'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: ../public/dashboard.php");
                exit();
            } else {
                 // error_log("Login failed: Password verify failed for user $username");
            }
        } else {
             // error_log("Login failed: User $username not found");
        }

        echo "<script>alert('Invalid username or password'); window.location='../public/index.php';</script>";
    }
}
