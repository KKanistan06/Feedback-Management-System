<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Course Feedback</title>
    <link rel="stylesheet" href="add_student_style.css">
</head>
<body>
<?php
require_once "../MA/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Debugging: log POST data
    file_put_contents('../MA/post_data.txt', print_r($_POST, true));

    // Retrieve necessary data from POST or URL
    $regNo = isset($_GET["RegNo"]) ? $_GET["RegNo"] : '';
    $batchNo = isset($_SESSION["BatchNo"]) ? $_SESSION["BatchNo"] : 'Unknown';

    // Check if required POST data is set
    if (isset($_POST['responses']) && isset($_POST['Comments']) && isset($_POST['CourseId'])) {
        $courseId = $_POST['CourseId'];
        $comments = $_POST['Comments'];

        // Flatten responses
        $responses = [];
        foreach ($_POST['responses'] as $key => $response) {
            $responses[$key] = $response;
        }

        // Ensure responses array has exactly 15 elements (CQ01 to CQ15)
        $questions = ['CQ01', 'CQ02', 'CQ03', 'CQ04', 'CQ05', 'CQ06', 'CQ07', 'CQ08', 'CQ09', 'CQ10', 'CQ11', 'CQ12', 'CQ13', 'CQ14', 'CQ15'];
        $placeholders = array_fill(0, 15, '?');
        $bind_types = str_repeat('s', 18);

        // Build the SQL query for course feedback
        $sqlFeedback = "INSERT INTO course_feedback (BatchNo, CourseId, " . implode(', ', $questions) . ", Comments) VALUES (?, ?, " . implode(', ', $placeholders) . ", ?)";

        // Prepare and bind parameters for course feedback
        if ($stmtFeedback = $conn->prepare($sqlFeedback)) {
            $stmtFeedback->bind_param($bind_types, $batchNo, $courseId, 
                $responses['CQ01'], $responses['CQ02'], $responses['CQ03'], 
                $responses['CQ04'], $responses['CQ05'], $responses['CQ06'], 
                $responses['CQ07'], $responses['CQ08'], $responses['CQ09'], 
                $responses['CQ10'], $responses['CQ11'], $responses['CQ12'], 
                $responses['CQ13'], $responses['CQ14'], $responses['CQ15'], 
                $comments
            );

            // Execute the feedback statement
            if ($stmtFeedback->execute()) {
                // Prepare and execute the gives_course_feedback statement
                $sqlGivesFeedback = "INSERT INTO gives_course_feedback (BatchNo, RegNo, CourseId) VALUES (?, ?, ?)";
                if ($stmtGivesFeedback = $conn->prepare($sqlGivesFeedback)) {
                    $stmtGivesFeedback->bind_param("sss", $batchNo, $regNo, $courseId);
                    $stmtGivesFeedback->execute();
                    $stmtGivesFeedback->close();
                }

                // Success message
                echo "<div class='message success-message'>";
                echo "<h2>Success!</h2>";
                echo "<p>Feedback saved successfully.</p>";
                echo "<p>Redirecting...</p>";
                echo "</div>";
                echo "<script>setTimeout(function() { window.location.href = 'student_home.php'; }, 3000);</script>";
                exit();
            } else {
                // Error message
                echo "<div class='message failure-message'>";
                echo "<h2>Error!</h2>";
                echo "<p>Oops! Something went wrong. Please try again later.</p>";
                echo "</div>";
            }

            $stmtFeedback->close();
        } else {
            // Error message
            echo "<div class='message failure-message'>";
            echo "<h2>Error!</h2>";
            echo "<p>Oops! Something went wrong. Please try again later.</p>";
            echo "</div>";
        }
    } else {
        // Error message
        echo "<div class='message failure-message'>";
        echo "<h2>Error!</h2>";
        echo "<p>'responses', 'Comments', or 'CourseId' data not received.</p>";
        echo "</div>";
        exit;
    }

    $conn->close();
} else {
    // Error message
    echo "<div class='message failure-message'>";
    echo "<h2>Error!</h2>";
    echo "<p>Invalid request.</p>";
    echo "</div>";
}
?>
</body>
</html>
