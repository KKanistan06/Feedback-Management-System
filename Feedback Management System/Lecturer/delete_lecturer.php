<?php
require_once "../MA/config.php";

if (!isset($_POST["lecturerId"]) || empty(trim($_POST["lecturerId"])) || !isset($_POST["email"]) || empty(trim($_POST["email"]))) {
    echo "Invalid request!";
    exit;
}

$lecturerId = trim($_POST["lecturerId"]);
$email = trim($_POST["email"]);

$sqlDeleteLecturer = "DELETE FROM lecturer WHERE LecturerId = ?";
$sqlDeleteUser = "DELETE FROM user WHERE Email = ?";

if ($stmtLecturer = mysqli_prepare($conn, $sqlDeleteLecturer)) {
    if ($stmtUser = mysqli_prepare($conn, $sqlDeleteUser)) {
        mysqli_stmt_bind_param($stmtLecturer, "s", $lecturerId);
        mysqli_stmt_bind_param($stmtUser, "s", $email);

        mysqli_stmt_execute($stmtLecturer);
        mysqli_stmt_execute($stmtUser);

        if (mysqli_stmt_affected_rows($stmtLecturer) > 0 && mysqli_stmt_affected_rows($stmtUser) > 0) {
            echo "success";
        } else {
            echo "No lecturer or user found with the provided ID and email.";
        }

        mysqli_stmt_close($stmtUser);
    } else {
        echo "Failed to prepare the user deletion statement: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmtLecturer);
} else {
    echo "Failed to prepare the lecturer deletion statement: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
