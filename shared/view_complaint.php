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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints — Nestify</title>
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
                <a href="../shared/view_complaint.php" class="active"><span class="nav-icon">⚠️</span> View Complaints</a>
            <?php } else { ?>
                <a href="../shared/view_rooms.php"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../student/pay_fee.php"><span class="nav-icon">💳</span> Pay Fee</a>
                <a href="../student/add_complaint.php"><span class="nav-icon">✍️</span> Submit Complaint</a>
                <a href="../shared/view_complaint.php" class="active"><span class="nav-icon">⚠️</span> My Complaints</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📢</span> Notices</a>
            <?php } ?>
            <div class="sidebar-divider"></div>
            <a href="../logout.php"><span class="nav-icon">🚪</span> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2 class="fade-in">Complaints Tracking</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Review and manage reported issues.</p>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--success); padding: 16px; color: var(--success);">
                ✅ <?php echo $msg; ?>
            </div>
        <?php } ?>

        <div class="glass-card fade-in fade-in-delay-2" style="padding: 15px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
            <span style="font-weight: 600; color: var(--text-secondary); font-size: 14px;">Filter Status:</span>
            <div style="display: flex; gap: 8px;">
                <a href="view_complaint.php" class="btn <?php echo !isset($_GET['filter']) ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">All</a>
                <a href="view_complaint.php?filter=Pending" class="btn <?php echo (isset($_GET['filter']) && $_GET['filter']=='Pending') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">Pending</a>
                <a href="view_complaint.php?filter=Completed" class="btn <?php echo (isset($_GET['filter']) && $_GET['filter']=='Completed') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">Completed</a>
                <a href="view_complaint.php?filter=Rejected" class="btn <?php echo (isset($_GET['filter']) && $_GET['filter']=='Rejected') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 6px 12px; font-size: 12px;">Rejected</a>
            </div>
        </div>

        <div class="glass-card fade-in fade-in-delay-3 p-0 overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:50px;">No.</th>
                        <th>Student Name</th>
                        <th style="width:100px;">Room</th>
                        <th>Description</th>
                        <th style="width:130px;">Date</th>
                        <th style="width:120px;">Status</th>
                        <?php if($_SESSION['role'] == 'admin') echo "<th style='width:180px;'>Action</th>"; ?>
                    </tr>
                </thead>
                <tbody>
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
                        echo "<td><strong>".$row['student_name']."</strong></td>";
                        echo "<td><span style='color:var(--accent); font-weight:600;'>".($row['room_no'] ? $row['room_no'] : 'N/A')."</span></td>";
                        echo "<td style='max-width:300px; font-size:13px;'>".$row['description']."</td>";
                        echo "<td style='color:var(--text-secondary); font-size:12px;'>".$row['date']."</td>";
                        
                        $status = $row['status'];
                        $badge_class = '';
                        if($status == 'Pending') $badge_class = 'badge-warning';
                        elseif($status == 'Completed' || $status == 'Accepted') $badge_class = 'badge-success';
                        elseif($status == 'Rejected') $badge_class = 'badge-danger';
                        
                        echo "<td><span class='badge $badge_class'>$status</span></td>";
                        
                        if($_SESSION['role'] == 'admin') {
                            echo "<td>";
                            if($status == 'Pending') {
                                echo "<form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='complaint_id' value='".$row['id']."'>
                                        <input type='submit' name='accept' value='Complete' class='btn btn-success' style='padding:5px 10px; font-size:11px; margin-right:5px;'>
                                      </form> 
                                      <form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='complaint_id' value='".$row['id']."'>
                                        <input type='submit' name='reject' value='Reject' class='btn btn-danger' style='padding:5px 10px; font-size:11px;'>
                                      </form>";
                            } else {
                                echo "<span style='color:var(--text-secondary); font-style:italic; font-size:12px;'>No actions</span>";
                            }
                            echo "</td>";
                        }
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
