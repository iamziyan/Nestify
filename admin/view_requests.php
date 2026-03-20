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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Requests — Nestify</title>
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
                <a href="../admin/view_requests.php" class="active"><span class="nav-icon">📩</span> Room Requests</a>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../admin/add_notice.php"><span class="nav-icon">📢</span> Add Notice</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📋</span> View Notices</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> View Complaints</a>
            <?php } else { ?>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../student/pay_fee.php"><span class="nav-icon">💳</span> Pay Fee</a>
                <a href="../student/add_complaint.php"><span class="nav-icon">✍️</span> Submit Complaint</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> My Complaints</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📢</span> Notices</a>
            <?php } ?>
            <div class="sidebar-divider"></div>
            <a href="../logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2 class="fade-in">Process Room Requests</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Review and approve student accommodation requests.</p>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--success); padding: 16px; color: var(--success); background: rgba(16, 185, 129, 0.05);">
                ✅ <?php echo $msg; ?>
            </div>
        <?php } ?>
        
        <?php if(isset($error)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--danger); padding: 16px; color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                ❌ <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="glass-card fade-in fade-in-delay-2 p-0 overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:80px;">Req ID</th>
                        <th>Student</th>
                        <th style="width:120px;">Requested Room</th>
                        <th style="width:100px;">Beds Left</th>
                        <th style="width:130px;">Request Date</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:180px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td style='color:var(--text-secondary); font-family:monospace;'>#".$row['req_id']."</td>";
                        echo "<td>
                                <div style='font-weight:600;'>".$row['student_name']."</div>
                                <div style='font-size:11px; color:var(--text-secondary);'>".$row['roll_no']."</div>
                              </td>";
                        echo "<td><strong style='color:var(--accent);'>Room ".$row['room_no']."</strong></td>";
                        echo "<td style='font-weight:600;'>".$row['available_beds']."</td>";
                        echo "<td style='font-size:12px; color:var(--text-secondary);'>".$row['request_date']."</td>";
                        
                        $status = $row['status'];
                        $badge_class = ($status == 'Pending') ? 'badge-success' : 'badge-warning';
                        echo "<td><span class='badge $badge_class'>$status</span></td>";

                        echo "<td style='text-align:center;'>";
                        echo "<form method='post' action='' style='margin:0; display:flex; gap:5px; justify-content:center;'>";
                        echo "<input type='hidden' name='req_id' value='".$row['req_id']."'>";
                        echo "<input type='hidden' name='action_type' id='action_type_".$row['req_id']."' value=''>";
                        
                        if($row['available_beds'] > 0) {
                            echo "<button type='submit' name='action_request' value='true' onclick='document.getElementById(\"action_type_".$row['req_id']."\").value=\"approve\";' class='btn btn-success' style='padding:5px 10px; font-size:11px;'>Approve</button>";
                        } else {
                            echo "<button type='button' disabled class='btn' style='background:rgba(255,255,255,0.05); color:var(--text-secondary); cursor:not-allowed; padding:5px 10px; font-size:11px;'>Full</button>";
                        }
                        
                        echo "<button type='submit' name='action_request' value='true' onclick='document.getElementById(\"action_type_".$row['req_id']."\").value=\"reject\";' class='btn btn-danger' style='padding:5px 10px; font-size:11px;'>Reject</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding:40px; color:var(--text-secondary);'>No pending room requests found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>

</body>
</html>
