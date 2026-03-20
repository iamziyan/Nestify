<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'student') { die("Only students can pay fees!"); }

if(isset($_POST['pay'])) {
    $date = date("Y-m-d");
    $email = $_SESSION['email'];
    $stQuery = mysqli_query($conn, "SELECT id, fee_due_amount, room_id, room_rent, mess_fee, maintenance_fee FROM students WHERE email='$email'");
    $stRow = mysqli_fetch_array($stQuery);
    
    if($stRow) {
        $st_id = $stRow['id'];
        $amount_due = $stRow['fee_due_amount'];
        $room_rent = isset($stRow['room_rent']) ? $stRow['room_rent'] : 0;
        $mess_fee = isset($stRow['mess_fee']) ? $stRow['mess_fee'] : 0;
        $maintenance_fee = isset($stRow['maintenance_fee']) ? $stRow['maintenance_fee'] : 0;
        
        if ($amount_due > 0) {
            $sql = "INSERT INTO fees (student_id, amount, date, room_rent, mess_fee, maintenance_fee, total_amount) VALUES ('$st_id', '$amount_due', '$date', '$room_rent', '$mess_fee', '$maintenance_fee', '$amount_due')";
            if(mysqli_query($conn, $sql)) {
                $receipt_id = mysqli_insert_id($conn);
                // Update student balance and optionally renew room tenure
                $update_q = "UPDATE students SET fee_due_amount = 0, fee_due_date = DATE_ADD(fee_due_date, INTERVAL 1 YEAR)";
                if($stRow['room_id'] > 0) {
                    $update_q .= ", room_expiry_date = DATE_ADD(CURRENT_DATE, INTERVAL 1 YEAR)";
                }
                $update_q .= " WHERE id='$st_id'";
                mysqli_query($conn, $update_q);
                
                $msg = "Fee of Rs $amount_due Paid Successfully! Account status active for 1 year.";
                $pdf_msg = "Receipt Generated. <a href='view_receipt.php?id=$receipt_id'>View Receipt</a>";
            } else {
                $msg = "Payment Failed!";
            }
        } else {
            $msg = "No fees are currently due!";
        }
    }
}

// Fetch current student info for display
$email = $_SESSION['email'];
$stQueryInfo = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");
$studentInfo = mysqli_fetch_array($stQueryInfo);
?>
<html>
<head>
<title>Pay Fee</title>
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
        <h2>Pay Hostel Fee</h2>
        <hr>
        <?php 
        if(isset($msg)) { 
            echo "<font color='green'><b>$msg</b></font><br>"; 
            if(isset($pdf_msg)) echo "<font color='blue'><i>$pdf_msg</i></font><br>";
            echo "<br>";
        }
        ?>
        
        <h3>Current Dues</h3>
        <table border="1" cellpadding="10" bgcolor="white" width="400">
            <tr><td>Room Rent:</td><td>Rs <?php echo isset($studentInfo['room_rent']) ? $studentInfo['room_rent'] : 0; ?></td></tr>
            <tr><td>Mess Fee:</td><td>Rs <?php echo isset($studentInfo['mess_fee']) ? $studentInfo['mess_fee'] : 0; ?></td></tr>
            <tr><td>Maintenance:</td><td>Rs <?php echo isset($studentInfo['maintenance_fee']) ? $studentInfo['maintenance_fee'] : 0; ?></td></tr>
            <tr><td><b>Total Due Amount:</b></td><td><font color="red"><b>Rs <?php echo $studentInfo['fee_due_amount']; ?></b></font></td></tr>
            <tr><td><b>Due Date:</b></td><td><b><?php echo $studentInfo['fee_due_date']; ?></b></td></tr>
            <tr>
                <td colspan="2" align="center">
                    <?php if($studentInfo['fee_due_amount'] > 0) { ?>
                        <form method="post" action="">
                            <input type="submit" name="pay" value="Pay Now (One-Click)" style="background-color: lightgreen; padding: 10px; font-weight: bold; cursor: pointer;">
                        </form>
                    <?php } else { ?>
                        <font color="green"><b>All Clear! No fees due at this moment.</b></font>
                    <?php } ?>
                </td>
            </tr>
        </table>
        
        <br><br>
        <h3>Payment History</h3>
        <table border="1" cellpadding="5" bgcolor="white" width="100%">
            <tr bgcolor="#cccccc">
                <th>Amount Paid</th>
                <th>Payment Date</th>
                <th>Action</th>
            </tr>
            <?php
            $st_id = $studentInfo['id'];
            $historyQ = "SELECT * FROM fees WHERE student_id='$st_id' ORDER BY date DESC, id DESC";
            $res = mysqli_query($conn, $historyQ);
            if($res) {
                while($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>Rs ".$row['amount']."</td>";
                    echo "<td>".$row['date']."</td>";
                    echo "<td><a href='view_receipt.php?id=".$row['id']."'>View Receipt</a></td>";
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
