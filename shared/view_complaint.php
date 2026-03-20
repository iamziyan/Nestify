<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }

if($_SESSION['role'] == 'admin') {
    if(isset($_POST['accept'])) {
        $c_id = $_POST['complaint_id'];
        mysqli_query($conn, "UPDATE complaints SET status='Completed' WHERE id='$c_id'");
        $msg = "Complaint Marked as Completed.";
    }
    if(isset($_POST['reject'])) {
        $c_id = $_POST['complaint_id'];
        mysqli_query($conn, "UPDATE complaints SET status='Rejected' WHERE id='$c_id'");
        $msg = "Complaint Rejected.";
    }
}
?>
<html>
<head>
<title>View Complaints</title>
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
        <h2>View Complaints</h2>
        <hr>
        <?php if(isset($msg)) echo "<font color='green'><b>$msg</b></font><br>"; ?>
        <div style="margin-bottom: 15px; padding: 10px; background-color: #e6e6fa; border: 1px solid #ccc;">
            <b>Filter Categories:</b>
            <a href="view_complaint.php" style="margin-right: 15px;">All</a>
            <a href="view_complaint.php?filter=Pending" style="margin-right: 15px;">Pending / Recent</a>
            <a href="view_complaint.php?filter=Completed" style="margin-right: 15px;">Completed</a>
            <a href="view_complaint.php?filter=Rejected">Rejected</a>
        </div>
        <table border="1" cellpadding="5" bgcolor="white" width="100%">
            <tr bgcolor="#cccccc">
                <th>No.</th>
                <th>Student Name</th>
                <th>Room No</th>
                <th>Description</th>
                <th>Date</th>
                <th>Status</th>
                <?php if($_SESSION['role'] == 'admin') echo "<th>Action</th>"; ?>
            </tr>
            <?php
            $filter_sql = "";
            if(isset($_GET['filter'])) {
                $f = mysqli_real_escape_string($conn, $_GET['filter']);
                if($f == 'Pending' || $f == 'Completed' || $f == 'Rejected') {
                    $filter_sql = " AND c.status='$f' ";
                }
            }

            if($_SESSION['role'] == 'admin') {
                $query = "SELECT c.*, s.name as student_name, r.room_no 
                          FROM complaints c 
                          JOIN students s ON c.student_id = s.id 
                          LEFT JOIN rooms r ON s.room_id = r.id 
                          WHERE 1=1 $filter_sql
                          ORDER BY c.id DESC";
            } else {
                // Get student's own ID
                $email = $_SESSION['email'];
                $stQuery = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
                $stRow = mysqli_fetch_array($stQuery);
                $st_id = $stRow ? $stRow['id'] : 0;
                
                $query = "SELECT c.*, s.name as student_name, r.room_no 
                          FROM complaints c 
                          JOIN students s ON c.student_id = s.id 
                          LEFT JOIN rooms r ON s.room_id = r.id 
                          WHERE c.student_id='$st_id' $filter_sql
                          ORDER BY c.id DESC";
            }
            
            $res = mysqli_query($conn, $query);
            if($res) {
                while($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['student_name']."</td>";
                    echo "<td>".($row['room_no'] ? $row['room_no'] : 'Not Allocated')."</td>";
                    echo "<td>".$row['description']."</td>";
                    echo "<td>".$row['date']."</td>";
                    
                    if($row['status'] == 'Pending') $color = 'orange';
                    elseif($row['status'] == 'Completed' || $row['status'] == 'Accepted') $color = 'green';
                    elseif($row['status'] == 'Rejected') $color = 'red';
                    else $color = 'black';
                    
                    echo "<td><b><font color='$color'>".$row['status']."</font></b></td>";
                    
                    if($_SESSION['role'] == 'admin') {
                        echo "<td>";
                        if($row['status'] == 'Pending') {
                            echo "<form method='post' action='' style='display:inline;'>
                                    <input type='hidden' name='complaint_id' value='".$row['id']."'>
                                    <input type='submit' name='accept' value='Complete' style='background-color: lightgreen; cursor: pointer; padding: 5px;'>
                                  </form> 
                                  <form method='post' action='' style='display:inline;'>
                                    <input type='hidden' name='complaint_id' value='".$row['id']."'>
                                    <input type='submit' name='reject' value='Reject' style='background-color: #ffcccc; cursor: pointer; padding: 5px;'>
                                  </form>";
                        } else {
                            echo "-";
                        }
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
