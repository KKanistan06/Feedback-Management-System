<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lecturer</title>
    <link rel="stylesheet" href="add_lecturer_style.css"> 
</head>
<body>
<?php

require_once "../Ma/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lecturerId = trim($_POST["lecturerId"]);
    if (empty($lecturerId)) {
        echo "<div class='message failure-message'>";
        echo "<h2>Error!</h2>";
        echo "<p>Invalid request!</p>";
        echo "</div>";
        exit;
    }

    $sql = "UPDATE lecturer SET Lecturer_Name = ?, Department = ? WHERE LecturerId = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $paramName, $paramDepartment, $paramLecturerId);

        $paramName = trim($_POST["name"]);
        $paramDepartment = trim($_POST["department"]);
        $paramLecturerId = $lecturerId;

        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='message success-message'>";
            echo "<h2>Success!</h2>";
            echo "<p>Lecturer details updated successfully.</p>";
            echo "<p>Redirecting...</p>";
            echo "</div>";
            echo "<script>setTimeout(function() { window.location.href = 'lecturer_list.php'; }, 2000);</script>"; 
        } else {
            echo "<div class='message failure-message'>";
            echo "<h2>Error!</h2>";
            echo "<p>Failed to update lecturer details.</p>";
            echo "</div>";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} else {
    echo "<div class='message failure-message'>";
    echo "<h2>Error!</h2>";
    echo "<p>Invalid request!</p>";
    echo "</div>";
}
?>
</body>
</html>
