<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
?>
<html>
<head>
<title>View Notices</title>
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
        <h2>Announcements & Notices</h2>
        <hr>
        
        <table border="1" cellpadding="10" bgcolor="white" width="80%">
            <tr bgcolor="#cccccc">
                <th>Date</th>
                <th>Title</th>
                <th>Content</th>
            </tr>
            <?php
            $query = "SELECT * FROM notices ORDER BY id DESC";
            $res = mysqli_query($conn, $query);
            if($res) {
                while($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>".$row['date']."</td>";
                    echo "<td><b>".$row['title']."</b></td>";
                    echo "<td>".$row['content']."</td>";
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
