<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link rel="stylesheet" href="../Student/add_student_style.css"> 
</head>
<body>
<?php
require_once "config.php";

$queId = $_POST["lecturerQueId"] ?? "";
$queType = $_POST["lecturerQueType"] ?? "";
$newQueType = $_POST["newLecturerQueType"] ?? "";
$queText = $_POST["lecturerQueText"] ?? "";

if ($queType === "New Type") {
    $queType = $newQueType;

    $queryInsertNewType = "INSERT INTO lecturer_feedback_contains (QueId, QueType, QueText) VALUES ('$queId', '$queType', '$queText')";
    $resultInsertNewType = mysqli_query($conn, $queryInsertNewType);
    if ($resultInsertNewType) {
        echo "<div class='message success-message'>";
        echo "<h2>Success!</h2>";
        echo "<p>Question added successfully.</p>";
        echo "<p>Redirecting....</p>";
        echo "</div>";
    } else {
        echo "<div class='message failure-message'>";
        echo "<h2>Error!</h2>";
        echo "<p>Failed to add question.</p>";
        echo "</div>";
    }
} else {
    $queryLecturerQuestion = "INSERT INTO lecturer_feedback_contains (QueId, QueType, QueText) VALUES ('$queId', '$queType', '$queText')";
    $resultLecturerQuestion = mysqli_query($conn, $queryLecturerQuestion);

    if ($resultLecturerQuestion) {
        echo "<div class='message success-message'>";
        echo "<h2>Success!</h2>";
        echo "<p>Question added successfully.</p>";
        echo "<p>Redirecting....</p>";
        echo "</div>";
    } else {
        echo "<div class='message failure-message'>";
        echo "<h2>Error!</h2>";
        echo "<p>Failed to add question.</p>";
        echo "</div>";
    }
}

echo "<script>setTimeout(function() { window.location.href = 'question_list.php'; }, 3000);</script>";
?>
</body>
</html>
