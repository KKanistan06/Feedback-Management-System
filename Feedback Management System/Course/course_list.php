<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

$errorMessage = "";

function filterCourses($semester, $courseId, $conn, $itemsPerPage, $offset)
{
    $whereClause = "";

    if (!empty($semester) && !empty($courseId)) {
        $whereClause = "WHERE Semester = '$semester' AND CourseId = '$courseId'";
    } elseif (!empty($semester)) {
        $whereClause = "WHERE Semester = '$semester'";
    } elseif (!empty($courseId)) {
        $whereClause = "WHERE CourseId = '$courseId'";
    }

    $query = "SELECT * FROM course $whereClause ORDER BY Semester LIMIT $itemsPerPage OFFSET $offset";
    $result = mysqli_query($conn, $query);

    return $result;
}

$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$queryCount = "SELECT COUNT(*) as total_count FROM course";
$resultCount = mysqli_query($conn, $queryCount);
$totalItems = mysqli_fetch_assoc($resultCount)['total_count'];
$totalPages = ceil($totalItems / $itemsPerPage);

$querySemesters = "SELECT DISTINCT Semester FROM course";
$resultSemesters = mysqli_query($conn, $querySemesters);

$queryEnumDepartment = "SHOW COLUMNS FROM course LIKE 'Department'";
$resultEnumDepartment = mysqli_query($conn, $queryEnumDepartment);
$rowEnumDepartment = mysqli_fetch_assoc($resultEnumDepartment);
$enumDepartment = substr($rowEnumDepartment['Type'], 5, -1);

$queryEnumSemester = "SHOW COLUMNS FROM student LIKE 'Semester'";
$resultEnumSemester = mysqli_query($conn, $queryEnumSemester);
$rowEnumSemester = mysqli_fetch_assoc($resultEnumSemester);
$enumSemester = substr($rowEnumSemester['Type'], 5, -1);

