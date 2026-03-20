<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['action_request'])) {
    $req_id = $_POST['req_id'];
    $action = $_POST['action_type']; // 'approve' or 'reject'
    
    // Fetch request details
    $reqQ = mysqli_query($conn, "SELECT * FROM room_requests WHERE id='$req_id'");
    $reqRow = mysqli_fetch_array($reqQ);
    
    if($reqRow && in_array($reqRow['status'], array('Pending', 'Waitlisted'))) {
        $student_id = $reqRow['student_id'];
        $room_id = $reqRow['room_id'];
        
        if($action == 'approve') {
            // Re-verify room has space
            $rQ = mysqli_query($conn, "SELECT available_beds FROM rooms WHERE id='$room_id'");
            $rRow = mysqli_fetch_array($rQ);
            
            if($rRow['available_beds'] > 0) {
                // Execute Approval
                mysqli_query($conn, "UPDATE room_requests SET status='Approved' WHERE id='$req_id'");
                mysqli_query($conn, "UPDATE students SET room_id='$room_id', room_assigned_date=CURRENT_DATE, room_expiry_date=DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR) WHERE id='$student_id'");
                mysqli_query($conn, "UPDATE rooms SET available_beds=available_beds-1 WHERE id='$room_id'");
                $msg = "Request #$req_id Approved Successfully.";
            } else {
                $error = "Cannot approve! No beds left in Room $room_id. Student remains Waitlisted.";
            }
        } elseif($action == 'reject') {
            mysqli_query($conn, "UPDATE room_requests SET status='Rejected' WHERE id='$req_id'");
            $msg = "Request #$req_id Rejected.";
        }
    }
}

// Fetch all Pending or Waitlisted requests with student/room joins
$query = "
    SELECT rr.id as req_id, rr.status, rr.request_date,
           s.name as student_name, s.roll_no,
           r.room_no, r.available_beds, rr.room_id
    FROM room_requests rr
    JOIN students s ON rr.student_id = s.id
    JOIN rooms r ON rr.room_id = r.id
    WHERE rr.status IN ('Pending', 'Waitlisted')
    ORDER BY rr.request_date ASC
";
$result = mysqli_query($conn, $query);
?>
<html>
<head>
<title>Room Requests - Admin Dashboard</title>
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
        <a href="add_student.php">Add Student</a>
        <a href="view_students.php">View Students</a>
        <a href="add_room.php">Add Room</a>
        <a href="allocate_room.php">Allocate Room</a>
        <a href="view_requests.php">Room Requests</a>
        <a href="../shared/view_rooms.php">View Rooms</a>
        <a href="add_notice.php">Add Notice</a>
        <a href="../shared/view_notices.php">View Notices</a>
        <a href="../shared/view_complaint.php">View All Complaints</a>
        <a href="../logout.php">Logout</a>
    </td>
    <td class="content-table" valign="top">
        <h2>Process Room Requests</h2>
        <hr>
        <?php 
        if(isset($msg)) echo "<font color='green'><b>$msg</b></font><br><br>"; 
        if(isset($error)) echo "<font color='red'><b>$error</b></font><br><br>"; 
        ?>
        <table border="1" cellpadding="10" bgcolor="white" width="100%">
            <tr bgcolor="#cccccc">
                <th>Req ID</th>
                <th>Student</th>
                <th>Roll No</th>
                <th>Requested Room</th>
                <th>Beds Left</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            if($result) {
                while($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>#".$row['req_id']."</td>";
                    echo "<td>".$row['student_name']."</td>";
                    echo "<td>".$row['roll_no']."</td>";
                    echo "<td>".$row['room_no']."</td>";
                    echo "<td>".$row['available_beds']."</td>";
                    echo "<td>".$row['request_date']."</td>";
                    
                    if($row['status'] == 'Pending') {
                        echo "<td bgcolor='lightgreen'><font color='green'>".$row['status']."</font></td>";
                    } else {
                        echo "<td bgcolor='yellow'><font color='red'>".$row['status']."</font></td>";
                    }

                    echo "<td>";
                    echo "<form method='post' action='' style='margin:0;'>";
                    echo "<input type='hidden' name='req_id' value='".$row['req_id']."'>";
                    if($row['available_beds'] > 0) {
                        echo "<button type='submit' name='action_request' value='true' onclick='this.form.action_type.value=\"approve\";' style='background-color:#10b981; color:white; border:none; padding:5px 10px; cursor:pointer;'>Approve</button> ";
                    } else {
                        echo "<button type='button' disabled style='background-color:#ccc; color:#666; border:none; padding:5px 10px;'>No Beds</button> ";
                    }
                    echo "<button type='submit' name='action_request' value='true' onclick='this.form.action_type.value=\"reject\";' style='background-color:#ef4444; color:white; border:none; padding:5px 10px; cursor:pointer;'>Reject</button>";
                    echo "<input type='hidden' name='action_type' value=''>";
                    echo "</form>";
                    echo "</td>";
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
