<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Lecturer Feedback</title>
    <link rel="stylesheet" href="add_student_style.css">
</head>
<body>
<?php

require_once "../MA/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $regNo = isset($_SESSION["RegNo"]) ? $_SESSION["RegNo"] : '';
    $batchNo = isset($_SESSION["BatchNo"]) ? $_SESSION["BatchNo"] : 'Unknown';

    if (isset($_POST['responses']) && isset($_POST['Comments']) && isset($_POST['CourseId']) && isset($_POST['LecturerId'])) {
        $courseId = $_POST['CourseId'];
        $lecturerId = $_POST['LecturerId'];
        $comments = $_POST['Comments'];

        $responses = [];
        foreach ($_POST['responses'] as $category => $questions) {
            foreach ($questions as $index => $response) {
                $responses[] = $response;
            }
        }

        while (count($responses) < 12) {
            $responses[] = null;
        }

        $sqlFeedback = "INSERT INTO lecturer_feedback (BatchNo, LecturerId, CourseId, LQ01, LQ02, LQ03, LQ04, LQ05, LQ06, LQ07, LQ08, LQ09, LQ10, LQ11, LQ12, Comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmtFeedback = $conn->prepare($sqlFeedback)) {
            $stmtFeedback->bind_param("ssssssssssssssss", 
                $batchNo, $lecturerId, $courseId, 
                $responses[0], $responses[1], $responses[2], $responses[3], 
                $responses[4], $responses[5], $responses[6], $responses[7], 
                $responses[8], $responses[9], $responses[10], $responses[11],
                $comments
            );

            if ($stmtFeedback->execute()) {
                $sqlGivesFeedback = "INSERT INTO gives_lecturer_feedback (BatchNo, RegNo, CourseId, LecturerId) VALUES (?, ?, ?, ?)";
                if ($stmtGivesFeedback = $conn->prepare($sqlGivesFeedback)) {
                    $stmtGivesFeedback->bind_param("ssss", $batchNo, $regNo, $courseId, $lecturerId);
                    $stmtGivesFeedback->execute();
                    $stmtGivesFeedback->close();
                }

                echo "<div class='message success-message'>";
                echo "<h2>Success!</h2>";
                echo "<p>Feedback saved successfully.</p>";
                echo "<p>Redirecting...</p>";
                echo "</div>";
                echo "<script>setTimeout(function() { window.location.href = 'student_home.php'; }, 3000);</script>";
                exit();
            } else {
                echo "<div class='message failure-message'>";
                echo "<h2>Error!</h2>";
                echo "<p>Oops! Something went wrong. Please try again later.</p>";
                echo "</div>";
            }

            $stmtFeedback->close();
        } else {
            echo "<div class='message failure-message'>";
            echo "<h2>Error!</h2>";
            echo "<p>Oops! Something went wrong. Please try again later.</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='message failure-message'>";
        echo "<h2>Error!</h2>";
        echo "<p>'responses', 'Comments', 'CourseId', 'LecturerId' data not received.</p>";
        echo "</div>";
        exit;
    }

    $conn->close();
} else {
    echo "<div class='message failure-message'>";
    echo "<h2>Error!</h2>";
    echo "<p>Invalid request.</p>";
    echo "</div>";
}
?>
</body>
</html>
