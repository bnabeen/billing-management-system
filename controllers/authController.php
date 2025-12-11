<?php
session_start();
require "../config/db.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ------------------------ SIGNUP ------------------------
    if (isset($_POST['signup'])) {

        $phone = $_POST['phone'];
        $username = $_POST['username'];
        $pass = $_POST['password'];
        $confirm = $_POST['confirm'];

        if ($pass !== $confirm) {
            echo "<script>alert('Passwords do not match'); window.location='../public/signup.php';</script>";
            exit();
        }

        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (phone, username, password) VALUES ('$phone', '$username', '$hashed')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Account created successfully!'); window.location='../public/dashboard.php';</script>";
        } else {
            echo "<script>alert('Signup failed'); window.location='../public/signup.php';</script>";
        }
    }


    // ------------------------ LOGIN ------------------------
    else {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../public/dashboard.php");
                exit();
            } else {
                 error_log("Login failed: Password verify failed for user $username");
            }
        } else {
             error_log("Login failed: User $username not found");
        }

        echo "<script>alert('Invalid username or password'); window.location='../public/index.php';</script>";
    }
}
