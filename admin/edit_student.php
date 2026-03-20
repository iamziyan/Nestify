<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }



if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
    $student = mysqli_fetch_array($query);
}

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $college = $_POST['college_name'];
    $roll = $_POST['roll_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_rent = $_POST['room_rent'];
    $mess_fee = $_POST['mess_fee'];
    $maintenance_fee = $_POST['maintenance_fee'];
    $fee_due_amount = $room_rent + $mess_fee + $maintenance_fee;

    $sql = "UPDATE students SET name='$name', college_name='$college', roll_no='$roll', email='$email', phone='$phone', room_rent='$room_rent', mess_fee='$mess_fee', maintenance_fee='$maintenance_fee', fee_due_amount='$fee_due_amount' WHERE id='$id'";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: view_students.php?msg=Updated");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>
<html>
<head>
<title>Edit Student</title>
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
        <a href="../admin/add_student.php">Add Student</a>
        <a href="../admin/view_students.php">View Students</a>
        <a href="../admin/add_room.php">Add Room</a>
        <a href="../admin/allocate_room.php">Allocate Room</a>
        <a href="../admin/view_requests.php">Room Requests</a>
        <a href="../shared/view_rooms.php">View Rooms</a>
        <a href="../admin/add_notice.php">Add Notice</a>
        <a href="../shared/view_notices.php">View Notices</a>
        <a href="../shared/view_complaint.php">View All Complaints</a>
        <a href="../logout.php">Logout</a>
    </td>
    <td class="content-table">
        <h2>Edit Student</h2>
        <hr>
        <?php if(isset($error)) echo "<font color='red'>$error</font>"; ?>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            <table cellpadding="10">
                <tr><td>Name:</td><td><input type="text" name="name" value="<?php echo $student['name']; ?>" required></td></tr>
                <tr><td>College:</td><td><input type="text" name="college_name" value="<?php echo $student['college_name']; ?>" required></td></tr>
                <tr><td>Roll No:</td><td><input type="text" name="roll_no" value="<?php echo $student['roll_no']; ?>" required></td></tr>
                <tr><td>Email:</td><td><input type="email" name="email" value="<?php echo $student['email']; ?>" required></td></tr>
                <tr><td>Phone:</td><td><input type="text" name="phone" value="<?php echo $student['phone']; ?>" required></td></tr>
                <tr><td>Room Rent:</td><td><input type="number" name="room_rent" value="<?php echo isset($student['room_rent']) ? $student['room_rent'] : 2000; ?>" required></td></tr>
                <tr><td>Mess Fee:</td><td><input type="number" name="mess_fee" value="<?php echo isset($student['mess_fee']) ? $student['mess_fee'] : 2500; ?>" required></td></tr>
                <tr><td>Maintenance:</td><td><input type="number" name="maintenance_fee" value="<?php echo isset($student['maintenance_fee']) ? $student['maintenance_fee'] : 500; ?>" required></td></tr>
                <tr><td colspan="2"><input type="submit" name="update" value="Update Student" style="padding: 5px 10px; background-color: lightblue; cursor: pointer;"></td></tr>
            </table>
        </form>
    </td>
</tr>
</table>
</body>
</html>
