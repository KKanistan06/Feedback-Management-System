<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: index.php");
    exit;
}
require_once "config.php";

$courseQuery = "SELECT CourseId FROM course";
$courseResult = mysqli_query($conn, $courseQuery);

$lecturerQuery = "SELECT DISTINCT CourseId, AY FROM teach";
$lecturerResult = mysqli_query($conn, $lecturerQuery);

function isAllowed($table, $courseId, $AY = null)
{
    global $conn;
    if ($table == 'course_feedback_notice') {
        $query = "SELECT Allow FROM $table WHERE CourseId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
    } else {
        $query = "SELECT Allow FROM $table WHERE CourseId = ? AND AY = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $courseId, $AY);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $allow);
    mysqli_stmt_fetch($stmt);
    return $allow == 1;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Settings</title>
    <link rel="stylesheet" href="notice_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style></style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.toggle-switch').on('change', function() {
                var form = $(this).closest('form');
                var checked = $(this).is(':checked') ? 1 : 0;
                $.ajax({
                    type: 'POST',
                    url: 'notice.php',
                    data: form.serialize() + '&allow=' + checked,
                    success: function(response) {
                        console.log(response);
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="managing_assistant_home.php" onclick="expandSidebar(event)"><i class="fa-solid fa-house"></i><span class="menu-text">Dashboard</span></a>
            <a href="feedback.php" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star"></i><span class="menu-text">Feedback</span></a>
            <a href="notice.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-bell fa-beat"></i><span class="menu-text">Notice</span></a>
            <a href="../Lecturer/lecturer_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-user-tie"></i><span class="menu-text">Lecturer List</span></a>
            <a href="../Student/student_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-users"></i><span class="menu-text">Students List</span></a>
            <a href="../Course/course_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book"></i><span class="menu-text">Course List</span></a>
            <a href="question_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-file-lines"></i><span class="menu-text">Questions List</span></a>
            <a href="../Course/course_allocation.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book-medical"></i><span class="menu-text">Course Allocation</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>

    <div class="content expanded" id="content">
        <h2>Notice Settings</h2>
        <div class="notice-category">
            <h3>Course Feedback</h3>
            <?php while ($row = mysqli_fetch_assoc($courseResult)) : ?>
                <form method="post" class="toggle-item">
                    <div class="lecturer-feedback-detail">
                        <label><?php echo $row['CourseId']; ?></label>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="courseId" value="<?php echo $row['CourseId']; ?>">
                        <input type="hidden" name="type" value="course">
                        <input type="checkbox" name="allow" class="toggle-switch" <?php echo isAllowed('course_feedback_notice', $row['CourseId']) ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                </form>
            <?php endwhile; ?>
        </div>
        <div class="notice-category">
            <h3>Lecturer Feedback</h3>
            <?php mysqli_data_seek($lecturerResult, 0); ?>
            <?php while ($row = mysqli_fetch_assoc($lecturerResult)) : ?>
                <form method="post" class="toggle-item">
                    <div class="lecturer-feedback-detail">
                        <div class="space"><span><?php echo $row['CourseId']; ?></span></div>
                        <div class="space"><span><?php echo $row['AY']; ?></span></div>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="courseId" value="<?php echo $row['CourseId']; ?>">
                        <input type="hidden" name="AY" value="<?php echo $row['AY']; ?>">
                        <input type="hidden" name="type" value="lecturer">
                        <input type="checkbox" name="allow" class="toggle-switch" <?php echo isAllowed('lecturer_feedback_notice', $row['CourseId'], $row['AY']) ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                </form>
            <?php endwhile; ?>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var content = document.getElementById("content");
            var logoContainer = document.querySelector(".logo-container");
            var toggleIcon = document.getElementById("toggleIcon");
            if (sidebar.classList.contains("collapsed")) {
                sidebar.classList.remove("collapsed");
                sidebar.classList.add("expanded");
                content.classList.remove("expanded");
                content.classList.add("collapsed");
                logoContainer.style.display = "block";
                toggleIcon.classList.remove("fa-angle-double-right");
                toggleIcon.classList.add("fa-angle-double-left");
            } else {
                sidebar.classList.remove("expanded");
                sidebar.classList.add("collapsed");
                content.classList.remove("collapsed");
                content.classList.add("expanded");
                logoContainer.style.display = "none";
                toggleIcon.classList.remove("fa-angle-double-left");
                toggleIcon.classList.add("fa-angle-double-right");
            }
        }

        function expandSidebar(event) {
            var sidebar = document.getElementById("sidebar");
            var content = document.getElementById("content");
            var logoContainer = document.querySelector(".logo-container");
            sidebar.classList.remove("collapsed");
            sidebar.classList.add("expanded");
            content.classList.remove("expanded");
            content.classList.add("collapsed");
            logoContainer.style.display = "block";
            var toggleIcon = document.getElementById("toggleIcon");
            toggleIcon.classList.remove("fa-angle-double-left");
            toggleIcon.classList.add("fa-angle-double-right");
        }

        function openResetPasswordPopup() {
            console.log("Reset password popup opened.");
        }
    </script>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST['courseId'] ?? null;
    $AY = $_POST['AY'] ?? null;
    $allow = isset($_POST['allow']) ? $_POST['allow'] : 0;

    if ($courseId && $AY && $_POST['type'] === 'lecturer') {
        $query = "SELECT * FROM lecturer_feedback_notice WHERE CourseId = ? AND AY = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $courseId, $AY);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $query = "UPDATE lecturer_feedback_notice SET Allow = ? WHERE CourseId = ? AND AY = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iss", $allow, $courseId, $AY);
        } else {
            $query = "INSERT INTO lecturer_feedback_notice (CourseId, AY, Allow) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $courseId, $AY, $allow);
        }
    } elseif ($courseId && $_POST['type'] === 'course') {
        $query = "SELECT * FROM course_feedback_notice WHERE CourseId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $courseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $query = "UPDATE course_feedback_notice SET Allow = ? WHERE CourseId = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "is", $allow, $courseId);
        } else {
            $query = "INSERT INTO course_feedback_notice (CourseId, Allow) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $courseId, $allow);
        }
    }

    if (isset($stmt)) {
        mysqli_stmt_execute($stmt);
        echo "Success";
    } else {
        echo "Error in processing request.";
    }
}
mysqli_close($conn);
?>
