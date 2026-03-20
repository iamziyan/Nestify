<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] == 'admin' && isset($_POST['delete_room'])) {
    $del_id = $_POST['delete_room_id'];
    // Update students assigned to this room to 0
    mysqli_query($conn, "UPDATE students SET room_id=0 WHERE room_id='$del_id'");
    // Delete room requests associated with this room
    mysqli_query($conn, "DELETE FROM room_requests WHERE room_id='$del_id'");
    // Delete the room
    if(mysqli_query($conn, "DELETE FROM rooms WHERE id='$del_id'")) {
        $msg = "Room deleted successfully!";
    } else {
        $error = "Error deleting room!";
    }
}

if(isset($_POST['request_room']) || isset($_POST['waitlist_room'])) {
    $room_id = $_POST['room_id'];
    $status = isset($_POST['request_room']) ? 'Pending' : 'Waitlisted';
    
    $email = $_SESSION['email'];
    $stQ = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
    $stR = mysqli_fetch_array($stQ);
    
    if($stR) {
        $st_id = $stR['id'];
        $date = date('Y-m-d');
        $check = mysqli_query($conn, "SELECT * FROM room_requests WHERE student_id='$st_id' AND room_id='$room_id' AND status IN ('Pending', 'Waitlisted')");
        
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO room_requests (student_id, room_id, status, request_date) VALUES ('$st_id', '$room_id', '$status', '$date')");
            $msg = "Request submitted successfully. Status: $status";
        } else {
            $error = "You already have an active request or waitlist for this room!";
        }
    }
}
?>
<html>
<head>
<title>View Rooms</title>
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
    <td class="content-table" valign="top">
        <h2>View Rooms</h2>
        <hr>
        <?php 
        if(isset($msg)) echo "<font color='green'><b>$msg</b></font><br><br>"; 
        if(isset($error)) echo "<font color='red'><b>$error</b></font><br><br>"; 
        ?>
        <table border="1" cellpadding="10" bgcolor="white" width="70%">
            <tr bgcolor="#cccccc">
                <th>Room ID</th>
                <th>Room No</th>
                <th>Gender</th>
                <th>Capacity</th>
                <th>Available Beds</th>
                <?php if($_SESSION['role'] == 'student') echo "<th>Action</th>"; ?>
                <?php if($_SESSION['role'] == 'admin') echo "<th>Action</th>"; ?>
            </tr>
            <?php
            $query = "SELECT * FROM rooms";
            $res = mysqli_query($conn, $query);
            if($res) {
                while($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['room_no']."</td>";
                    echo "<td>".$row['gender']."</td>";
                    echo "<td>".$row['capacity']."</td>";
                    echo "<td>".$row['available_beds']."</td>";
                    if($_SESSION['role'] == 'student') {
                        echo "<td>";
                        echo "<form method='post' action='' style='margin:0;'>";
                        echo "<input type='hidden' name='room_id' value='".$row['id']."'>";
                        if($row['available_beds'] > 0) {
                            echo "<input type='submit' name='request_room' value='Request Room'>";
                        } else {
                            echo "<input type='submit' name='waitlist_room' value='Notify Me' style='background-color:#f59e0b;'>";
                        }
                        echo "</form>";
                        echo "</td>";
                    }
                    if($_SESSION['role'] == 'admin') {
                        echo "<td>";
                        echo "<a href='../admin/edit_room.php?id=".$row['id']."' style='padding:5px; background:lightblue; text-decoration:none; color:black;'>Edit</a> | ";
                        echo "<form method='post' action='' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this room? All assigned students will lose their room assignment!\");'>
                                <input type='hidden' name='delete_room_id' value='".$row['id']."'>
                                <input type='submit' name='delete_room' value='Delete' style='padding:5px; background:red; color:white; border:none; cursor:pointer;'>
                              </form>";
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </td>
</tr>
</table>

</body>
</html>
