<?php
session_start();
include('includes/config.php');

if(isset($_POST['register'])) {
    $name = $_POST['name'];
    $college_name = $_POST['college_name'];
    $roll_no = $_POST['roll_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    
    // Default due date one month from registration
    $due_date = date("Y-m-d", strtotime("+1 month"));
    
    // Check if user already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if($check && mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        // Insert into users
        $query1 = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
        mysqli_query($conn, $query1);
        
        // Insert into students
        $query2 = "INSERT INTO students (name, college_name, roll_no, email, phone, gender, fee_due_date, fee_due_amount, room_id, photo) VALUES ('$name', '$college_name', '$roll_no', '$email', '$phone', '$gender', '$due_date', 5000, 0, '')";
        mysqli_query($conn, $query2);
        
        $success = "Registration successful! You can now login.";
    }
}
?>
<html>
<head>
<title>Register - Nestify</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body bgcolor="#f0f8ff">

<div class="header">
    <h1>Nestify</h1>
</div>

<center>
    <br><br><br>
    <h2>Student Registration</h2>
    <form method="post" action="">
        <table border="1" cellpadding="10" cellspacing="0" bgcolor="white" width="400">
            <tr>
                <td colspan="2" align="center" bgcolor="#cccccc"><b>Register Here</b></td>
            </tr>
            <tr>
                <td><b>Full Name:</b></td>
                <td><input type="text" name="name" required></td>
            </tr>
            <tr>
                <td><b>College Name:</b></td>
                <td><input type="text" name="college_name" required></td>
            </tr>
            <tr>
                <td><b>Roll No:</b></td>
                <td><input type="text" name="roll_no" required></td>
            </tr>
            <tr>
                <td><b>Email:</b></td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td><b>Phone:</b></td>
                <td><input type="text" name="phone" required></td>
            </tr>
            <tr>
                <td><b>Gender:</b></td>
                <td>
                    <select name="gender" required>
                        <option value="Boy">Boy</option>
                        <option value="Girl">Girl</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Password:</b></td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="register" value="Register Now">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    Already have an account? <a href="login.php">Login here</a>
                </td>
            </tr>
            <?php 
            if(isset($error)) { echo "<tr><td colspan='2' align='center' bgcolor='yellow'><font color='red'><b>$error</b></font></td></tr>"; }
            if(isset($success)) { echo "<tr><td colspan='2' align='center' bgcolor='lightgreen'><font color='green'><b>$success</b></font></td></tr>"; }
            ?>
        </table>
    </form>
</center>

</body>
</html>
