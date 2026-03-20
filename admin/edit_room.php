<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $room_no = $_POST['room_no'];
    $capacity = $_POST['capacity'];
    $available_beds = $_POST['available_beds'];
    $gender = $_POST['gender'];
    
    $sql = "UPDATE rooms SET room_no='$room_no', capacity='$capacity', available_beds='$available_beds', gender='$gender' WHERE id='$id'";
    if(mysqli_query($conn, $sql)) {
        $msg = "Room Updated Successfully!";
    } else {
        $msg = "Error updating room!";
    }
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM rooms WHERE id='$id'");
    $room = mysqli_fetch_array($query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room — Nestify</title>
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
        <h2 class="fade-in">Edit Room Details</h2>
        <p class="page-subtitle fade-in fade-in-delay-1">Modify property details and capacity of existing rooms.</p>

        <?php if(isset($msg)) { ?>
            <div class="glass-card fade-in mb-4" style="border-left: 4px solid var(--accent); padding: 16px; color: var(--accent); background: rgba(56, 189, 248, 0.05);">
                ✨ <?php echo $msg; ?>
            </div>
        <?php } ?>

        <div class="glass-card fade-in fade-in-delay-2" style="max-width: 500px;">
            <?php if(isset($room)) { ?>
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
                    
                    <div class="form-group">
                        <label>Room Number</label>
                        <input type="text" name="room_no" value="<?php echo $room['room_no']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Assignment Type</label>
                        <select name="gender" required>
                            <option value="Boy" <?php if($room['gender']=='Boy') echo 'selected'; ?>>Boy's Hostel</option>
                            <option value="Girl" <?php if($room['gender']=='Girl') echo 'selected'; ?>>Girl's Hostel</option>
                        </select>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Total Capacity</label>
                            <input type="number" name="capacity" value="<?php echo $room['capacity']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Available Beds</label>
                            <input type="number" name="available_beds" value="<?php echo $room['available_beds']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mt-4" style="display: flex; gap: 10px;">
                        <input type="submit" name="update" value="Save Changes" class="btn btn-primary" style="flex: 1;">
                        <a href="../shared/view_rooms.php" class="btn btn-secondary" style="text-decoration: none; padding: 12px 25px;">Back</a>
                    </div>
                </form>
            <?php } else { ?>
                <div style="text-align: center; padding: 30px; color: var(--danger);">
                    <p>⚠️ Room record not found.</p>
                </div>
            <?php } ?>
        </div>
    </main>
</div>

</body>
</html>

</body>
</html>
