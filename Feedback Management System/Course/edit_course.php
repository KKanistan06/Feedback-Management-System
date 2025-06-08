<?php

require_once "../MA/config.php";

if (isset($_POST["courseId"], $_POST["courseName"], $_POST["semester"], $_POST["credit"]) && !empty($_POST["courseId"]) && !empty($_POST["courseName"]) && !empty($_POST["semester"]) && !empty($_POST["credit"])) {
    $sql = "UPDATE course SET Course_Name=?, Semester=?, Credit=? WHERE CourseId=?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $param_courseName, $param_semester, $param_credit, $param_courseId);

        $param_courseName = $_POST["courseName"];
        $param_semester = $_POST["semester"];
        $param_credit = $_POST["credit"];
        $param_courseId = $_POST["courseId"];

        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
