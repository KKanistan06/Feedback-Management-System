<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="add_course_style.css">
</head>

<body>
    <?php

    require_once "../MA/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newCourseId = $_POST["newCourseId"];
        $newCourseName = $_POST["newCourseName"];
        $department = $_POST["department"];
        $semester = $_POST["semester"];
        $newCredit = $_POST["newCredit"];

        $query = "INSERT INTO course (CourseId, Course_Name, Department, Semester, Credit) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $newCourseId, $newCourseName, $department, $semester, $newCredit);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            echo "<div class='message success-message'>";
            echo "<h2>Success!</h2>";
            echo "<p>Course added successfully.</p>";
            echo "<p>Redirecting...</p>";
            echo "</div>";
            echo "<script>setTimeout(function() { window.location.href = 'course_list.php'; }, 3000);</script>";
            exit;
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            echo "<div class='message failure-message'>";
            echo "<h2>Error!</h2>";
            echo "<p>Failed to add course.</p>";
            echo "</div>";
            exit;
        }
    } else {
        header("location: course_list.php");
        exit;
    }
    ?>
</body>

</html>