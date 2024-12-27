<?php
session_start();
include 'config.php';

// Helper Functions
function hashPassword($password) {
    return hash('sha256', $password);
}

function verifyPassword($inputPassword, $storedHashedPassword) {
    return hash('sha256', $inputPassword) === $storedHashedPassword;
}

function generateTOTP() {
    return str_pad(rand(100000, 999999), 6, "0", STR_PAD_LEFT);
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];

       // Generate TOTP
        $totp = generateTOTP();
        $_SESSION['totp'] = $totp;

        echo "<body data-totp='$totp'>";  
        echo "<script src='login_modal.js'></script>";
       
    } else {
        echo "Invalid username, email, or password.";

    }
}

// Handle TOTP Verification
if (isset($_POST['action']) && $_POST['action'] === 'verify_totp') {
    $enteredTOTP = $_POST['totp'];
    if ($enteredTOTP === $_SESSION['totp']) {
        echo "TOTP verified! Access granted.<br>";
        if ($_SESSION['role'] === 'Admin') {
            header("Location: admin_page.php");
        } else {
            header("Location: user_page.php");
        }
        exit();
    } else {
        echo "Invalid TOTP. Access denied.";

    }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['reg_username'];
    $password = $_POST['reg_password'];
    $email = $_POST['reg_email'];
    $role = $_POST['role'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";

    }

    // Validate password length
    if (strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.');</script>";
    }else{
        $hashedPassword = hashPassword($password);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)");
        try {
            $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'email' => $email, 'role' => $role]);
            echo "Sign-up successful! You can now log in.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                echo "<script>alert('Username or email already exists. Please try another.');</script>";

            } else {
                echo "Error: " . $e->getMessage();
            }
        }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Projectr</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="" method="POST" class="sign-in-form">
                    <h2 class="title">Sign in</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Username or Email" name="login_username" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Password" name="login_password" required />
                    </div>
                    <input type="submit" value="Login" class="btn solid" name="login" />
                </form>

                <form action="" method="POST" class="sign-up-form">
                    <h2 class="title">Sign up</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Username" name="reg_username" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Email" name="reg_email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Password" name="reg_password" required />
                    </div>
                    <select name="role" required class="btn transparent">
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                    <input type="submit" class="btn" name="register" value="Sign up" />
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>New here?</h3>
                    <p>Sign up to start managing your account!</p>
                    <button class="btn transparent" id="sign-up-btn">Sign up</button>
                </div>
                <img src="img/register.svg" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us?</h3>
                    <p>Sign in to manage your account!</p>
                    <button class="btn transparent" id="sign-in-btn">Sign in</button>
                </div>
                <img src="img/login.svg" class="image" alt="" />
            </div>
        </div>
    </div>

    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector(".container");

        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });
    </script>
</body>
</html>
