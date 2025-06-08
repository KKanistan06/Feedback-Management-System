<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Student") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

if (!isset($_GET["CourseId"]) || empty(trim($_GET["CourseId"])) || !isset($_GET["RegNo"]) || empty(trim($_GET["RegNo"]))) {
    exit("CourseId or RegNo not provided.");
}

$courseId = trim($_GET["CourseId"]);
$regNo = trim($_GET["RegNo"]);

$_SESSION['RegNo'] = $regNo;

$sqlSemester = "SELECT Semester FROM course WHERE CourseId = ?";
if ($stmtSemester = $conn->prepare($sqlSemester)) {
    $stmtSemester->bind_param("s", $courseId);
    if ($stmtSemester->execute()) {
        $stmtSemester->store_result();
        if ($stmtSemester->num_rows == 1) {
            $stmtSemester->bind_result($semester);
            $stmtSemester->fetch();
        } else {
            exit("Semester details not found.");
        }
    } else {
        exit("Oops! Something went wrong. Please try again later.");
    }
    $stmtSemester->close();
}

$sql = "SELECT QueType, QueText FROM lecturer_feedback_contains ORDER BY QueType AND QueId";
if ($stmt = $conn->prepare($sql)) {
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($queType, $queText);

            $currentType = null;
            $questions = [];

            while ($stmt->fetch()) {
                if ($currentType !== $queType) {
                    if ($currentType !== null) {
                        $questions[] = ['type' => $currentType, 'questions' => $currentQuestions];
                    }
                    $currentType = $queType;
                    $currentQuestions = [];
                }
                $currentQuestions[] = $queText;
            }
            if ($currentType !== null) {
                $questions[] = ['type' => $currentType, 'questions' => $currentQuestions];
            }
        } else {
            exit("No questions found.");
        }
    } else {
        exit("Oops! Something went wrong. Please try again later.");
    }
    $stmt->close();
}

$batchNo = 'Unknown';
if (isset($_SESSION["RegNo"])) {
    $regNo = $_SESSION["RegNo"];
    $sqlBatch = "SELECT BatchNo FROM student WHERE RegNo = ?";
    if ($stmtBatch = $conn->prepare($sqlBatch)) {
        $stmtBatch->bind_param("s", $regNo);
        if ($stmtBatch->execute()) {
            $stmtBatch->store_result();
            if ($stmtBatch->num_rows == 1) {
                $stmtBatch->bind_result($batch);
                $stmtBatch->fetch();
                $batchNo = $batch;
            }
        }
        $stmtBatch->close();
    }
}

$sqlLecturer = "SELECT DISTINCT LecturerId FROM teach WHERE CourseId = ?";
$lecturerIds = [];

if ($stmtLecturer = $conn->prepare($sqlLecturer)) {
    $stmtLecturer->bind_param("s", $courseId);
    if ($stmtLecturer->execute()) {
        $stmtLecturer->store_result();
        if ($stmtLecturer->num_rows > 0) {
            $stmtLecturer->bind_result($lecturerId);
            while ($stmtLecturer->fetch()) {
                $lecturerIds[] = $lecturerId;
            }
        } else {
            exit("No lecturers found for this course.");
        }
    } else {
        exit("Oops! Something went wrong. Please try again later.");
    }
    $stmtLecturer->close();
}

$sqlFeedbackGiven = "SELECT DISTINCT LecturerId FROM gives_lecturer_feedback WHERE RegNo = ? AND CourseId = ?";
$feedbackGiven = [];

if ($stmtFeedback = $conn->prepare($sqlFeedbackGiven)) {
    $stmtFeedback->bind_param("ss", $regNo, $courseId);
    if ($stmtFeedback->execute()) {
        $stmtFeedback->store_result();
        if ($stmtFeedback->num_rows > 0) {
            $stmtFeedback->bind_result($feedbackLecturerId);
            while ($stmtFeedback->fetch()) {
                $feedbackGiven[] = $feedbackLecturerId;
            }
        }
    } else {
        exit("Oops! Something went wrong while checking feedback status.");
    }
    $stmtFeedback->close();
}