$departmentOptions = explode("','", substr($enumDepartment, 1, -1));
$semesterOptions = explode("','", substr($enumSemester, 1, -1));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["semester"]) || isset($_POST["courseId"])) {
        $selectedSemester = $_POST["semester"] ?? "";
        $searchCourseId = $_POST["courseId"] ?? "";

        $result = filterCourses($selectedSemester, $searchCourseId, $conn, $itemsPerPage, $offset);
    } else {
        $result = filterCourses("", "", $conn, $itemsPerPage, $offset);
    }
} else {
    $result = filterCourses("", "", $conn, $itemsPerPage, $offset);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course List</title>
    <link rel="stylesheet" href="course_list_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="../MA/managing_assistant_home.php" onclick="expandSidebar(event)"><i class="fa-solid fa-house"></i><span class="menu-text">Dashboard</span></a>
            <a href="../MA/feedback.php" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star"></i><span class="menu-text">Feedback</span></a>
            <a href="../MA/notice.php" onclick="expandSidebar(event)"><i class="fa-solid fa-bell"></i><span class="menu-text">Notice</span></a>
            <a href="../Lecturer/lecturer_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-user-tie"></i><span class="menu-text">Lecturer List</span></a>
            <a href="../Student/student_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-users"></i><span class="menu-text">Students List</span></a>
            <a href="course_list.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-book fa-beat"></i><span class="menu-text">Course List</span></a>
            <a href="../MA/question_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-file-lines"></i><span class="menu-text">Questions List</span></a>
            <a href="course_allocation.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book-medical"></i><span class="menu-text">Course Allocation</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="../MA/index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>

    <div class="content expanded" id="content">
        <form id="filterForm" method="post" style="margin-bottom: 20px;">
            <select name="semester" id="semester" style="width: 36%;">
                <option value="">All Semesters</option>
                <?php
                mysqli_data_seek($resultSemesters, 0);
                while ($row = mysqli_fetch_assoc($resultSemesters)) :
                    $selectedSemester = isset($_POST["semester"]) ? $_POST["semester"] : "";
                ?>
                    <option value="<?php echo $row['Semester']; ?>" <?php if ($selectedSemester == $row['Semester']) echo "selected"; ?>>
                        <?php echo $row['Semester']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" style="margin-right: 20px;"><i class="fa-solid fa-filter fa-beat" style="margin-right: 7px;"></i>Filter</button>

            <input type="text" name="courseId" id="courseId" style="width: 36%;" placeholder="Search By Course ID" value="<?php echo isset($_POST['courseId']) ? $_POST['courseId'] : ''; ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass fa-beat" style="margin-right: 7px;"></i>Search</button>
        </form>

        <!-- Course list -->
        <div class="request-table">
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Semester</th>
                        <th>Credit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr id='row_" . $row['CourseId'] . "'>";
                            echo "<td class='courseId'>" . $row['CourseId'] . "</td>";
                            echo "<td class='courseName'>" . $row['Course_Name'] . "</td>";
                            echo "<td class='semester'>" . $row['Semester'] . "</td>";
                            echo "<td class='credit'>" . $row['Credit'] . "</td>";
                            echo "<td><button class='edit-button' onclick='editCourse(this)'><i class='far fa-edit ' style='margin-right:8px;'></i>Edit</button> <button class='delete-button' data-courseid='" . $row['CourseId'] . "' onclick='deleteCourse(this)'><i class='fa-solid fa-trash' style='margin-right:8px;'></i>Delete</button> <button class='save-button' style='display:none' onclick='saveCourse(this)'>Save</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php if ($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>

            <button class="addCourse" onclick="openAddCoursePopup()"><i class="fa-solid fa-file-circle-plus fa-beat" style="margin-right: 7px;"></i>Add Course</button>
            <button class="uploadExcel" onclick="openUploadExcelPopup()"><i class="fa-solid fa-upload fa-beat" style="margin-right: 7px;"></i>Upload Excel</button>
        </div>
    </div>

    <!-- Add Course popup -->
    <div id="addCoursePopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeAddCoursePopup()">&times;</span>
            <h2>Add New Course</h2>
            <form id="addCourseForm" action="add_course.php" method="post">
                <div class="form-group">
                    <label for="newCourseId">Course ID:</label>
                    <input type="text" id="newCourseId" name="newCourseId" required>
                </div>
                <div class="form-group">
                    <label for="newCourseName">Course Name:</label>
                    <input type="text" id="newCourseName" name="newCourseName" required>
                </div>
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select name="department" id="department" required>
                        <option value="">Select Department</option>
                        <?php
                        foreach ($departmentOptions as $departmentOption) {
                            echo "<option value='$departmentOption'>$departmentOption</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester">Semester:</label>
                    <select name="semester" id="semester" required>
                        <option value="">Select Semester</option>
                        <?php
                        foreach ($semesterOptions as $semesterOption) {
                            echo "<option value='$semesterOption'>$semesterOption</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="newCredit">Credit:</label>
                    <input type="text" id="newCredit" name="newCredit" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Add Course">
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Excel popup -->
    <div id="uploadExcelPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeUploadExcelPopup()">&times;</span>
            <h3 style="margin-bottom: 35px;">Upload Excel File</h3>
            <form id="uploadExcelForm" action="upload_courses.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="excelFile"  style="margin-bottom: 7px;">Select course list Excel File</label>
                    <input type="file" id="excelFile" name="excelFile" accept=".xls, .xlsx" required>
                </div>
                <button type="submit" class="uploadExcel"><i class="fa-solid fa-upload fa-beat" style="margin-right: 7px;"></i>Upload</button>
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

        function editCourse(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            for (var i = 1; i < cells.length - 1; i++) {
                var content = cells[i].textContent;
                cells[i].innerHTML = '<input type="text" value="' + content + '">';
            }
            button.innerHTML = '<i class="fa-solid fa-floppy-disk fa-beat" style="margin-right:5px;"></i> Save';
            button.setAttribute("onclick", "saveCourse(this)");
            button.classList.remove("edit-button");
            button.classList.add("save-button");
        }

        function saveCourse(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            var courseId = cells[0].textContent;
            var courseName = cells[1].querySelector("input").value;
            // var department = cells[2].querySelector("input").value;
            var semester = cells[2].querySelector("input").value;
            var credit = cells[3].querySelector("input").value;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText === "success") {
                        alert("Course details updated successfully.");
                        button.innerHTML = '<i class="far fa-edit" style="margin-right:5px; "></i> Edit';
                        button.setAttribute("onclick", "editCourse(this)");
                        button.classList.remove("save-button");
                        button.classList.add("edit-button");
                        for (var i = 1; i < cells.length - 1; i++) {
                            var inputVal = cells[i].querySelector("input").value;
                            cells[i].textContent = inputVal;
                        }
                    } else {
                        alert("Failed to update course details.");
                    }
                }
            };
            xhttp.open("POST", "edit_course.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("courseId=" + encodeURIComponent(courseId) + "&courseName=" + encodeURIComponent(courseName) + "&department=" + encodeURIComponent(department) + "&semester=" + encodeURIComponent(semester) + "&credit=" + encodeURIComponent(credit));
        }

        function deleteCourse(button) {
            var courseId = button.getAttribute("data-courseid");
            if (confirm("Are you sure you want to delete this course?")) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText === "success") {
                            alert("Course deleted successfully.");
                            button.parentNode.parentNode.remove();
                        } else {
                            alert("Failed to delete course.");
                        }
                    }
                };
                xhttp.open("POST", "delete_course.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("courseId=" + encodeURIComponent(courseId));
            }
        }

        function openAddCoursePopup() {
            document.getElementById("addCoursePopup").style.display = "block";
        }

        function closeAddCoursePopup() {
            document.getElementById("addCoursePopup").style.display = "none";
        }

        function openUploadExcelPopup() {
            document.getElementById("uploadExcelPopup").style.display = "block";
        }

        function closeUploadExcelPopup() {
            document.getElementById("uploadExcelPopup").style.display = "none";
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
    </script>

</body>

</html>
