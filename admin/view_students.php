<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }
?>
<html>
<head>
<title>View Students</title>
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
        <h2>View Students</h2>
        <hr>
        <table border="1" cellpadding="5" bgcolor="white" width="100%">
            <tr bgcolor="#cccccc">
                <th>ID</th>
                <th>Name</th>
                <th>College Name</th>
                <th>Roll No</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Fee Due</th>
                <th>Due Date</th>
                <th>Room ID</th>
                <th>Photo</th>
                <th>Action</th>
            </tr>
            <?php
            $query = "SELECT * FROM students";
            $res = mysqli_query($conn, $query);
            if($res) {
                while($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['name']."</td>";
                    echo "<td>".$row['college_name']."</td>";
                    echo "<td>".$row['roll_no']."</td>";
                    echo "<td>".$row['gender']."</td>";
                    echo "<td>".$row['email']."</td>";
                    echo "<td>".$row['phone']."</td>";
                    echo "<td>Rs ".$row['fee_due_amount']."</td>";
                    echo "<td>".$row['fee_due_date']."</td>";
                    echo "<td>".($row['room_id'] == 0 ? "Not Allocated" : $row['room_id'])."</td>";
                    echo "<td>".$row['photo']."</td>";
                    echo "<td><a href='edit_student.php?id=".$row['id']."'>Edit</a></td>";
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
