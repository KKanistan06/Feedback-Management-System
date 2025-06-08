<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Student") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

$Student_Name = $RegNo = $BatchNo = $Semester = $email = "";

$sql_student = "SELECT Student_Name, RegNo, BatchNo, Semester, email FROM student WHERE email = ?";
if ($stmt = $conn->prepare($sql_student)) {
    $stmt->bind_param("s", $param_email);
    $param_email = $_SESSION["email"];
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($Student_Name, $RegNo, $BatchNo, $Semester, $email);
            if ($stmt->fetch()) {
                $Student_Name = $Student_Name;
            }
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();
}

$_SESSION["BatchNo"] = $BatchNo;

$sql_batch = "SHOW COLUMNS FROM student LIKE 'BatchNo'";
$sql_semester = "SHOW COLUMNS FROM student LIKE 'Semester'";
$batchOptions = $semesterOptions = [];

$result_batch = mysqli_query($conn, $sql_batch);
$result_semester = mysqli_query($conn, $sql_semester);

if ($result_batch && $result_semester) {
    if (mysqli_num_rows($result_batch) == 1 && mysqli_num_rows($result_semester) == 1) {
        $row_batch = mysqli_fetch_array($result_batch, MYSQLI_ASSOC);
        $row_semester = mysqli_fetch_array($result_semester, MYSQLI_ASSOC);
        $batchOptions = array_map('htmlspecialchars', explode("','", substr($row_batch['Type'], 6, -2)));
        $semesterOptions = array_map('htmlspecialchars', explode("','", substr($row_semester['Type'], 6, -2)));
    }
}

mysqli_free_result($result_batch);
mysqli_free_result($result_semester);

$sql_latest_ay = "SELECT MAX(AY) AS LatestAY FROM enroll WHERE RegNo = ?";
$latest_ay = null;

if ($stmt = $conn->prepare($sql_latest_ay)) {
    $stmt->bind_param("s", $param_RegNo);
    $param_RegNo = $RegNo;
    if ($stmt->execute()) {
        $stmt->bind_result($latest_ay);
        $stmt->fetch();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();
}

$sql_courses = "SELECT c.Course_Name, e.CourseId, t.LecturerId 
                FROM enroll e 
                JOIN course c ON e.CourseId = c.CourseId 
                JOIN teach t ON t.CourseId = c.CourseId 
                WHERE e.RegNo = ? AND e.AY = ?";

if ($stmt = $conn->prepare($sql_courses)) {
    $stmt->bind_param("ss", $param_RegNo, $latest_ay);
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($Course_Name, $CourseId, $LecturerId);
            $enrolled_courses = [];
            while ($stmt->fetch()) {
                if (!isset($enrolled_courses[$CourseId])) {
                    $enrolled_courses[$CourseId] = [
                        "name" => $Course_Name,
                        "lecturers" => []
                    ];
                }
                $enrolled_courses[$CourseId]["lecturers"][] = $LecturerId;
            }
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();
}

function validatePasswordStrength($password) {
    if (
        strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*()\-_=+{};:,<.>ยง~]/', $password)
    ) {
        return false;
    }
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION["email"];
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];

    if (validatePassword($email, $currentPassword)) {
        if ($newPassword === $confirmPassword) {
            if (validatePasswordStrength($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE email = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $hashedPassword, $email);
                    if ($stmt->execute()) {
                        header("location: profile.php");
                        exit;
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            } else {
                echo "New password does not meet the strength requirements.";
            }
        } else {
            echo "New password and confirm password do not match.";
        }
    } else {
        echo "Incorrect current password.";
    }
}

function hasFeedbackBeenGiven($RegNo, $CourseId, $conn) {
    $sql = "SELECT * FROM gives_course_feedback WHERE RegNo = ? AND CourseId = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $RegNo, $CourseId);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                return true;
            }
        }
    }
    return false;
}

function hasLecturerFeedbackBeenGiven($RegNo, $CourseId, $LecturerId, $conn) {
    $sql = "SELECT * FROM gives_lecturer_feedback WHERE RegNo = ? AND CourseId = ? AND LecturerId = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $RegNo, $CourseId, $LecturerId);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                return true;
            }
        }
    }
    return false;
}

