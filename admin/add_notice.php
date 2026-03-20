<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['add_notice'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $date = date("Y-m-d");
    
    $sql = "INSERT INTO notices (title, content, date) VALUES ('$title', '$content', '$date')";
    if(mysqli_query($conn, $sql)) {
        $msg = "Notice Added!";
    } else {
        $msg = "Error adding notice!";
    }
}
?>
<html>
<head>
<title>Add Notice</title>
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
        <h2>Add Notice</h2>
        <hr>
        <?php if(isset($msg)) echo "<font color='green'><b>$msg</b></font><br><br>"; ?>
        <form method="post" action="">
        <table border="1" cellpadding="10" bgcolor="white">
            <tr><td><b>Title:</b></td><td><input type="text" name="title" required></td></tr>
            <tr><td valign="top"><b>Content:</b></td><td><textarea name="content" rows="4" cols="30" required></textarea></td></tr>
            <tr><td colspan="2" align="center"><input type="submit" name="add_notice" value="Add Notice"></td></tr>
        </table>
        </form>
    </td>
</tr>
</table>

</body>
</html>
