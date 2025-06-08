<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result</title>
    <link rel="stylesheet" href="add_lecturer_style.css">
</head>

<body>
    <?php

    require_once "../MA/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $lecturerId = $_POST['lecturerId'];
        $name = $_POST['name'];
        $department = $_POST['department'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $insertLecturerQuery = "INSERT INTO lecturer (LecturerId, Lecturer_Name, Department, Email) VALUES ('$lecturerId', '$name', '$department', '$email')";
        $insertLecturerResult = mysqli_query($conn, $insertLecturerQuery);

        $insertUserQuery = "INSERT INTO user (usertype, Approved, Email, password) VALUES ('lecturer', 1, '$email', '$password')";
        $insertUserResult = mysqli_query($conn, $insertUserQuery);

        if ($insertLecturerResult && $insertUserResult) {
            echo "<div class='message success-message'>";
            echo "<h2>Success!</h2>";
            echo "<p>Your action was completed successfully.</p>";
            echo "<p>Redirecting...</p>";
            echo "</div>";
            echo "<script>setTimeout(function() { window.location.href = 'lecturer_list.php'; }, 2000);</script>";
            exit;
        } else {
            echo "<div class='message failure-message'>";
            echo "<h2>Failure!</h2>";
            echo "<p>Failed to complete the action.</p>";
            echo "<p>Redirecting...</p>";
            echo "</div>";
            echo "<script>setTimeout(function() { window.location.href = 'lecturer_list.php'; }, 2000);</script>";
            exit;
        }
    }
    ?>
</body>

</html>