function isCourseFeedbackAllowed($CourseId, $conn) {
    $allow = 0;
    $sql = "SELECT `allow` FROM course_feedback_notice WHERE CourseId = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $CourseId);
        if ($stmt->execute()) {
            $stmt->bind_result($allow);
            $stmt->fetch();
            return $allow == 1;
        }
    }
    return false;
}

function isLecturerFeedbackAllowed($CourseId, $AY, $conn) {
    $allow = 0;
    $sql = "SELECT `allow` FROM lecturer_feedback_notice WHERE CourseId = ? AND AY = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $CourseId, $AY);
        if ($stmt->execute()) {
            $stmt->bind_result($allow);
            $stmt->fetch();
            return $allow == 1;
        }
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home Page</title>
    <link rel="stylesheet" href="student_home_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>

    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="student_home.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-house fa-beat"></i><span class="menu-text">Dashboard</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="../MA/index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>
    </div>
    <div class="content expanded" id="content">

        <div class="container">
            <section class="welcome-section">
                <div class="welcome-text">
                    <p class="date" id="currentDateTime"></p>
                    <h2>Welcome back, <?php echo htmlspecialchars($Student_Name); ?>!</h2>
                    <p>Always stay updated in your student portal.......</p>
                </div>
                <div class="welcome-image">
                    <img src="../image/2.png" alt="Welcome Image">
                </div>
            </section>
            <div class="info-container">
                <div class="info-item">
                    <i class="fa-solid fa-id-card"></i>
                    <p>REGISTRATION NO: <?php echo htmlspecialchars($RegNo); ?></p>
                </div>
                <div class="info-item">
                    <i class="fa-solid fa-users-rectangle"></i>
                    <p>BATCH NO: <?php echo htmlspecialchars($BatchNo); ?></p>
                </div>
            </div>

            <div class="info-container">
                <div class="info-item">
                    <i class="fa-solid fa-book-open"></i>
                    <p>SEMESTER: <?php echo htmlspecialchars($Semester); ?></p>
                </div>
                <div class="info-item">
                    <i class="fa-regular fa-envelope"></i>
                    <p>EMAIL: <?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
            <button class="edit" onclick="openPopup()"><i class="far fa-pen-to-square" style="margin-right: 5px;"></i>Edit</button>
            <hr>
            <h3>Enrolled Courses</h3>
            <table>
                <tr>
                    <th>Course Name</th>
                    <th>Course ID</th>
                    <th>Actions</th>
                </tr>
                <?php
                if (isset($enrolled_courses)) {
                    foreach ($enrolled_courses as $courseId => $course) {
                        $allLecturerFeedbackGiven = true;
                        foreach ($course['lecturers'] as $lecturerId) {
                            if (!hasLecturerFeedbackBeenGiven($RegNo, $courseId, $lecturerId, $conn)) {
                                $allLecturerFeedbackGiven = false;
                                break;
                            }
                        }

                        $courseFeedbackAllowed = isCourseFeedbackAllowed($courseId, $conn);
                        $lecturerFeedbackAllowed = isLecturerFeedbackAllowed($courseId, $latest_ay, $conn);

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($course['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($courseId) . "</td>";
                        echo "<td>";
                        echo "<div class='feedback-buttons'>";
                        if (hasFeedbackBeenGiven($RegNo, $courseId, $conn)) {
                            echo "<button class='given-feedback-button'><i class='fa-solid fa-circle-check' style='margin-right:10px;'></i>Feedback Given</button>";
                        } else {
                            if ($courseFeedbackAllowed) {
                                echo "<a href='course_feedback.php?CourseId=" . htmlspecialchars($courseId) . "&RegNo=" . htmlspecialchars($RegNo) . "'><button class='course_button'><i class='fa-solid fa-book' style='margin-right:10px;'></i>Course Feedback</button></a>";
                            } else {
                                echo "<button class='disabled-button' title='Feedback not allowed'><i class='fa-solid fa-book' style='margin-right:10px;'></i>Course Feedback</button>";
                            }
                        }
                        if ($allLecturerFeedbackGiven) {
                            echo "<button class='given-feedback-button'><i class='fa-solid fa-circle-check' style='margin-right:10px;'></i>Feedback Given</button>";
                        } else {
                            if ($lecturerFeedbackAllowed) {
                                echo "<a href='lecturer_feedback.php?CourseId=" . htmlspecialchars($courseId) . "&RegNo=" . htmlspecialchars($RegNo) . "'><button class='feedback-button'><i class='fa-solid fa-user-tie' style='margin-right:10px;'></i>Lecturer Feedback</button></a>";
                            } else {
                                echo "<button class='disabled-button' title='Feedback not allowed'><i class='fa-solid fa-user-tie' style='margin-right:10px;'></i>Lecturer Feedback</button>";
                            }
                        }
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No courses enrolled.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div id="popup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Edit Student Details</h2>
            <form action="update_student.php" method="post">
                <div class="form-group">
                    <label for="studentName">Name</label>
                    <input type="text" id="studentName" name="studentName" value="<?php echo htmlspecialchars($Student_Name); ?>">
                </div>
                <div class="form-group">
                    <label for="regNo">Reg No</label>
                    <input type="text" id="regNo" name="regNo" value="<?php echo htmlspecialchars($RegNo); ?>">
                </div>
                <div class="form-group">
                    <label for="batchNo">Batch No</label>
                    <select id="batchNo" name="batchNo">
                        <?php foreach ($batchOptions as $option) : ?>
                            <option value="<?php echo $option; ?>" <?php if ($option === $BatchNo) echo 'selected'; ?>>
                                <?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester">
                        <?php foreach ($semesterOptions as $option) : ?>
                            <option value="<?php echo $option; ?>" <?php if ($option === $Semester) echo 'selected'; ?>>
                                <?php echo $option; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit"><i class="far fa-floppy-disk" style="margin-right: 5px;"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Reset Popup -->
    <div id="resetPasswordPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closeResetPasswordPopup()">&times;</span>
            <h2>Reset Password</h2>
            <form action="reset_password.php" method="post">
                <div class="form-group">
                    <label for="currentPassword">Current Password:</label>
                    <input type="password" id="currentPassword" name="currentPassword" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                <div class="form-group">
                    <button type="submit"><i class="fa-solid fa-unlock-keyhole" style="margin-right: 5px;"></i> Reset password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Disabled Button Popup -->
    <div id="disabledButtonPopup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closeDisabledButtonPopup()">&times;</span>
            <h2></h2>
            <p>Feedback is currently not allowed for this course. Please try again later.</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });

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

        function openPopup() {
            var popup = document.getElementById("popup");
            popup.classList.remove("hide");
            popup.classList.add("show");
            popup.style.display = "block";
        }

        function closePopup() {
            var popup = document.getElementById("popup");
            popup.classList.remove("show");
            popup.classList.add("hide");
            setTimeout(function() {
                popup.style.display = "none";
            }, 500);
        }

        function openResetPasswordPopup() {
            closePopup();
            var resetPopup = document.getElementById("resetPasswordPopup");
            resetPopup.classList.remove("hide");
            resetPopup.classList.add("show");
            resetPopup.style.display = "block";
        }

        function closeResetPasswordPopup() {
            var resetPopup = document.getElementById("resetPasswordPopup");
            resetPopup.classList.remove("show");
            resetPopup.classList.add("hide");
            setTimeout(function() {
                resetPopup.style.display = "none";
            }, 500);
        }

        function openDisabledButtonPopup() {
            var disabledPopup = document.getElementById("disabledButtonPopup");
            disabledPopup.classList.remove("hide");
            disabledPopup.classList.add("show");
            disabledPopup.style.display = "block";
        }

        function closeDisabledButtonPopup() {
            var disabledPopup = document.getElementById("disabledButtonPopup");
            disabledPopup.classList.remove("show");
            disabledPopup.classList.add("hide");
            setTimeout(function() {
                disabledPopup.style.display = "none";
            }, 500);
        }

        function updateDateTime() {
            const now = new Date();
            const dateOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('en-US', dateOptions);
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            document.getElementById('currentDateTime').textContent = `${dateString}, ${timeString}`;
        }

        document.addEventListener("click", function(event) {
            if (event.target.classList.contains('disabled-button')) {
                openDisabledButtonPopup();
            }
        });

        document.addEventListener("mouseover", function(event) {
            if (event.target.classList.contains('disabled-button')) {
                event.target.setAttribute('title', 'Feedback not allowed. Please try again later.');
            }
        });
    </script>
</body>

</html>
