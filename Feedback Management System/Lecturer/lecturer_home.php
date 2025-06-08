<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Lecturer") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

$LecturerId = $Lecturer_Name = $Department = $email = "";

$sql_lecturer = "SELECT LecturerId, Lecturer_Name, Department, Email FROM lecturer WHERE Email = ?";
if ($stmt = $conn->prepare($sql_lecturer)) {
    $stmt->bind_param("s", $param_email);
    $param_email = $_SESSION["email"];

    if ($stmt->execute()) {
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($LecturerId, $Lecturer_Name, $Department, $email);
            if ($stmt->fetch()) {
                $Lecturer_Name = $Lecturer_Name;
            }
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();
}

$sql_latest_ay = "SELECT MAX(AY) as latest_ay 
                  FROM teach 
                  WHERE LecturerId = ?";
if ($stmt = $conn->prepare($sql_latest_ay)) {
    $stmt->bind_param("s", $param_LecturerId);
    $param_LecturerId = $LecturerId;

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $latest_ay_row = $result->fetch_assoc();
        $latest_ay = $latest_ay_row['latest_ay'];
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();
}

$sql_courses = "SELECT teach.CourseId, course.Course_Name, course.Semester 
                FROM teach 
                JOIN course ON teach.CourseId = course.CourseId 
                WHERE teach.LecturerId = ? AND teach.AY = ?";
if ($stmt = $conn->prepare($sql_courses)) {
    $stmt->bind_param("ss", $param_LecturerId, $param_latest_ay);
    $param_LecturerId = $LecturerId;
    $param_latest_ay = $latest_ay;

    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($CourseId, $Course_Name, $Semester);
            $taught_courses = [];
            while ($stmt->fetch()) {
                $taught_courses[] = array("id" => $CourseId, "name" => $Course_Name, "semester" => $Semester);
            }
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();
}

$departmentOptions = [];
$sql_department = "SHOW COLUMNS FROM lecturer LIKE 'Department'";
$result_department = mysqli_query($conn, $sql_department);
if ($result_department) {
    if (mysqli_num_rows($result_department) == 1) {
        $row_department = mysqli_fetch_array($result_department, MYSQLI_ASSOC);
        $options_department = explode("','", substr($row_department['Type'], 6, -2));
        $departmentOptions = array_map('htmlspecialchars', $options_department);
    }
    mysqli_free_result($result_department);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["lecturerId"], $_POST["lecturerName"], $_POST["department"]) && !empty($_POST["lecturerId"]) && !empty($_POST["lecturerName"]) && !empty($_POST["department"])) {
        $sql_update_lecturer = "UPDATE lecturer SET LecturerId = ?, Lecturer_Name = ?, Department = ? WHERE Email = ?";
        if ($stmt_update_lecturer = $conn->prepare($sql_update_lecturer)) {
            $stmt_update_lecturer->bind_param("ssss", $param_LecturerId, $param_Lecturer_Name, $param_Department, $param_email);
            $param_LecturerId = trim($_POST["lecturerId"]);
            $param_Lecturer_Name = trim($_POST["lecturerName"]);
            $param_Department = trim($_POST["department"]);
            $param_email = $email;

            if ($stmt_update_lecturer->execute()) {
                $sql_update_teach = "UPDATE teach SET LecturerId = ? WHERE LecturerId = ?";
                if ($stmt_update_teach = $conn->prepare($sql_update_teach)) {
                    $stmt_update_teach->bind_param("ss", $param_LecturerId, $param_LecturerId_old);
                    $param_LecturerId_old = $LecturerId;
                    $stmt_update_teach->execute();
                    $stmt_update_teach->close();
                }
                header("location: lecturer_home.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt_update_lecturer->close();
        }
    } else {
        echo "All fields are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Home Page</title>
    <link rel="stylesheet" href="lecturer_home_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="lecturer_home.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-house fa-beat"></i><span class="menu-text">Dashboard</span></a>
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
                    <h2>Welcome back, <?php echo htmlspecialchars($Lecturer_Name); ?>!</h2>
                    <p>Always stay updated in your lecturer portal......</p>
                </div>
                <div class="welcome-image">
                    <img src="../image/11.png" alt="Welcome Image">
                </div>
            </section>

            <div class="info-container">
                <div class="info-item">
                    <i class="fa-solid fa-id-card"></i>
                    <p>Lecturer ID: <?php echo htmlspecialchars($LecturerId); ?></p>
                </div>
                <div class="info-item">
                    <i class="fa-solid fa-building-columns"></i>
                    <p>Department: <?php echo htmlspecialchars($Department); ?></p>
                </div>
            </div>
            <div class="info-container">
                <div class="info-item">
                    <i class="fa-regular fa-envelope"></i>
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
            <button class="edit" onclick="openPopup()"><i class="far fa-pen-to-square" style="margin-right: 5px;"></i>Edit</button>
            <hr>
            <h3>Teaching Courses</h3>
            <table>
                <tr>
                    <th>Course Name</th>
                    <th>Course ID</th>
                    <th>Semester</th>
                    <th>Actions</th>
                </tr>
                <?php
                if (isset($taught_courses)) {
                    foreach ($taught_courses as $course) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($course['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($course['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($course['semester']) . "</td>";
                        echo '<td><a href="view_lecturer_feedback.php?CourseId=' . htmlspecialchars($course['id']) . '&LecturerId=' . htmlspecialchars($LecturerId) . '"><button><i class="fa-solid fa-eye" style="margin-right: 5px;"></i>View Feedback</button></a></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No courses taught.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Edit Lecturer Details</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="lecturerId">Lecturer ID:</label>
                    <input type="text" id="lecturerId" name="lecturerId" value="<?php echo htmlspecialchars($LecturerId); ?>">
                </div>
                <div class="form-group">
                    <label for="lecturerName">Lecturer Name:</label>
                    <input type="text" id="lecturerName" name="lecturerName" value="<?php echo htmlspecialchars($Lecturer_Name); ?>">
                </div>
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select id="department" name="department">
                        <?php foreach ($departmentOptions as $option) : ?>
                            <option value="<?php echo htmlspecialchars($option); ?>" <?php if ($option === $Department) echo 'selected'; ?>><?php echo htmlspecialchars($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit"><i class="far fa-floppy-disk" style="margin-right: 5px;"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_SESSION["message"])) {
        $message = $_SESSION["message"];
        $messageType = $_SESSION["message_type"];
        unset($_SESSION["message"]);
        unset($_SESSION["message_type"]);
    }
    ?>

    <!-- Reset Password Popup -->
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

    <!-- Error Popup -->
    <div id="errorPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeErrorPopup()">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
        </div>
    </div>

    <!-- Success Popup -->
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
            document.getElementById("popup").style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
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
        }

        function closeSuccessPopup() {
            var successPopup = document.getElementById("successPopup");
            successPopup.classList.remove("show");
            successPopup.classList.add("hide");
            setTimeout(function() {
                successPopup.style.display = "none";
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
    </script>
</body>

</html>