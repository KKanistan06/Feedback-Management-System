<?php

require_once "../MA/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["courseId"])) {
    $sql = "DELETE FROM course WHERE CourseId = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_courseId);

        $param_courseId = $_POST["courseId"];

        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "error";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} else {
    header("Location: course_list.php");
    exit;
}
