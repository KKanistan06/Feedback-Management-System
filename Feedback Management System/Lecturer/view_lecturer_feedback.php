<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Lecturer") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

if (!isset($_GET["CourseId"]) || empty(trim($_GET["CourseId"]))) {
    exit("CourseId not provided.");
}

$courseId = trim($_GET["CourseId"]);

if (!isset($_GET["LecturerId"]) || empty(trim($_GET["LecturerId"]))) {
    exit("LecturerId not provided.");
}

$lecturerId = trim($_GET["LecturerId"]);

$sql = "SELECT LQ01, LQ02, LQ03, LQ04, LQ05, LQ06, LQ07, LQ08, LQ09, LQ10, LQ11, LQ12, Comments  
        FROM lecturer_feedback 
        WHERE CourseId = ? AND LecturerId = ?";

$feedbackExists = false;
$feedbackData = [];
$questionColumns = ["LQ01", "LQ02", "LQ03", "LQ04", "LQ05", "LQ06", "LQ07", "LQ08", "LQ09", "LQ10", "LQ11", "LQ12"];

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $courseId, $lecturerId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $feedbackData = $result->fetch_all(MYSQLI_ASSOC);
            $feedbackExists = true;
        }
    }
    $stmt->close();
}

$totalPoints = [];
$averagePoints = [];
$sumOfAverages = 0;

foreach ($questionColumns as $question) {
    $totalPoints[$question] = 0;
    foreach ($feedbackData as $feedback) {
        $totalPoints[$question] += $feedback[$question];
    }
    $averagePoints[$question] = count($feedbackData) > 0 ? round($totalPoints[$question] / count($feedbackData), 2) : 0;
    $sumOfAverages += $averagePoints[$question];
}

$studentCount = count($feedbackData);
$overallAverage = $studentCount > 0 ? round($sumOfAverages / count($questionColumns), 2) : 0;
$overallAveragePercentage = $overallAverage * 20; 

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link rel="stylesheet" href="view_lecturer_feedback_style.css">
</head>

<body>
    <div class="container">
        <button onclick="closeFeedback()" class="close-button">&#10006;</button>
        <h2>Feedback Details for Course <?php echo htmlspecialchars($courseId); ?></h2>
        <?php if ($feedbackExists) { ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Question ID</th>
                            <?php foreach ($feedbackData as $index => $feedback) : ?>
                                <th><?php echo 'Stu' . ($index + 1); ?></th>
                            <?php endforeach; ?>
                            <th>Total Points</th>
                            <th>Average Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questionColumns as $question) : ?>
                            <tr>
                                <td><?php echo $question; ?></td>
                                <?php foreach ($feedbackData as $feedback) : ?>
                                    <td><?php echo htmlspecialchars($feedback[$question]); ?></td>
                                <?php endforeach; ?>
                                <td><?php echo $totalPoints[$question]; ?></td>
                                <td><?php echo $averagePoints[$question]; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php } else {
            echo "<p>No feedback found for this course.</p>";
        } ?>
    </div>

    <script>
        function closeFeedback() {
            window.location.href = "lecturer_home.php";
        }

        const studentCount = <?php echo $studentCount; ?>;
        let currentCount = 0;
        const studentCounterElement = document.getElementById('studentCounter');

        const countUpStudents = setInterval(() => {
            if (currentCount < studentCount) {
                currentCount += 1;
                studentCounterElement.textContent = currentCount;
            } else {
                clearInterval(countUpStudents);
            }
        }, 100);

        const overallPercentage = <?php echo $overallAveragePercentage; ?>;
        let currentPercentage = 0;
        const percentageCounterElement = document.getElementById('percentageCounter');

        const countUpPercentage = setInterval(() => {
            if (currentPercentage < overallPercentage) {
                currentPercentage += 1;
                percentageCounterElement.textContent = currentPercentage + '%';
            } else {
                clearInterval(countUpPercentage);
            }
        }, 20);
    </script>
</body>

</html>
