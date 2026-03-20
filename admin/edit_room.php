<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $room_no = $_POST['room_no'];
    $capacity = $_POST['capacity'];
    $available_beds = $_POST['available_beds'];
    $gender = $_POST['gender'];
    
    $sql = "UPDATE rooms SET room_no='$room_no', capacity='$capacity', available_beds='$available_beds', gender='$gender' WHERE id='$id'";
    if(mysqli_query($conn, $sql)) {
        $msg = "Room Updated Successfully!";
    } else {
        $msg = "Error updating room!";
    }
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM rooms WHERE id='$id'");
    $room = mysqli_fetch_array($query);
}
?>
<html>
<head>
<title>Edit Room</title>
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
        <h2>Edit Room</h2>
        <hr>
        <?php 
        if(isset($msg)) echo "<font color='purple'><b>$msg</b></font><br><br>"; 
        if(isset($room)) { 
        ?>
        <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
        <table border="1" cellpadding="10" bgcolor="white">
            <tr><td><b>Room No:</b></td><td><input type="text" name="room_no" value="<?php echo $room['room_no']; ?>" required></td></tr>
            <tr><td><b>Room Type (Gender):</b></td><td>
                <select name="gender" required>
                    <option value="Boy" <?php if($room['gender']=='Boy') echo 'selected'; ?>>Boy</option>
                    <option value="Girl" <?php if($room['gender']=='Girl') echo 'selected'; ?>>Girl</option>
                </select>
            </td></tr>
            <tr><td><b>Capacity:</b></td><td><input type="number" name="capacity" value="<?php echo $room['capacity']; ?>" required></td></tr>
            <tr><td><b>Available Beds:</b></td><td><input type="number" name="available_beds" value="<?php echo $room['available_beds']; ?>" required></td></tr>
            <tr><td colspan="2" align="center"><input type="submit" name="update" value="Update Room"></td></tr>
        </table>
        </form>
        <?php } else { echo "<font color='red'><b>Room not found!</b></font>"; } ?>
    </td>
</tr>
</table>

</body>
</html>
