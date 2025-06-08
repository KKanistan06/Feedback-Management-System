<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

function fetchAllLecturerFeedbacks($conn)
{
    $query = "SELECT l.LecturerId, l.Lecturer_Name, c.Course_Name, AVG(
                    (lf.LQ01 + lf.LQ02 + lf.LQ03 + lf.LQ04 + lf.LQ05 + lf.LQ06 + lf.LQ07 + lf.LQ08 + lf.LQ09 + lf.LQ10 + lf.LQ11 + lf.LQ12) / 12
                ) AS average_score 
              FROM lecturer_feedback lf
              JOIN lecturer l ON lf.LecturerId = l.LecturerId
              JOIN course c ON lf.CourseId = c.CourseId
              GROUP BY l.LecturerId, l.Lecturer_Name, c.Course_Name
              ORDER BY average_score DESC";
    $result = mysqli_query($conn, $query);
    return $result;
}

$allLecturerFeedbacks = fetchAllLecturerFeedbacks($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Lecturer Feedback Summary</title>
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
            <a href="feedback.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star"></i><span class="menu-text">Feedback</span></a>
            <a href="question_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-file-lines"></i><span class="menu-text">Questions List</span></a>
            <a href="../Lecturer/lecturer_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-user-tie"></i><span class="menu-text">Lecturer List</span></a>
            <a href="../Student/student_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-users"></i><span class="menu-text">Students List</span></a>
            <a href="../Course/course_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book"></i><span class="menu-text">Course List</span></a>
            <a href="../Course/course_allocation.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book-medical"></i><span class="menu-text">Course Allocation</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>

    <div class="content expanded" id="content">
        <button class="back-button" onclick="location.href='feedback.php'"><i class="fa-solid fa-arrow-left" style="margin-right: 7px;"></i> Back</button>
        <div class="feedback-summary">
            <h2>All Lecturer Feedback Summary</h2>
            <div class="summary-container">
                <?php
                if ($allLecturerFeedbacks && mysqli_num_rows($allLecturerFeedbacks) > 0) {
                    while ($row = mysqli_fetch_assoc($allLecturerFeedbacks)) {
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

                        $lecturerName = strlen($row['Lecturer_Name']) > 30 ? substr($row['Lecturer_Name'], 0, 30) . "..." : $row['Lecturer_Name'];

                        echo "<div class='summary-box'>
                            <div class='box-content'>
                                <h3 class='lecturer-name' title='{$lecturerName}'>{$lecturerName}</h3>
                                <h3 class='course-name' title='{$row['Course_Name']}'>{$row['Course_Name']}</h3>
                                <p class='average-score'>$starRating</p>
                            </div>
                            <a href='view_lecturer_summary.php?course_name=" . urlencode($row['Course_Name']) . "&lecturer_id=" . urlencode($row['LecturerId']) . "' class='view-details'>View Details <i class='fa-solid fa-arrow-right'></i></a>
                        </div>";
                    }
                } else {
                    echo "<p>No feedback available for lecturers.</p>";
                }
                ?>
            </div>
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