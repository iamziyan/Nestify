<?php
session_start();
include('includes/config.php');
if(!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Handle Room Leave request
if(isset($_POST['leave_room'])) {
    $email = $_SESSION['email'];
    $stu_q = mysqli_query($conn, "SELECT id, room_id FROM students WHERE email='$email'");
    $stu_r = mysqli_fetch_array($stu_q);
    
    if($stu_r && $stu_r['room_id'] > 0) {
        $old_room = $stu_r['room_id'];
        $stu_id = $stu_r['id'];
        // Remove from student
        mysqli_query($conn, "UPDATE students SET room_id=0, room_assigned_date=NULL, room_expiry_date=NULL WHERE id='$stu_id'");
        // Free bed
        mysqli_query($conn, "UPDATE rooms SET available_beds = available_beds + 1 WHERE id='$old_room'");
        $msg = "You have successfully surrendered your room.";
    }
}
// Fetch Complaint Statistics
$role = $_SESSION['role'];
$email = $_SESSION['email'];
$pending_cnt = $completed_cnt = $rejected_cnt = 0;

if($role == 'admin') {
    $q_p = mysqli_query($conn, "SELECT count(*) as cnt FROM complaints WHERE status='Pending'");
    $pending_cnt = mysqli_fetch_array($q_p)['cnt'];
    $q_c = mysqli_query($conn, "SELECT count(*) as cnt FROM complaints WHERE status='Completed' OR status='Accepted'");
    $completed_cnt = mysqli_fetch_array($q_c)['cnt'];
    $q_r = mysqli_query($conn, "SELECT count(*) as cnt FROM complaints WHERE status='Rejected'");
    $rejected_cnt = mysqli_fetch_array($q_r)['cnt'];
} else {
    $stQ = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
    $stR = mysqli_fetch_array($stQ);
    if($stR) {
        $st_id = $stR['id'];
        $q_p = mysqli_query($conn, "SELECT count(*) as cnt FROM complaints WHERE student_id='$st_id' AND status='Pending'");
        $pending_cnt = mysqli_fetch_array($q_p)['cnt'];
        $q_c = mysqli_query($conn, "SELECT count(*) as cnt FROM complaints WHERE student_id='$st_id' AND (status='Completed' OR status='Accepted')");
        $completed_cnt = mysqli_fetch_array($q_c)['cnt'];
        $q_r = mysqli_query($conn, "SELECT count(*) as cnt FROM complaints WHERE student_id='$st_id' AND status='Rejected'");
        $rejected_cnt = mysqli_fetch_array($q_r)['cnt'];
    }
}
?>
<<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Nestify</title>
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

<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="avatar"><?php echo substr($_SESSION['name'], 0, 1); ?></div>
            <div class="name"><?php echo $_SESSION['name']; ?></div>
            <div class="role"><?php echo $_SESSION['role']; ?></div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active"><span class="nav-icon">📊</span> Dashboard</a>
            <div class="sidebar-divider"></div>
            <?php if($_SESSION['role'] == 'admin') { ?>
                <a href="admin/add_student.php"><span class="nav-icon">➕</span> Add Student</a>
                <a href="admin/view_students.php"><span class="nav-icon">👥</span> View Students</a>
                <a href="admin/add_room.php"><span class="nav-icon">🏠</span> Add Room</a>
                <a href="admin/allocate_room.php"><span class="nav-icon">🔑</span> Allocate Room</a>
                <a href="admin/view_requests.php"><span class="nav-icon">📩</span> Room Requests</a>
                <a href="shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="admin/add_notice.php"><span class="nav-icon">📢</span> Add Notice</a>
                <a href="shared/view_notices.php"><span class="nav-icon">📋</span> View Notices</a>
                <a href="shared/view_complaint.php"><span class="nav-icon">⚠️</span> View Complaints</a>
            <?php } else { ?>
                <a href="shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="student/pay_fee.php"><span class="nav-icon">💳</span> Pay Fee</a>
                <a href="student/add_complaint.php"><span class="nav-icon">✍️</span> Submit Complaint</a>
                <a href="shared/view_complaint.php"><span class="nav-icon">⚠️</span> My Complaints</a>
                <a href="shared/view_notices.php"><span class="nav-icon">📢</span> Notices</a>
            <?php } ?>
            <div class="sidebar-divider"></div>
            <a href="logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2 class="fade-in">Dashboard Overview</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Welcome back, <?php echo $_SESSION['name']; ?>. Here's a summary of the hostel status.</p>

        <div class="stat-grid fade-in fade-in-delay-2">
            <div class="stat-card pending">
                <div class="stat-value"><?php echo $pending_cnt; ?></div>
                <div class="stat-label">Pending Complaints</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-value"><?php echo $completed_cnt; ?></div>
                <div class="stat-label">Resolved Issues</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-value"><?php echo $rejected_cnt; ?></div>
                <div class="stat-label">Rejected / Cancelled</div>
            </div>
        </div>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--success); padding: 16px; color: var(--success);">
                ✅ <?php echo $msg; ?>
            </div>
        <?php } ?>

        <?php if($_SESSION['role'] == 'student') { 
            $email = $_SESSION['email'];
            $stQ = mysqli_query($conn, "SELECT room_id, room_assigned_date, room_expiry_date FROM students WHERE email='$email'");
            $stRow = mysqli_fetch_array($stQ);
            
            if($stRow && $stRow['room_id'] > 0) {
                $r_id = $stRow['room_id'];
                $rQ = mysqli_query($conn, "SELECT room_no FROM rooms WHERE id='$r_id'");
                $rRow = mysqli_fetch_array($rQ);
        ?>
            <div class="glass-card fade-in fade-in-delay-3">
                <h3>🏠 My Accommodation</h3>
                <div class="data-table" style="margin-top:20px;">
                    <table>
                        <tr>
                            <td style="width:200px; color:var(--text-secondary);">Room Number</td>
                            <td style="font-weight:700; color:var(--accent);"><?php echo $rRow['room_no']; ?></td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-secondary);">Assigned Date</td>
                            <td><?php echo $stRow['room_assigned_date']; ?></td>
                        </tr>
                        <tr>
                            <td style="color:var(--text-secondary);">Expiry Date</td>
                            <td style="color:var(--warning);"><?php echo $stRow['room_expiry_date']; ?></td>
                        </tr>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <form method="post" action="" onsubmit="return confirm('Are you absolutely sure you want to leave this room? You will lose your bed.');">
                        <input type="submit" name="leave_room" value="Leave Room" class="btn btn-danger">
                    </form>
                </div>
            </div>
        <?php } else { ?>
            <div class="glass-card fade-in fade-in-delay-3" style="border-left: 4px solid var(--warning);">
                <h3>Room Status</h3>
                <p style="margin-top:10px;">You have not been assigned a room yet. Please visit the <a href="shared/view_rooms.php" style="color:var(--accent);">View Rooms</a> tab to request one, or wait for Admin allocation.</p>
            </div>
        <?php } } ?>

        <div class="glass-card fade-in fade-in-delay-4 mt-4">
            <h3>📢 Important Info</h3>
            <p style="color:var(--text-secondary); margin-top:10px;">No new notifications at this time. All systems are operational. Check the <a href="shared/view_notices.php" style="color:var(--accent);">Notices</a> section for archived updates.</p>
        </div>
    </main>
</div>

</body>
</html>
