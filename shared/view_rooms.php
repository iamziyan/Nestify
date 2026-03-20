<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] == 'admin' && isset($_POST['delete_room'])) {
    $del_id = $_POST['delete_room_id'];
    // Update students assigned to this room to 0
    mysqli_query($conn, "UPDATE students SET room_id=0 WHERE room_id='$del_id'");
    // Delete room requests associated with this room
    mysqli_query($conn, "DELETE FROM room_requests WHERE room_id='$del_id'");
    // Delete the room
    if(mysqli_query($conn, "DELETE FROM rooms WHERE id='$del_id'")) {
        $msg = "Room deleted successfully!";
    } else {
        $error = "Error deleting room!";
    }
}

if(isset($_POST['request_room']) || isset($_POST['waitlist_room'])) {
    $room_id = $_POST['room_id'];
    $status = isset($_POST['request_room']) ? 'Pending' : 'Waitlisted';
    
    $email = $_SESSION['email'];
    $stQ = mysqli_query($conn, "SELECT id FROM students WHERE email='$email'");
    $stR = mysqli_fetch_array($stQ);
    
    if($stR) {
        $st_id = $stR['id'];
        $date = date('Y-m-d');
        $check = mysqli_query($conn, "SELECT * FROM room_requests WHERE student_id='$st_id' AND room_id='$room_id' AND status IN ('Pending', 'Waitlisted')");
        
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO room_requests (student_id, room_id, status, request_date) VALUES ('$st_id', '$room_id', '$status', '$date')");
            $msg = "Request submitted successfully. Status: $status";
        } else {
            $error = "You already have an active request or waitlist for this room!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rooms — Nestify</title>
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
                <a href="../shared/view_rooms.php" class="active"><span class="nav-icon">🏢</span> View Rooms</a>
                <a href="../admin/add_notice.php"><span class="nav-icon">📢</span> Add Notice</a>
                <a href="../shared/view_notices.php"><span class="nav-icon">📋</span> View Notices</a>
                <a href="../shared/view_complaint.php"><span class="nav-icon">⚠️</span> View Complaints</a>
            <?php } else { ?>
                <a href="../shared/view_rooms.php" class="active"><span class="nav-icon">🏢</span> View Rooms</a>
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
        <h2 class="fade-in">View Rooms</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">All hostel rooms with availability status.</p>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--success); padding: 16px; color: var(--success);">
                ✅ <?php echo $msg; ?>
            </div>
        <?php } ?>
        
        <?php if(isset($error)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--danger); padding: 16px; color: #f87171;">
                ⚠️ <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="glass-card fade-in fade-in-delay-2 p-0 overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room No</th>
                        <th>Gender</th>
                        <th>Capacity</th>
                        <th>Available Beds</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT * FROM rooms";
                $res = mysqli_query($conn, $query);
                if($res) {
                    while($row = mysqli_fetch_array($res)) {
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td><strong style='color:var(--accent);'>".$row['room_no']."</strong></td>";
                        $g_class = ($row['gender'] == 'Boy') ? 'badge-boy' : 'badge-girl';
                        echo "<td><span class='badge $g_class'>".$row['gender']."</span></td>";
                        echo "<td>".$row['capacity']."</td>";
                        echo "<td>".$row['available_beds']."</td>";
                        echo "<td>";
                        if($_SESSION['role'] == 'student') {
                            echo "<form method='post' action='' style='margin:0;'>";
                            echo "<input type='hidden' name='room_id' value='".$row['id']."'>";
                            if($row['available_beds'] > 0) {
                                echo "<input type='submit' name='request_room' value='Request' class='btn btn-primary' style='padding:6px 14px; font-size:12px;'>";
                            } else {
                                echo "<input type='submit' name='waitlist_room' value='Notify Me' class='btn btn-warning' style='padding:6px 14px; font-size:12px;'>";
                            }
                            echo "</form>";
                        }
                        if($_SESSION['role'] == 'admin') {
                            echo "<a href='../admin/edit_room.php?id=".$row['id']."' class='btn btn-secondary' style='padding:6px 14px; font-size:12px; margin-right:8px;'>Edit</a>";
                            echo "<form method='post' action='' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this room?\");'>
                                    <input type='hidden' name='delete_room_id' value='".$row['id']."'>
                                    <input type='submit' name='delete_room' value='Delete' class='btn btn-danger' style='padding:6px 14px; font-size:12px;'>
                                  </form>";
                        }
                        echo "<td>";
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
