<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

function fetchCourseFeedbackSummary($conn)
{
    $query = "SELECT c.CourseId, c.Course_Name, AVG(
                    (cf.CQ01 + cf.CQ02 + cf.CQ03 + cf.CQ04 + cf.CQ05 + cf.CQ06 + cf.CQ07 + cf.CQ08 + cf.CQ09 + cf.CQ10 + cf.CQ11 + cf.CQ12 + cf.CQ13 + cf.CQ14 + cf.CQ15) / 15
                ) AS average_score 
              FROM course_feedback cf
              JOIN course c ON cf.CourseId = c.CourseId
              GROUP BY c.CourseId, c.Course_Name
              ORDER BY average_score DESC
              LIMIT 3";  
    $result = mysqli_query($conn, $query);
    return $result;
}

function fetchLecturerFeedbackSummary($conn)
{
    $query = "SELECT l.LecturerId, l.Lecturer_Name, c.Course_Name, AVG(
                    (lf.LQ01 + lf.LQ02 + lf.LQ03 + lf.LQ04 + lf.LQ05 + lf.LQ06 + lf.LQ07 + lf.LQ08 + lf.LQ09 + lf.LQ10 + lf.LQ11 + lf.LQ12) / 12
                ) AS average_score 
              FROM lecturer_feedback lf
              JOIN lecturer l ON lf.LecturerId = l.LecturerId
              JOIN course c ON lf.CourseId = c.CourseId
              GROUP BY l.LecturerId, l.Lecturer_Name, c.Course_Name
              ORDER BY average_score DESC
              LIMIT 3";  
    $result = mysqli_query($conn, $query);
    return $result;
}

$courseFeedbacks = fetchCourseFeedbackSummary($conn);
$lecturerFeedbacks = fetchLecturerFeedbackSummary($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Summary</title>
    <link rel="stylesheet" href="feedback_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="managing_assistant_home.php" onclick="expandSidebar(event)"><i class="fa-solid fa-house"></i><span class="menu-text">Dashboard</span></a>
            <a href="feedback.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star fa-beat"></i><span class="menu-text">Feedback</span></a>
            <a href="notice.php" onclick="expandSidebar(event)"><i class="fa-solid fa-bell"></i><span class="menu-text">Notice</span></a>
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
        <div class="feedback-summary">
            <div class="summary-header">
                <h2>Course Feedback Summary</h2>
                <button class="see-all" onclick="location.href='course_summary.php'">See All</button>
            </div>
            <div class="summary-container">
                <?php
                if ($courseFeedbacks && mysqli_num_rows($courseFeedbacks) > 0) {
                    while ($row = mysqli_fetch_assoc($courseFeedbacks)) {
                        $averageScore = round($row['average_score'], 3);
                        $fullStars = floor($averageScore);
                        $partialStar = $averageScore - $fullStars;

                        $starRating = "";
                        for ($i = 0; $i < $fullStars; $i++) {
                            $starRating .= "<i class='fa-solid fa-star'></i>";
                        }
                        if ($partialStar > 0) {
                            $starRating .= "<i class='fa-solid fa-star-half-alt'></i>";
                        }

                        $courseName = strlen($row['Course_Name']) > 20 ? substr($row['Course_Name'], 0, 30) . "..." : $row['Course_Name'];

                        echo "<div class='summary-box'>
                            <div class='box-content'>
                                <h3 class='course-name' title='{$row['Course_Name']}'>{$courseName}</h3>
                                <h3 class='course-id'>{$row['CourseId']}</h3>
                                <p class='average-score'>$starRating</p>
                            </div>
                            <a href='view_course_feedback_summary.php?id={$row['CourseId']}' class='view-details'>View Details <i class='fa-solid fa-arrow-right'></i></a>
                        </div>";
                    }
                } else {
                    echo "<p>No feedback available for courses.</p>";
                }
                ?>
            </div>
        </div>
                <br>
        <div class="feedback-summary">
            <div class="summary-header">
                <h2>Lecturer Feedback Summary</h2>
                <button class="see-all" onclick="location.href='lecturer_summary.php'">See All</button>
            </div>
            <div class="summary-container">
                <?php
                if ($lecturerFeedbacks && mysqli_num_rows($lecturerFeedbacks) > 0) {
                    while ($row = mysqli_fetch_assoc($lecturerFeedbacks)) {
                        $averageScore = round($row['average_score'], 2);
                        $fullStars = floor($averageScore);
                        $partialStar = $averageScore - $fullStars;

                        $starRating = "";
                        for ($i = 0; $i < $fullStars; $i++) {
                            $starRating .= "<i class='fa-solid fa-star'></i>";
                        }
                        if ($partialStar > 0) {
                            $starRating .= "<i class='fa-solid fa-star-half-alt'></i>";
                        }

                        $lecturerName = strlen($row['Lecturer_Name']) > 34 ? substr($row['Lecturer_Name'], 0, 30) . "..." : $row['Lecturer_Name'];

                        echo "<div class='summary-box'>
                <div class='box-content'>
                  <h3 class='lecturer-name' title='{$row['Lecturer_Name']}'>{$lecturerName}</h3>
                  <h3 class='course-name' title='{$row['Course_Name']}'>{$row['Course_Name']}</h3>
                  <p class='average-score'>$starRating</p>
                </div>
                <a href='view_lecturer_feedback_summary.php?course_name=" . urlencode($row['Course_Name']) . "&lecturer_id=" . urlencode($row['LecturerId']) . "' class='view-details'>View Details <i class='fa-solid fa-arrow-right'></i></a>
              </div>";
                    }
                } else {
                    echo "<p>No feedback available for lecturers.</p>";
                }
                ?>
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
        </script>

</body>

</html>
