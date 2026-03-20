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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Payment — Nestify</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">
    <a href="../index.php" style="text-decoration:none;"><h1>⬡ Nestify</h1></a>
    <div class="header-right">
        <a href="https://github.com/iamziyan/Nestify" class="github-btn" target="_blank">
            <svg viewBox="0 0 16 16"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
            <span>GitHub</span>
        </a>
    </div>
</div>

<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-profile">
            <div class="avatar"><?php echo substr($_SESSION['name'], 0, 1); ?></div>
            <div class="name"><?php echo $_SESSION['name']; ?></div>
            <div class="role"><?php echo $_SESSION['role']; ?></div>
        </div>
        <nav class="sidebar-nav">
            <a href="../dashboard.php"><span class="nav-icon">📊</span> Dashboard</a>
            <div class="sidebar-divider"></div>
            <?php if($_SESSION['role'] == 'admin') { ?>
                <a href="../admin/add_student.php"><span class="nav-icon">➕</span> Add Student</a>
                <a href="../admin/view_students.php"><span class="nav-icon">👥</span> View Students</a>
                <a href="../admin/add_room.php"><span class="nav-icon">🏠</span> Add Room</a>
                <a href="../admin/allocate_room.php"><span class="nav-icon">🔑</span> Allocate Room</a>
                <a href="../admin/view_requests.php"><span class="nav-icon">📩</span> Room Requests</a>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../admin/add_notice.php"><span class="nav-icon">📢</span> Add Notice</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📋</span> View Notices</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> View Complaints</a>
            <?php } else { ?>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../student/pay_fee.php" class="active"><span class="nav-icon">💳</span> Pay Fee</a>
                <a href="../student/add_complaint.php"><span class="nav-icon">✍️</span> Submit Complaint</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> My Complaints</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📢</span> Notices</a>
            <?php } ?>
            <div class="sidebar-divider"></div>
            <a href="../logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2 class="fade-in">Fee Management</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">View and settle your hostel dues securely.</p>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--success); padding: 16px; color: var(--success); background: rgba(16, 185, 129, 0.05);">
                ✅ <?php echo $msg; ?>
                <?php if(isset($pdf_msg)) echo "<br><span style='font-size:12px; opacity:0.8;'>$pdf_msg</span>"; ?>
            </div>
        <?php } ?>

        <div style="display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 25px;">
            <div class="glass-card fade-in fade-in-delay-2">
                <h3 style="margin-top:0; color:var(--text-primary); font-size:18px;">Payment Summary</h3>
                <div style="margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-color); font-size:14px;">
                        <span style="color:var(--text-secondary);">Room Rent</span>
                        <span style="font-weight:600;">Rs <?php echo isset($studentInfo['room_rent']) ? $studentInfo['room_rent'] : 0; ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-color); font-size:14px;">
                        <span style="color:var(--text-secondary);">Mess Charges</span>
                        <span style="font-weight:600;">Rs <?php echo isset($studentInfo['mess_fee']) ? $studentInfo['mess_fee'] : 0; ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-color); font-size:14px;">
                        <span style="color:var(--text-secondary);">Maintenance</span>
                        <span style="font-weight:600;">Rs <?php echo isset($studentInfo['maintenance_fee']) ? $studentInfo['maintenance_fee'] : 0; ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:15px 0; font-size:16px;">
                        <span style="font-weight:700; color:var(--text-primary);">Total Due</span>
                        <span style="font-weight:800; color:var(--warning);">Rs <?php echo $studentInfo['fee_due_amount']; ?></span>
                    </div>
                </div>
                
                <div style="padding:12px; background:rgba(255,255,255,0.03); border-radius:8px; margin-bottom:20px;">
                    <div style="font-size:12px; color:var(--text-secondary); margin-bottom:4px;">Due Date</div>
                    <div style="font-weight:600; color:var(--accent); font-size:14px;">🗓️ <?php echo $studentInfo['fee_due_date']; ?></div>
                </div>

                <?php if($studentInfo['fee_due_amount'] > 0) { ?>
                    <form method="post" action="">
                        <input type="submit" name="pay" value="Secure Payment (One-Click)" class="btn btn-primary" style="width:100%; padding:14px;">
                    </form>
                <?php } else { ?>
                    <div style="text-align:center; padding:15px; border:1px solid var(--success); border-radius:8px; color:var(--success); font-weight:600; font-size:14px;">
                        ✨ All Clear! No fees due.
                    </div>
                <?php } ?>
            </div>

            <div class="glass-card fade-in fade-in-delay-3 p-0 overflow-hidden">
                <div style="padding:20px; border-bottom:1px solid var(--border-color);">
                    <h3 style="margin:0; color:var(--text-primary); font-size:18px;">Payment History</h3>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $st_id = $studentInfo['id'];
                    $historyQ = "SELECT * FROM fees WHERE student_id='$st_id' ORDER BY date DESC, id DESC";
                    $res = mysqli_query($conn, $historyQ);
                    if($res && mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_array($res)) {
                            echo "<tr>";
                            echo "<td style='color:var(--text-secondary); font-size:13px;'>".$row['date']."</td>";
                            echo "<td style='font-weight:600; color:var(--success);'>Rs ".$row['amount']."</td>";
                            echo "<td style='text-align:right;'><a href='view_receipt.php?id=".$row['id']."' class='btn btn-secondary' style='padding:5px 12px; font-size:11px;'>View Receipt</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding:40px; color:var(--text-secondary);'>No previous payments found.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>

</body>
</html>
