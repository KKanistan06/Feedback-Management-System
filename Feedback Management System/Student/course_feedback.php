<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Student") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

if (!isset($_GET["CourseId"]) || empty(trim($_GET["CourseId"]))) {
    exit("CourseId not provided.");
}

if (!isset($_GET["RegNo"]) || empty(trim($_GET["RegNo"]))) {
    exit("RegNo not provided.");
}

$courseId = trim($_GET["CourseId"]);
$regNo = trim($_GET["RegNo"]);
$feedbackGiven = false;

$sqlCheckFeedback = "SELECT COUNT(*) FROM gives_course_feedback WHERE RegNo = ? AND CourseId = ?";
if ($stmtCheckFeedback = $conn->prepare($sqlCheckFeedback)) {
    $stmtCheckFeedback->bind_param("ss", $regNo, $courseId);
    if ($stmtCheckFeedback->execute()) {
        $stmtCheckFeedback->store_result();
        $stmtCheckFeedback->bind_result($feedbackCount);
        $stmtCheckFeedback->fetch();
        $feedbackGiven = $feedbackCount > 0;
    } else {
        exit("Oops! Something went wrong. Please try again later.");
    }
    $stmtCheckFeedback->close();
}

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

// Fetch BatchNo from student table
$batchNo = 'Unknown';
$sqlBatch = "SELECT BatchNo FROM student WHERE RegNo = ?";
if ($stmtBatch = $conn->prepare($sqlBatch)) {
    $stmtBatch->bind_param("s", $regNo);
    if ($stmtBatch->execute()) {
        $stmtBatch->store_result();
        if ($stmtBatch->num_rows == 1) {
            $stmtBatch->bind_result($batch);
            $stmtBatch->fetch();
            $batchNo = $batch;
        } else {
            $batchNo = 'Not found';
        }
    } else {
        $batchNo = 'Error fetching batch number';
    }
    $stmtBatch->close();
}

$sql = "SELECT QueId, QueType, QueText FROM course_feedback_contains ORDER BY QueType AND QueId";
if ($stmt = $conn->prepare($sql)) {
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($queId, $queType, $queText);
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
                $currentQuestions[] = ['QueId' => $queId, 'QueText' => $queText];
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
} else {
    exit("Oops! Something went wrong. Please try again later.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Feedback</title>
    <link rel="stylesheet" href="course_feedback_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <button onclick="closeFeedback()" class="close-button">&#10006;</button>
        <h3>Feedback Form</h3>
        <h3>Faculty of Engineering, University of Jaffna</h3>
        <hr>
        <h2>COURSE EVALUATION</h2>
        <p>This questionnaire intends to collect feedback from the students about the course unit. Your valuable feedback will be vital for us to strengthen the teaching-learning environment to achieve excellence in teaching and learning.</p>

        <div class="info-container">
            <div class="info-left">
                <p>Batch No: <?php echo htmlspecialchars($batchNo); ?></p>
                <p>Course ID: <?php echo htmlspecialchars($courseId); ?></p>
            </div>
            <div class="info-right">
                <p>Semester: <?php echo htmlspecialchars($semester); ?></p>
                <p>Date: <?php echo htmlspecialchars(date('Y-m-d')); ?></p>
            </div>
        </div>

        <form id="feedbackForm" method="post" action="save_feedback.php?CourseId=<?php echo htmlspecialchars($courseId); ?>&RegNo=<?php echo htmlspecialchars($regNo); ?>">
            <input type="hidden" name="CourseId" value="<?php echo htmlspecialchars($courseId); ?>">
            <input type="hidden" name="Comments" value="">

            <?php foreach ($questions as $questionType) : ?>
                <p><?php echo htmlspecialchars($questionType['type']); ?></p>
                <table>
                    <tr>
                        <?php if ($questionType['type'] === 'Comments') : ?>
                            <th class='questions-header'>Questions</th>
                            <th>Response</th>
                        <?php else : ?>
                            <th class='questions-header'>Questions</th>
                            <th>Strongly Disagree</th>
                            <th>Disagree</th>
                            <th>Not Sure</th>
                            <th>Agree</th>
                            <th>Strongly Agree</th>
                        <?php endif; ?>
                    </tr>
                    <?php $count = 1; ?>
                    <?php foreach ($questionType['questions'] as $question) : ?>
                        <tr>
                            <?php if ($questionType['type'] === 'Comments') : ?>
                                <td class='question-column' style='width: 57.5%;'><?php echo $count . ". " . htmlspecialchars($question['QueText']); ?></td>
                                <td class='response-column' style='width: 42.5%;'><input type='text' id='comments' class='response-input' name='responses[<?php echo $question['QueId']; ?>]' placeholder='Type Your Comments here'></td>
                            <?php else : ?>
                                <?php $nameAttribute = 'responses[' . $question['QueId'] . ']'; ?>
                                <td class='question-column'><?php echo $count . ". " . htmlspecialchars($question['QueText']); ?></td>
                                <td class='response-column'><input type='radio' class='single-checkbox' name='<?php echo $nameAttribute; ?>' value='1'></td>
                                <td class='response-column'><input type='radio' class='single-checkbox' name='<?php echo $nameAttribute; ?>' value='2'></td>
                                <td class='response-column'><input type='radio' class='single-checkbox' name='<?php echo $nameAttribute; ?>' value='3'></td>
                                <td class='response-column'><input type='radio' class='single-checkbox' name='<?php echo $nameAttribute; ?>' value='4'></td>
                                <td class='response-column'><input type='radio' class='single-checkbox' name='<?php echo $nameAttribute; ?>' value='5'></td>
                            <?php endif; ?>
                        </tr>
                        <?php $count++; ?>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>

            <div class="actions">
                <?php if ($feedbackGiven) : ?>
                    <p>You have already given feedback for this course.</p>
                <?php else : ?>
                    <button type="button" onclick="validateAndSaveFeedback()">Save</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        function closeFeedback() {
            window.location.href = "student_home.php";
        }

        function saveFeedback() {
            document.getElementsByName('Comments')[0].value = document.getElementById('comments').value;
            document.getElementById("feedbackForm").submit();
        }

        function validateAndSaveFeedback() {
            var questions = document.querySelectorAll('input[type="radio"]');
            var allQuestionsAnswered = true;
            questions.forEach(function(question) {
                var name = question.getAttribute('name');
                var checked = document.querySelector('input[name="' + name + '"]:checked');
                if (!checked) {
                    allQuestionsAnswered = false;
                }
            });
            if (!allQuestionsAnswered) {
                alert("Please answer all questions before submitting.");
                return;
            }
            var urlParams = new URLSearchParams(window.location.search);
            var regNo = urlParams.get('RegNo');
            if (regNo) {
                var feedbackForm = document.getElementById("feedbackForm");
                var courseId = feedbackForm.querySelector('input[name="CourseId"]').value;
                feedbackForm.action = "save_course_feedback.php?CourseId=" + encodeURIComponent(courseId) + "&RegNo=" + encodeURIComponent(regNo);
            }
            document.getElementsByName('Comments')[0].value = document.getElementById('comments').value;
            document.getElementById("feedbackForm").submit();
            setTimeout(function() {
                window.location.href = "student_home.php";
            }, 5000);
        }
    </script>
</body>
</html>
