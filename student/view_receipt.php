<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'student' && $_SESSION['role'] != 'admin') { die("Access Denied"); }

if(!isset($_GET['id'])) { die("Invalid Receipt ID"); }
$receipt_id = (int)$_GET['id'];

$query = "SELECT f.*, s.name, s.roll_no, s.college_name, s.room_id 
          FROM fees f 
          JOIN students s ON f.student_id = s.id 
          WHERE f.id = $receipt_id";
$res = mysqli_query($conn, $query);
$receipt = mysqli_fetch_array($res);

if(!$receipt) { die("Receipt not found."); }

// Ensure a student can only view their own receipt (unless admin)
if($_SESSION['role'] == 'student') {
    $email = $_SESSION['email'];
    $stQ = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
    $stR = mysqli_fetch_array($stQ);
    if(!$stR || $stR['id'] != $receipt['student_id']) {
        die("Unauthorized");
    }
}
?>
<html>
<head>
<title>Hostel Fee Receipt</title>
<style>
body { font-family: Arial, sans-serif; background-color: #f0f8ff; }
.receipt-container {
    width: 600px;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.header-text { text-align: center; }
.header-text h1 { margin: 0; color: #333; }
.header-text p { margin: 5px 0; color: #666; }
.details-table, .fee-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
.details-table td { padding: 5px; }
.fee-table th, .fee-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}
.fee-table th { background-color: #f5f5f5; }
.total-row { font-weight: bold; background-color: #f9f9f9; }
.footer-text { margin-top: 40px; text-align: center; color: #888; font-size: 12px; }
.back-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 3px;
}
</style>
</head>
<body>

<div class="receipt-container">
    <div class="header-text">
        <h1>Nestify</h1>
        <p>Official Fee Receipt</p>
        <p>Receipt No: #<?php echo str_pad($receipt['id'], 6, '0', STR_PAD_LEFT); ?></p>
        <p>Date: <?php echo date('d M Y', strtotime($receipt['date'])); ?></p>
    </div>
    
    <hr>
    
    <table class="details-table">
        <tr>
            <td><b>Student Name:</b></td><td><?php echo $receipt['name']; ?></td>
            <td><b>Roll No:</b></td><td><?php echo $receipt['roll_no']; ?></td>
        </tr>
        <tr>
            <td><b>College:</b></td><td><?php echo $receipt['college_name']; ?></td>
            <td><b>Room No:</b></td><td><?php echo ($receipt['room_id'] > 0) ? $receipt['room_id'] : "N/A"; ?></td>
        </tr>
    </table>
    
    <table class="fee-table">
        <tr>
            <th>S.No</th>
            <th>Particulars</th>
            <th style="text-align: right;">Amount (Rs)</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Room Rent</td>
            <td style="text-align: right;"><?php echo isset($receipt['room_rent']) ? $receipt['room_rent'] : 0; ?></td>
        </tr>
        <tr>
            <td>2</td>
            <td>Mess Fee</td>
            <td style="text-align: right;"><?php echo isset($receipt['mess_fee']) ? $receipt['mess_fee'] : 0; ?></td>
        </tr>
        <tr>
            <td>3</td>
            <td>Maintenance Fee</td>
            <td style="text-align: right;"><?php echo isset($receipt['maintenance_fee']) ? $receipt['maintenance_fee'] : 0; ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="2" style="text-align: right;">Total Amount Paid</td>
            <td style="text-align: right;">Rs <?php echo isset($receipt['total_amount']) && $receipt['total_amount'] > 0 ? $receipt['total_amount'] : $receipt['amount']; ?></td>
        </tr>
    </table>
    
    <div class="footer-text">
        <p>This is a computer-generated receipt.</p>
    </div>
    
    <center>
        <a href="javascript:history.back()" class="back-btn">Go Back</a>
    </center>
</div>

</body>
</html>
