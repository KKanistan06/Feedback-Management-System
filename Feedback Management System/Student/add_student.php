<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="add_student_style.css">
</head>

<body>
    <?php

    require_once "../MA/config.php";

    $regNo = $_POST["regNo"] ?? "";
    $name = $_POST["name"] ?? "";
    $batch = $_POST["batch"] ?? "";
    $semester = $_POST["semester"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $address = $_POST["address"] ?? "";
    $phone = $_POST["phone"] ?? "";

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $queryStudent = "INSERT INTO student (RegNo, Student_Name, BatchNo, Semester, Email, Address, PhoneNo) VALUES ('$regNo', '$name', '$batch', '$semester', '$email', '$address', '$phone')";
    $resultStudent = mysqli_query($conn, $queryStudent);

    $queryUser = "INSERT INTO user (usertype, approved, email, password) VALUES ('student', 1, '$email', '$hashedPassword')";
    $resultUser = mysqli_query($conn, $queryUser);

    if ($resultStudent && $resultUser) {
        echo "<div class='message success-message'>";
        echo "<h2>Success!</h2>";
        echo "<p>Student added successfully.</p>";
        echo "<p>Redirecting...</p>";
        echo "</div>";
        echo "<script>setTimeout(function() { window.location.href = 'student_list.php'; }, 3000);</script>";
    } else {
        echo "<div class='message failure-message'>";
        echo "<h2>Error!</h2>";
        echo "<p>Failed to add student.</p>";
        echo "</div>";
    }
    ?>
</body>

</html>