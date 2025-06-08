<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

if (!isset($_GET['id'])) {
    echo "Course ID is required.";
    exit;
}

$courseId = $_GET['id'];

function fetchBatchNumbers($conn, $courseId) {
    $query = "SELECT DISTINCT BatchNo FROM course_feedback WHERE CourseId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $courseId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $batchNumbers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $batchNumbers[] = $row['BatchNo'];
    }
    return $batchNumbers;
}

function fetchCourseFeedbackDetails($conn, $courseId, $batchNo = null) {
    $query = "SELECT FeedbackId, BatchNo, CQ01, CQ02, CQ03, CQ04, CQ05, CQ06, CQ07, CQ08, CQ09, CQ10, CQ11, CQ12, CQ13, CQ14, CQ15 
              FROM course_feedback 
              WHERE CourseId = ?";
    if ($batchNo) {
        $query .= " AND BatchNo = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $courseId, $batchNo);
    } else {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result;
}

$batchNumbers = fetchBatchNumbers($conn, $courseId);
$selectedBatchNo = isset($_GET['batchNo']) ? $_GET['batchNo'] : null;

$feedbackDetails = fetchCourseFeedbackDetails($conn, $courseId, $selectedBatchNo);

$feedbackData = [];
$questionColumns = ["CQ01", "CQ02", "CQ03", "CQ04", "CQ05", "CQ06", "CQ07", "CQ08", "CQ09", "CQ10", "CQ11", "CQ12", "CQ13", "CQ14", "CQ15"];

while ($row = mysqli_fetch_assoc($feedbackDetails)) {
    $feedbackData[] = $row;
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
$overallAveragePercentage = $overallAverage * 20 ; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Feedback Summary</title>
    <link rel="stylesheet" href="view_course_feedback_summary_style.css">
</head>

<body>

    <div class="container">
        <button class="close-button" onclick="window.location.href='course_summary.php'">&#10006;</button>
        <h2>Course Feedback Summary</h2>
        <h3>Course Id: <?php echo htmlspecialchars($courseId); ?></h3>
        <form method="GET" action="view_course_feedback_summary.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($courseId); ?>">
            <label for="batchNo">Select Batch No:</label>
            <select name="batchNo" id="batchNo" onchange="this.form.submit()">
                <option value="" <?php echo !$selectedBatchNo ? 'selected' : ''; ?>>All Batches</option>
                <?php foreach ($batchNumbers as $batch) : ?>
                    <option value="<?php echo $batch; ?>" <?php echo $batch == $selectedBatchNo ? 'selected' : ''; ?>>
                        <?php echo $batch; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="count-box">
            <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
            </div>
            <div class="title">Students</div>
            <div class="value" id="studentCounter">0</div>
        </div>
        <div class="percentage-box">
            <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
            </div>
            <div class="title">Overall Average</div>
            <div class="value" id="percentageCounter">0%</div>
        </div>
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
                                <td><?php echo $feedback[$question]; ?></td>
                            <?php endforeach; ?>
                            <td><?php echo $totalPoints[$question]; ?></td>
                            <td><?php echo $averagePoints[$question]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
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
