<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['add'])) {
    $name = $_POST['name'];
    $college_name = $_POST['college_name'];
    $roll_no = $_POST['roll_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    
    // basic insecure file upload
    $photo = "";
    if(isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != "") {
        $photo = $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }
    
    // Create login account
    $password = $roll_no; // password is roll_no initially
    mysqli_query($conn, "INSERT INTO users(name, email, password, role) VALUES ('$name', '$email', '$password', 'student')");
    
    // Default due date one month from registration
    $due_date = date("Y-m-d", strtotime("+1 month"));
    
    // Insert student
    $sql = "INSERT INTO students (name, college_name, roll_no, email, phone, gender, fee_due_date, fee_due_amount, photo) VALUES ('$name', '$college_name', '$roll_no', '$email', '$phone', '$gender', '$due_date', 5000, '$photo')";
    if(mysqli_query($conn, $sql)) {
        $msg = "Student Added Successfully!";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student — Nestify</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">
    <a href="../index.php" style="text-decoration:none;"><h1>⬡ Nestify</h1></a>
    <div class="header-right">
        <a href="https://github.com/iamziyan/Nestify" class="github-btn" target="_blank">
            <svg viewBox="0 0 16 16"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
            <span>GitHub</span>
        </a>
    </div>
</div>

<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="avatar"><?php echo substr($_SESSION['name'], 0, 1); ?></div>
            <div class="name"><?php echo $_SESSION['name']; ?></div>
            <div class="role"><?php echo $_SESSION['role']; ?></div>
        </div>
        <nav class="sidebar-nav">
            <a href="../dashboard.php"><span class="nav-icon">📊</span> Dashboard</a>
            <div class="sidebar-divider"></div>
            <?php if($_SESSION['role'] == 'admin') { ?>
                <a href="../admin/add_student.php" class="active"><span class="nav-icon">➕</span> Add Student</a>
                <a href="../admin/view_students.php"><span class="nav-icon">👥</span> View Students</a>
                <a href="../admin/add_room.php"><span class="nav-icon">🏠</span> Add Room</a>
                <a href="../admin/allocate_room.php"><span class="nav-icon">🔑</span> Allocate Room</a>
                <a href="../admin/view_requests.php"><span class="nav-icon">📩</span> Room Requests</a>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../admin/add_notice.php"><span class="nav-icon">📢</span> Add Notice</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📋</span> View Notices</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> View Complaints</a>
            <?php } else { ?>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../student/pay_fee.php"><span class="nav-icon">💳</span> Pay Fee</a>
                <a href="../student/add_complaint.php"><span class="nav-icon">✍️</span> Submit Complaint</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> My Complaints</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📢</span> Notices</a>
            <?php } ?>
            <div class="sidebar-divider"></div>
            <a href="../logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2 class="fade-in">Add New Student</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Register a new student into the hostel system.</p>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--success); padding: 16px; color: var(--success);">
                ✅ <?php echo $msg; ?>
            </div>
        <?php } ?>

        <div class="glass-card fade-in fade-in-delay-2" style="max-width: 800px;">
            <form method="post" action="" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>College Name</label>
                        <input type="text" name="college_name" placeholder="MIT Engineering College" required>
                    </div>
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" name="roll_no" placeholder="2024BCA001" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="student@college.com" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="+91 98765 43210" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="Boy">Boy</option>
                            <option value="Girl">Girl</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Student Photo</label>
                        <input type="file" name="photo" style="padding: 10px; border: 1px dashed var(--border-color); background: rgba(255,255,255,0.05);">
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <input type="submit" name="add" value="Add Student" class="btn btn-primary" style="width: 200px;">
                </div>
            </form>
            
            <div style="margin-top: 30px; padding: 15px; background: rgba(56, 189, 248, 0.05); border-radius: 8px; border: 1px solid rgba(56, 189, 248, 0.2); font-size: 13px; color: var(--text-secondary);">
                <span style="color: var(--accent); font-weight: 600;">ℹ️ System Note:</span> 
                User account will be created automatically. Initial login credential: 
                <strong>Username:</strong> [Roll No], <strong>Password:</strong> [Roll No].
            </div>
        </div>
    </main>
</div>

</body>
</html>
