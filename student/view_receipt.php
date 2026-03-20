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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo str_pad($receipt['id'], 6, '0', STR_PAD_LEFT); ?> — Nestify</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .receipt-card {
            max-width: 700px;
            margin: 40px auto;
            position: relative;
            overflow: hidden;
        }
        .receipt-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--accent-hover));
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 25px;
        }
        .receipt-logo h1 {
            color: var(--accent);
            margin: 0;
            font-size: 28px;
        }
        .receipt-info {
            text-align: right;
        }
        .receipt-info div {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 3px;
        }
        .receipt-info .receipt-number {
            font-family: monospace;
            font-size: 16px;
            color: var(--text-primary);
            font-weight: 700;
        }
        .student-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-item label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }
        .detail-item span {
            font-weight: 600;
            color: var(--text-primary);
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            text-align: left;
            font-size: 12px;
            color: var(--text-secondary);
            padding: 10px;
            border-bottom: 2px solid var(--border-color);
        }
        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .total-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            padding: 15px 25px;
            border-radius: 12px;
            text-align: right;
        }
        .total-box label {
            display: block;
            font-size: 12px;
            color: var(--success);
            margin-bottom: 5px;
        }
        .total-box span {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
        }
        @media print {
            .back-btn, .header-right, .sidebar { display: none !important; }
            .receipt-card { border: 1px solid #ccc; color: black; background: white; box-shadow: none; }
            body { background: white; }
        }
    </style>
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">

    <div class="glass-card receipt-card fade-in">
        <div class="receipt-header">
            <div class="receipt-logo">
                <h1>⬡ Nestify</h1>
                <p style="font-size: 12px; color: var(--text-secondary); margin: 5px 0 0 0;">Hostel Management System</p>
            </div>
            <div class="receipt-info">
                <div>Receipt Number</div>
                <div class="receipt-number">#<?php echo str_pad($receipt['id'], 6, '0', STR_PAD_LEFT); ?></div>
                <div style="margin-top: 10px;">Date: <?php echo date('d M Y', strtotime($receipt['date'])); ?></div>
            </div>
        </div>

        <div class="student-details">
            <div class="detail-item">
                <label>Student Name</label>
                <span><?php echo $receipt['name']; ?></span>
            </div>
            <div class="detail-item">
                <label>Roll Number</label>
                <span><?php echo $receipt['roll_no']; ?></span>
            </div>
            <div class="detail-item">
                <label>College</label>
                <span><?php echo $receipt['college_name']; ?></span>
            </div>
            <div class="detail-item">
                <label>Room Assignment</label>
                <span><?php echo ($receipt['room_id'] > 0) ? "Room #".$receipt['room_id'] : "N/A"; ?></span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hostel Room Rent (Annual)</td>
                    <td style="text-align: right;">Rs <?php echo isset($receipt['room_rent']) ? $receipt['room_rent'] : 0; ?></td>
                </tr>
                <tr>
                    <td>Mess & Catering Fee</td>
                    <td style="text-align: right;">Rs <?php echo isset($receipt['mess_fee']) ? $receipt['mess_fee'] : 0; ?></td>
                </tr>
                <tr>
                    <td>Maintenance & Facility Charges</td>
                    <td style="text-align: right;">Rs <?php echo isset($receipt['maintenance_fee']) ? $receipt['maintenance_fee'] : 0; ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <label>Total Amount Paid</label>
                <span>Rs <?php echo isset($receipt['total_amount']) && $receipt['total_amount'] > 0 ? $receipt['total_amount'] : $receipt['amount']; ?></span>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center; border-top: 1px dashed var(--border-color); padding-top: 20px;">
            <p style="font-size: 12px; color: var(--text-secondary); margin: 0;">This is a system-generated electronic receipt. No signature required.</p>
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
                <a href="javascript:history.back()" class="btn btn-secondary back-btn">← Back</a>
                <button onclick="window.print()" class="btn btn-primary back-btn">🖨️ Print Receipt</button>
            </div>
        </div>
    </div>

</body>
</html>

</body>
</html>
