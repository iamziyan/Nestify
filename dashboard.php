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
<html>
<head>
<title>Dashboard - Nestify</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body bgcolor="#f0f8ff">

<div class="header">
    <h1>Nestify</h1>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="menu-table">
        <center>
            <br><b>Welcome <?php echo $_SESSION['name']; ?></b><br>
            <font color="red">Role: <?php echo $_SESSION['role']; ?></font>
            <br><br>
        </center>
        <a href="dashboard.php">Dashboard Home</a>
        <?php if($_SESSION['role'] == 'admin') { ?>
            <a href="admin/add_student.php">Add Student</a>
            <a href="admin/view_students.php">View Students</a>
            <a href="admin/add_room.php">Add Room</a>
            <a href="admin/allocate_room.php">Allocate Room</a>
            <a href="admin/view_requests.php">Room Requests</a>
            <a href="shared/view_rooms.php">View Rooms</a>
            <a href="admin/add_notice.php">Add Notice</a>
            <a href="shared/view_notices.php">View Notices</a>
            <a href="shared/view_complaint.php">View All Complaints</a>
        <?php } else { ?>
            <a href="shared/view_rooms.php">View Rooms</a>
            <a href="student/pay_fee.php">Pay Fee</a>
            <a href="student/add_complaint.php">Submit Complaint</a>
            <a href="shared/view_complaint.php">My Complaints</a>
            <a href="shared/view_notices.php">Notices</a>
        <?php } ?>
        <a href="logout.php">Logout</a>
    </td>
    <td class="content-table" valign="top">
        <h2>Dashboard Overview</h2>
        <hr>
        
        
        <table border="1" cellpadding="10" width="60%" bgcolor="white" style="border-collapse: collapse; text-align: center;">
            <tr bgcolor="#eeeeee"><td colspan="3"><b>Complaints Overview</b></td></tr>
            <tr>
                <td><b><font color='orange'>Pending / Recent:</font></b> <br><span style='font-size: 20px;'><?php echo $pending_cnt; ?></span></td>
                <td><b><font color='green'>Completed:</font></b> <br><span style='font-size: 20px;'><?php echo $completed_cnt; ?></span></td>
                <td><b><font color='red'>Rejected:</font></b> <br><span style='font-size: 20px;'><?php echo $rejected_cnt; ?></span></td>
            </tr>
        </table>
        <br>
        
        <?php if(isset($msg)) echo "<font color='green'><b>$msg</b></font><br><br>"; ?>
        
        <?php if($_SESSION['role'] == 'student') { 
            // Fetch student room details
            $email = $_SESSION['email'];
            $stQ = mysqli_query($conn, "SELECT room_id, room_assigned_date, room_expiry_date FROM students WHERE email='$email'");
            $stRow = mysqli_fetch_array($stQ);
            
            if($stRow && $stRow['room_id'] > 0) {
                // Fetch room name
                $r_id = $stRow['room_id'];
                $rQ = mysqli_query($conn, "SELECT room_no FROM rooms WHERE id='$r_id'");
                $rRow = mysqli_fetch_array($rQ);
        ?>
            <h3>My Accommodation</h3>
            <table border="1" cellpadding="10" bgcolor="white" width="60%">
                <tr><td><b>Room Number:</b></td><td><?php echo $rRow['room_no']; ?></td></tr>
                <tr><td><b>Assigned Date:</b></td><td><?php echo $stRow['room_assigned_date']; ?></td></tr>
                <tr><td><b>Expiry Date:</b></td><td><?php echo $stRow['room_expiry_date']; ?></td></tr>
                <tr>
                    <td colspan="2" align="center">
                        <form method="post" action="" onsubmit="return confirm('Are you absolutely sure you want to leave this room? You will lose your bed.');">
                            <input type="submit" name="leave_room" value="Leave Room" style="background-color: #ef4444;">
                        </form>
                    </td>
                </tr>
            </table>
            <br>
        <?php } else { ?>
            <table border="1" cellpadding="10" width="50%" bgcolor="#e6e6fa">
                <tr><td><b>Room Status</b></td></tr>
                <tr><td>You have not been assigned a room yet. Please visit the <a href="shared/view_rooms.php">View Rooms</a> tab to request one, or wait for Admin allocation.</td></tr>
            </table>
            <br>
        <?php } } ?>

        <table border="1" cellpadding="10" width="50%" bgcolor="#e6e6fa">
            <tr><td><b>Important Info</b></td></tr>
            <tr><td>No new notifications at this time. Check back later.</td></tr>
        </table>
    </td>
</tr>
</table>

</body>
</html>