$lecturerIds = array_diff($lecturerIds, $feedbackGiven);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Feedback</title>
    <link rel="stylesheet" href="lecturer_feedback_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container">
        <button onclick="closeFeedback()" class="close-button">&#10006;</button>
        <h3>Feedback Form</h3>
        <h3>Faculty of Engineering, University of Jaffna</h3>
        <hr>
        <h2>LECTURER EVALUATION</h2>
        <p>This questionnaire intends to collect feedback from the students about the lecturer of the course. Your valuable feedback will be vital for us to improve the quality of teaching.</p>

        <div class="info-container">
            <div class="info-left">
                <p>Batch No: <?php echo htmlspecialchars($batchNo); ?></p>
                <p>Course ID: <?php echo htmlspecialchars($courseId); ?></p>
                <p>Lecturer ID:
                    <select name="LecturerId" form="feedbackForm">
                        <?php foreach ($lecturerIds as $lecturerId) : ?>
                            <option value="<?php echo htmlspecialchars($lecturerId); ?>"><?php echo htmlspecialchars($lecturerId); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            </div>
            <div class="info-right">
                <p>Semester: <?php echo htmlspecialchars($semester); ?></p>
                <p>Date: <?php echo htmlspecialchars(date('Y-m-d')); ?></p>
            </div>
        </div>

        <form id="feedbackForm" method="post" action="save_lecturer_feedback.php">
            <input type="hidden" name="CourseId" value="<?php echo htmlspecialchars($courseId); ?>">
            <input type="hidden" name="LecturerId" value="<?php echo htmlspecialchars(implode(', ', $lecturerIds)); ?>">
            <input type="hidden" name="RegNo" value="<?php echo htmlspecialchars($regNo); ?>">
            <input type="hidden" name="Comments" value="">

            <?php
            if (isset($questions) && is_array($questions)) {
                foreach ($questions as $questionType) {
                    echo "<p>" . htmlspecialchars($questionType['type']) . "</p>";
                    echo "<table>";
                    echo "<tr>";
                    if ($questionType['type'] === 'Comments') {
                        echo "<th class='questions-header'>Questions</th><th>Response</th>";
                    } else {
                        echo "<th class='questions-header'>Questions</th><th>Strongly Disagree</th><th>Disagree</th><th>Not Sure</th><th>Agree</th><th>Strongly Agree</th>";
                    }
                    echo "</tr>";

                    $count = 1;
                    foreach ($questionType['questions'] as $question) {
                        if ($questionType['type'] === 'Comments') {
                            $nameAttribute = 'responses[comments][' . $count . ']';
                            echo "<tr><td class='question-column' style='width: 57.5%;'>" . $count . ". " . htmlspecialchars($question) . "</td>";
                            echo "<td class='response-column' style='width: 42.5%;'><input type='text' class='response-input' name='$nameAttribute' placeholder='Type Your Comments here'></td></tr>";
                        } else {
                            $nameAttribute = 'responses[' . htmlspecialchars($questionType['type']) . '][' . $count . ']';
                            echo "<tr><td class='question-column'>" . $count . ". " . htmlspecialchars($question) . "</td>";
                            echo "<td class='response-column'><input type='radio' class='single-checkbox' name='$nameAttribute' value='1'></td>";
                            echo "<td class='response-column'><input type='radio' class='single-checkbox' name='$nameAttribute' value='2'></td>";
                            echo "<td class='response-column'><input type='radio' class='single-checkbox' name='$nameAttribute' value='3'></td>";
                            echo "<td class='response-column'><input type='radio' class='single-checkbox' name='$nameAttribute' value='4'></td>";
                            echo "<td class='response-column'><input type='radio' class='single-checkbox' name='$nameAttribute' value='5'></td></tr>";
                        }
                        $count++;
                    }
                    echo "</table>";
                }
            }
            ?>

            <div class="actions">
                <button type="button" onclick="saveFeedback()">Save</button>
            </div>
        </form>
    </div>

    <script>
        function closeFeedback() {
            window.location.href = "student_home.php";
        }

        function saveFeedback() {
            var comments = document.querySelectorAll('input[name^="responses[comments]"]');
            var allComments = Array.from(comments).map(input => input.value).join('; ');
            document.getElementsByName('Comments')[0].value = allComments;
            document.getElementById("feedbackForm").submit();
        }
    </script>
</body>

</html>
