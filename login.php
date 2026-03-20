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
<html>
<head>
<title>Nestify - Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body bgcolor="#f0f8ff">

<div class="header">
    <h1>Nestify</h1>
</div>

<center>
    <br><br><br>
    <h2>Login Page</h2>
    <form method="post" action="">
        <table border="1" cellpadding="10" cellspacing="0" bgcolor="white" width="300">
            <tr>
                <td colspan="2" align="center" bgcolor="#cccccc"><b>User Login</b></td>
            </tr>
            <tr>
                <td><b>Email:</b></td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td><b>Password:</b></td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="submit" value="Login">
                    <br><br>
                    Don't have an account? <a href="register.php">Register here</a>
                </td>
            </tr>
            <?php 
            if(isset($error)) { 
                echo "<tr><td colspan='2' align='center' bgcolor='yellow'><font color='red'><b>$error</b></font></td></tr>"; 
            } 
            ?>
        </table>
    </form>
    
</center>

</body>
</html>
