<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: index.php");
    exit;
}
require_once "config.php";

$studentCountQuery = "SELECT COUNT(*) as count FROM student";
$studentCountResult = mysqli_query($conn, $studentCountQuery);
$studentCountRow = mysqli_fetch_assoc($studentCountResult);
$studentCount = $studentCountRow['count'];

$lecturerCountQuery = "SELECT COUNT(*) as count FROM lecturer";
$lecturerCountResult = mysqli_query($conn, $lecturerCountQuery);
$lecturerCountRow = mysqli_fetch_assoc($lecturerCountResult);
$lecturerCount = $lecturerCountRow['count'];

$courseCountQuery = "SELECT COUNT(*) as count FROM course";
$courseCountResult = mysqli_query($conn, $courseCountQuery);
$courseCountRow = mysqli_fetch_assoc($courseCountResult);
$courseCount = $courseCountRow['count'];

$successMessage = $errorMessage = "";
$query = "SELECT email FROM user WHERE approved = 0";
$result = mysqli_query($conn, $query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["accept"])) {
        $email = $_POST["email"];
        $updateQuery = "UPDATE user SET approved = 1 WHERE email = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "User request successfully accepted.";
        } else {
            $errorMessage = "Error: Unable to accept user request.";
        }
    } elseif (isset($_POST["reject"])) {
        $email = $_POST["email"];
        $deleteQuery = "DELETE FROM user WHERE email = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "User request successfully rejected.";
        } else {
            $errorMessage = "Error: Unable to reject user request.";
        }
    }

    $query = "SELECT email FROM user WHERE approved = 0";
    $result = mysqli_query($conn, $query);

    ob_start();
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr id='" . $row['email'] . "'>";
        echo "<td>" . (isset($row['email']) ? $row['email'] : '') . "</td>";
        echo "<td>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='email' value='" . $row['email'] . "'>";
        echo "<button type='submit' name='accept' value='accept' class='btn-accept' onclick='handleRequest(event, \"accept\", \"" . $row['email'] . "\")'><i class='fas fa-check'></i> Accept</button>";
        echo "<button type='submit' name='reject' value='reject' class='btn-reject' onclick='handleRequest(event, \"reject\", \"" . $row['email'] . "\")'><i class='fas fa-times'></i> Reject</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    $updatedTable = ob_get_clean();
    echo json_encode(['success' => true, 'message' => $successMessage, 'tableContent' => $updatedTable]);
    exit;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Managing Assistant</title>
    <link rel="stylesheet" href="managing_assistant_home_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="managing_assistant_home.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-house fa-beat"></i><span class="menu-text">Dashboard</span></a>
            <a href="feedback.php" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star"></i><span class="menu-text">Feedback</span></a>
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
        <section class="welcome-section">
            <div class="welcome-text">
                <p class="date" id="currentDateTime"></p>
                <h2>Welcome back, Admin!</h2>
                <p>Always stay updated in your admin portal.......</p>
            </div>
            <div class="welcome-image">
                <img src="../image/12.png" alt="Welcome Image">
            </div>
        </section>
        <div class="icon-boxes">
            <div class="icon-box" onclick="location.href='../Student/student_list.php'">
                <div class="icon"><i class="fa-solid fa-users"></i></div>
                <h3>Students</h3>
                <p><?php echo $studentCount >= 10 ? "200+" : $studentCount; ?></p>
            </div>
            <div class="icon-box" onclick="location.href='../Lecturer/lecturer_list.php'">
                <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
                <h3>Lecturers</h3>
                <p><?php echo $lecturerCount > 4 ? "30+" : $lecturerCount; ?></p>
            </div>
            <div class="icon-box" onclick="location.href='../Course/course_list.php'">
                <div class="icon"><i class="fa-solid fa-book"></i></div>
                <h3>Courses</h3>
                <p><?php echo $courseCount > 10 ? "40+" : $courseCount; ?></p>
            </div>
        </div>

        <h2>Pending Request</h2>
        <div class="request-table">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="pendingRequests">
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr id='" . $row['email'] . "'>";
                        echo "<td>" . (isset($row['email']) ? $row['email'] : '') . "</td>";
                        echo "<td>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='email' value='" . $row['email'] . "'>";
                        echo "<button type='submit' name='accept' value='accept' class='btn-accept' onclick='handleRequest(event, \"accept\", \"" . $row['email'] . "\")'><i class='fas fa-check'></i> Accept</button>";
                        echo "<button type='submit' name='reject' value='reject' class='btn-reject' onclick='handleRequest(event, \"reject\", \"" . $row['email'] . "\")'><i class='fas fa-times'></i> Reject</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Popups -->
    <div id="resetPasswordPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeResetPasswordPopup()">&times;</span>
            <h2>Reset Password</h2>
            <form id="resetPasswordForm" action="reset_password.php" method="post">
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
                    <input type="submit" value="Reset Password">
                </div>
            </form>
        </div>
    </div>
    <div id="errorPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeErrorPopup()">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
        </div>
    </div>
    <div id="successPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeSuccessPopup()">&times;</span>
            <h2>Success</h2>
            <p id="successMessage"></p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });

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

        function showErrorPopup(message) {
            var errorPopup = document.getElementById("errorPopup");
            var errorMessage = document.getElementById("errorMessage");
            errorMessage.textContent = message;
            errorPopup.classList.remove("hide");
            errorPopup.classList.add("show");
            errorPopup.style.display = "block";
        }

        function closeErrorPopup() {
            var errorPopup = document.getElementById("errorPopup");
            errorPopup.classList.remove("show");
            errorPopup.classList.add("hide");
            setTimeout(function() {
                errorPopup.style.display = "none";
            }, 500);
        }

        function showSuccessPopup(message) {
            var successPopup = document.getElementById("successPopup");
            var successMessage = document.getElementById("successMessage");
            successMessage.textContent = message;
            successPopup.classList.remove("hide");
            successPopup.classList.add("show");
            successPopup.style.display = "block";
            setTimeout(closeSuccessPopup, 2000);
        }

        function closeSuccessPopup() {
            var successPopup = document.getElementById("successPopup");
            successPopup.classList.remove("show");
            successPopup.classList.add("hide");
            setTimeout(function() {
                successPopup.style.display = "none";
            }, 500);
        }

        function handleRequest(event, action, email) {
            event.preventDefault();

            var formData = new FormData();
            formData.append(action, action);
            formData.append('email', email);

            fetch('managing_assistant_home.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('pendingRequests').innerHTML = data.tableContent;
                        showSuccessPopup(data.message);
                    } else {
                        showErrorPopup('Error: Unable to process request.');
                    }
                })
                .catch(error => {
                    showErrorPopup('Error: Unable to process request.');
                });
        }
    </script>
</body>

</html>