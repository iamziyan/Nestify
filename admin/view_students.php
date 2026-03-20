<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students — Nestify</title>
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
                <a href="../admin/view_students.php" class="active"><span class="nav-icon">👥</span> View Students</a>
                <a href="../admin/add_room.php"><span class="nav-icon">🏠</span> Add Room</a>
                <a href="../admin/allocate_room.php"><span class="nav-icon">🔑</span> Allocate Room</a>
                <a href="../admin/view_requests.php"><span class="nav-icon">📩</span> Room Requests</a>
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
        <h2 class="fade-in">Student Directory</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Comprehensive list of all registered students.</p>

        <div class="glass-card fade-in fade-in-delay-2 p-0 overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Info</th>
                        <th>College & Roll</th>
                        <th>Contact</th>
                        <th>Billing Info</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM students";
                $res = mysqli_query($conn, $query);
                if($res) {
                    while($row = mysqli_fetch_array($res)) {
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        $avatar_bg = ($row['gender'] == 'Boy') ? '#38bdf8' : '#fb7185';
                        echo "<td>
                                <div style='display:flex; align-items:center; gap:10px;'>
                                    <div style='width:32px; height:32px; border-radius:50%; background:$avatar_bg; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:12px;'>".substr($row['name'],0,1)."</div>
                                    <div>
                                        <div style='font-weight:600;'>".$row['name']."</div>
                                        <div style='font-size:11px; color:var(--text-secondary);'>".$row['gender']."</div>
                                    </div>
                                </div>
                              </td>";
                        echo "<td>
                                <div style='font-size:13px;'>".$row['college_name']."</div>
                                <div style='font-size:11px; color:var(--accent); font-weight:600;'>".$row['roll_no']."</div>
                              </td>";
                        echo "<td>
                                <div style='font-size:13px;'>".$row['email']."</div>
                                <div style='font-size:11px; color:var(--text-secondary);'>".$row['phone']."</div>
                              </td>";
                        echo "<td>
                                <div style='font-size:13px; color:var(--warning); font-weight:600;'>Rs ".$row['fee_due_amount']."</div>
                                <div style='font-size:11px; color:var(--text-secondary);'>Due: ".$row['fee_due_date']."</div>
                              </td>";
                        $room_text = ($row['room_id'] == 0) ? 'Not Allocated' : 'Room #'.$row['room_id'];
                        $room_class = ($row['room_id'] == 0) ? 'badge-danger' : 'badge-success';
                        echo "<td><span class='badge $room_class' style='font-size:10px;'>$room_text</span></td>";
                        echo "<td>
                                <a href='edit_student.php?id=".$row['id']."' class='btn btn-secondary' style='padding:5px 12px; font-size:11px;'>Edit Profile</a>
                              </td>";
                        echo "</tr>";
                    }
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
