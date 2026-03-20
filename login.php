<?php
session_start();
include('includes/config.php');

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Direct string variables in query (SQL injection vulnerable)
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
        
        // Redirect to dashboard
        header("Location: dashboard.php");
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Nestify</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="header">
    <a href="index.php" style="text-decoration:none;"><h1>⬡ Nestify</h1></a>
    <div class="header-right">
        <a href="https://github.com/iamziyan/Nestify" class="github-btn" target="_blank">
            <svg viewBox="0 0 16 16"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
            <span>GitHub</span>
        </a>
    </div>
</div>

<div class="form-container fade-in">
    <div class="form-card">
        <div class="form-header">
            <h3>🔑 User Login</h3>
        </div>
        <div class="form-body">
            <form method="post" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="admin@hostel.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                
                <?php if(isset($error)) { ?>
                    <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); padding: 12px; margin-bottom: 20px; color: #f87171; font-size: 14px; font-weight: 600;">
                        ⚠️ <?php echo $error; ?>
                    </div>
                <?php } ?>

                <div class="text-center">
                    <input type="submit" name="submit" value="Login">
                </div>
            </form>
        </div>
        <div class="form-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

</body>
</html>
