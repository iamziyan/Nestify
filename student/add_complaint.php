<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'student') { die("Only students can add complaints!"); }

if(isset($_POST['submit_complaint'])) {
    $desc = $_POST['description'];
    $date = date("Y-m-d");
    $email = $_SESSION['email'];
    
    // Get student ID
    $stQuery = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
    $stRow = mysqli_fetch_array($stQuery);
    
    if($stRow) {
        $st_id = $stRow['id'];
        $sql = "INSERT INTO complaints (student_id, description, date, status) VALUES ('$st_id', '$desc', '$date', 'Pending')";
        if(mysqli_query($conn, $sql)) {
            $msg = "Complaint Submitted Successfully!";
        } else {
            $msg = "Error submitting complaint!";
        }
    } else {
        $msg = "Student record not found!";
    }
}
?>
<html>
<head>
<title>Lodge Complaint</title>
<link rel="stylesheet" href="../css/style.css">
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
        <a href="../dashboard.php">Dashboard Home</a>
        <?php if($_SESSION['role'] == 'admin') { ?>
            <a href="../admin/add_student.php">Add Student</a>
            <a href="../admin/view_students.php">View Students</a>
            <a href="../admin/add_room.php">Add Room</a>
            <a href="../admin/allocate_room.php">Allocate Room</a>
            <a href="../admin/view_requests.php">Room Requests</a>
            <a href="../shared/view_rooms.php">View Rooms</a>
            <a href="../admin/add_notice.php">Add Notice</a>
            <a href="../shared/view_notices.php">View Notices</a>
            <a href="../shared/view_complaint.php">View All Complaints</a>
        <?php } else { ?>
            <a href="../shared/view_rooms.php">View Rooms</a>
            <a href="../student/pay_fee.php">Pay Fee</a>
            <a href="../student/add_complaint.php">Submit Complaint</a>
            <a href="../shared/view_complaint.php">My Complaints</a>
            <a href="../shared/view_notices.php">Notices</a>
        <?php } ?>
        <a href="../logout.php">Logout</a>
    </td>
    <td class="content-table">
        <h2>Submit Complaint</h2>
        <hr>
        <?php if(isset($msg)) echo "<font color='red'><b>$msg</b></font><br><br>"; ?>
        <form method="post" action="">
        <table border="1" cellpadding="10" bgcolor="white">
            <tr>
                <td valign="top"><b>Complaint Detail:</b></td>
                <td><textarea name="description" rows="5" cols="40" required></textarea></td>
            </tr>
            <tr><td colspan="2" align="center"><input type="submit" name="submit_complaint" value="Submit"></td></tr>
        </table>
        </form>
    </td>
</tr>
</table>

</body>
</html>
