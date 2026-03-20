<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['allocate'])) {
    $student_id = $_POST['student_id'];
    $room_id = $_POST['room_id'];
    
    // Check if bed available
    $roomQuery = mysqli_query($conn, "SELECT * FROM rooms WHERE id='$room_id'");
    $roomRow = mysqli_fetch_array($roomQuery);
    
    if($roomRow['available_beds'] > 0) {
        // Update student with 1 year tenure
        mysqli_query($conn, "UPDATE students SET room_id='$room_id', room_assigned_date=CURRENT_DATE, room_expiry_date=DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR) WHERE id='$student_id'");
        // Decrease beds
        $newBeds = $roomRow['available_beds'] - 1;
        mysqli_query($conn, "UPDATE rooms SET available_beds='$newBeds' WHERE id='$room_id'");
        $msg = "Room Allocated Successfully!";
    } else {
        $msg = "Error: No beds available in this room!";
    }
}
?>
<html>
<head>
<title>Allocate Room</title>
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
        <h2>Allocate Room</h2>
        <hr>
        <?php if(isset($msg)) echo "<font color='purple'><b>$msg</b></font><br><br>"; ?>
        <form method="post" action="">
        <table border="1" cellpadding="10" bgcolor="white">
            <tr>
                <td><b>Select Student:</b></td>
                <td>
                    <select name="student_id" required>
                        <option value="">--Select--</option>
                        <?php
                        $stQuery = mysqli_query($conn, "SELECT * FROM students WHERE room_id=0 OR room_id IS NULL");
                        if($stQuery) {
                            while($st = mysqli_fetch_array($stQuery)) {
                                echo "<option value='".$st['id']."'>".$st['name']." (".$st['roll_no'].")</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Select Room:</b></td>
                <td>
                    <select name="room_id" required>
                        <option value="">--Select--</option>
                        <?php
                        $rmQuery = mysqli_query($conn, "SELECT * FROM rooms WHERE available_beds > 0");
                        if($rmQuery) {
                            while($rm = mysqli_fetch_array($rmQuery)) {
                                echo "<option value='".$rm['id']."'>".$rm['room_no']." (Beds left: ".$rm['available_beds'].")</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr><td colspan="2" align="center"><input type="submit" name="allocate" value="Allocate Room"></td></tr>
        </table>
        </form>
    </td>
</tr>
</table>

</body>
</html>
