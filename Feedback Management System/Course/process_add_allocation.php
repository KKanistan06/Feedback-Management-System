<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Course Allocation</title>
    <link rel="stylesheet" href="course_allocation_style.css">
</head>

<body>
    <?php
    require_once "../MA/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $course_id = $_POST['course_id'] ?? '';
        $academic_year = $_POST['academic_year'];
        $semester = $_POST['semester'];
        $courses = $_POST['courses'];
        $batches = $_POST['batches'];
        $students = $_POST['students'];
        $lecturers = $_POST['lecturers'];

        $success = true;
        $conn->autocommit(FALSE);

        try {
            if ($course_id) {
                $sqlDeleteEnroll = "DELETE FROM enroll WHERE CourseId = ? AND AY = ?";
                $stmtDeleteEnroll = $conn->prepare($sqlDeleteEnroll);
                $stmtDeleteEnroll->bind_param("ss", $course_id, $academic_year);
                $stmtDeleteEnroll->execute();

                $sqlDeleteTeach = "DELETE FROM teach WHERE CourseId = ? AND AY = ?";
                $stmtDeleteTeach = $conn->prepare($sqlDeleteTeach);
                $stmtDeleteTeach->bind_param("ss", $course_id, $academic_year);
                $stmtDeleteTeach->execute();
            }

            foreach ($students as $student) {
                $sqlEnroll = "INSERT INTO enroll (CourseId, RegNo, AY) VALUES (?, ?, ?)";
                $stmtEnroll = $conn->prepare($sqlEnroll);
                $stmtEnroll->bind_param("sss", $courses[0], $student, $academic_year); // Assuming one course is selected
                $stmtEnroll->execute();
                if ($stmtEnroll->errno) {
                    throw new Exception($stmtEnroll->error);
                }
            }

            foreach ($lecturers as $lecturer) {
                $sqlTeach = "INSERT INTO teach (CourseId, LecturerId, AY) VALUES (?, ?, ?)";
                $stmtTeach = $conn->prepare($sqlTeach);
                $stmtTeach->bind_param("sss", $courses[0], $lecturer, $academic_year); 
                $stmtTeach->execute();
                if ($stmtTeach->errno) {
                    throw new Exception($stmtTeach->error);
                }
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $success = false;
            $errorMessage = $e->getMessage();
        }

        $conn->autocommit(TRUE);
        $conn->close();

        if ($success) {
            echo "<div class='message success-message'>";
            echo "<h2>Success!</h2>";
            echo "<p>Course allocation " . ($course_id ? "updated" : "added") . " successfully.</p>";
            echo "<p>Redirecting...</p>";
            echo "</div>";
            echo "<script>setTimeout(function() { window.location.href = 'course_allocation.php'; }, 2000);</script>";
        } else {
            echo "<div class='message failure-message'>";
            echo "<h2>Error!</h2>";
            echo "<p>Failed to " . ($course_id ? "update" : "add") . " course allocation: $errorMessage</p>";
            echo "</div>";
        }
    }
    ?>
</body>

</html>
