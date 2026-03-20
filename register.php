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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Nestify</title>
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
            <h3>🎓 Student Registration</h3>
        </div>
        <div class="form-body">
            <form method="post" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="college">College Name</label>
                    <input type="text" id="college" name="college_name" placeholder="MIT Engineering College" required>
                </div>
                <div class="form-group">
                    <label for="roll">Roll Number</label>
                    <input type="text" id="roll" name="roll_no" placeholder="2024BCA001" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="student@college.com" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" placeholder="+91 98765 43210" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="Boy">Boy</option>
                        <option value="Girl">Girl</option>
                        <option value="Other">Other</option>
                    </select>
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
                
                <?php if(isset($success)) { ?>
                    <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid var(--success); padding: 12px; margin-bottom: 20px; color: #34d399; font-size: 14px; font-weight: 600;">
                        ✅ <?php echo $success; ?>
                    </div>
                <?php } ?>

                <div class="text-center">
                    <input type="submit" name="register" value="Register Now">
                </div>
            </form>
        </div>
        <div class="form-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

</body>
</html>
