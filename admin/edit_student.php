<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }



if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
    $student = mysqli_fetch_array($query);
}

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $college = $_POST['college_name'];
    $roll = $_POST['roll_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_rent = $_POST['room_rent'];
    $mess_fee = $_POST['mess_fee'];
    $maintenance_fee = $_POST['maintenance_fee'];
    $fee_due_amount = $room_rent + $mess_fee + $maintenance_fee;

    $sql = "UPDATE students SET name='$name', college_name='$college', roll_no='$roll', email='$email', phone='$phone', room_rent='$room_rent', mess_fee='$mess_fee', maintenance_fee='$maintenance_fee', fee_due_amount='$fee_due_amount' WHERE id='$id'";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: view_students.php?msg=Updated");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student — Nestify</title>
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
                <a href="../admin/add_student.php"><span class="nav-icon">➕</span> Add Student</a>
                <a href="../admin/view_students.php" class="active"><span class="nav-icon">👥</span> View Students</a>
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
        <h2 class="fade-in">Modify Student Profile</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Update existing student details and fee structures.</p>

        <?php if(isset($error)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--danger); padding: 16px; color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                ❌ <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="glass-card fade-in fade-in-delay-2" style="max-width: 800px;">
            <form method="post" action="">
                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>College Name</label>
                        <input type="text" name="college_name" value="<?php echo $student['college_name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" name="roll_no" value="<?php echo $student['roll_no']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo $student['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo $student['phone']; ?>" required>
                    </div>
                </div>

                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px; color: var(--accent);">Fee Structure</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Room Rent (Rs)</label>
                            <input type="number" name="room_rent" value="<?php echo isset($student['room_rent']) ? $student['room_rent'] : 2000; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mess Fee (Rs)</label>
                            <input type="number" name="mess_fee" value="<?php echo isset($student['mess_fee']) ? $student['mess_fee'] : 2500; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Maintenance (Rs)</label>
                            <input type="number" name="maintenance_fee" value="<?php echo isset($student['maintenance_fee']) ? $student['maintenance_fee'] : 500; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4" style="display: flex; gap: 10px;">
                    <input type="submit" name="update" value="Save Changes" class="btn btn-primary" style="flex: 1;">
                    <a href="view_students.php" class="btn btn-secondary" style="text-decoration: none; padding: 12px 25px;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
</body>
</html>
