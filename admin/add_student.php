<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['name'])) { header("Location: ../login.php"); exit(); }
if($_SESSION['role'] != 'admin') { die("Access Denied"); }

if(isset($_POST['add'])) {
    $name = $_POST['name'];
    $college_name = $_POST['college_name'];
    $roll_no = $_POST['roll_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    
    // basic insecure file upload
    $photo = "";
    if(isset($_FILES['photo']['name']) && $_FILES['photo']['name'] != "") {
        $photo = $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }
    
    // Create login account
    $password = $roll_no; // password is roll_no initially
    mysqli_query($conn, "INSERT INTO users(name, email, password, role) VALUES ('$name', '$email', '$password', 'student')");
    
    // Default due date one month from registration
    $due_date = date("Y-m-d", strtotime("+1 month"));
    
    // Insert student
    $sql = "INSERT INTO students (name, college_name, roll_no, email, phone, gender, fee_due_date, fee_due_amount, photo) VALUES ('$name', '$college_name', '$roll_no', '$email', '$phone', '$gender', '$due_date', 5000, '$photo')";
    if(mysqli_query($conn, $sql)) {
        $msg = "Student Added Successfully!";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>
<html>
<head>
<title>Add Student</title>
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
        <h2>Add Student</h2>
        <hr>
        <?php if(isset($msg)) echo "<font color='green'><b>$msg</b></font><br><br>"; ?>
        <form method="post" action="" enctype="multipart/form-data">
        <table border="1" cellpadding="10" bgcolor="white">
            <tr><td><b>Student Name</b></td><td><input type="text" name="name" required></td></tr>
            <tr><td><b>College Name</b></td><td><input type="text" name="college_name" required></td></tr>
            <tr><td><b>Roll No</b></td><td><input type="text" name="roll_no" required></td></tr>
            <tr><td><b>Email</b></td><td><input type="email" name="email" required></td></tr>
            <tr><td><b>Phone</b></td><td><input type="text" name="phone" required></td></tr>
            <tr><td><b>Gender</b></td>
                <td>
                    <select name="gender" required>
                        <option value="Boy">Boy</option>
                        <option value="Girl">Girl</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
            </tr>
            <tr><td><b>Photo</b></td><td><input type="file" name="photo"></td></tr>
            <tr><td colspan="2" align="center"><input type="submit" name="add" value="Add Student"></td></tr>
        </table>
        </form>
        <br><br>
        User account will be created automatically. Username = Roll No, Password = Roll No.
    </td>
</tr>
</table>

</body>
</html>
