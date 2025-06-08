<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

function getEnumValues($table, $column, $conn)
{
    $query = "SHOW COLUMNS FROM $table LIKE '$column'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $type = $row['Type'];
    preg_match('/^enum\((.*)\)$/', $type, $matches);
    $enum = str_getcsv($matches[1], ',', "'");
    return $enum;
}

$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$errorMessage = "";

$queryCount = "SELECT COUNT(*) as total_count FROM lecturer";
$resultCount = mysqli_query($conn, $queryCount);
$totalItems = mysqli_fetch_assoc($resultCount)['total_count'];
$totalPages = ceil($totalItems / $itemsPerPage);

$query = "SELECT lecturer.LecturerId, lecturer.Lecturer_Name, lecturer.Department, lecturer.Email
          FROM lecturer LIMIT $itemsPerPage OFFSET $offset";
$result = mysqli_query($conn, $query);

$queryDepartments = "SELECT DISTINCT Department FROM lecturer";
$resultDepartments = mysqli_query($conn, $queryDepartments);

$enumDepartments = getEnumValues('lecturer', 'Department', $conn);

function filterLecturers($department, $lectureId, $conn, $offset, $itemsPerPage)
{
    $whereClause = "";

    if (!empty($department) && !empty($lectureId)) {
        $whereClause = "WHERE lecturer.Department = '$department' AND lecturer.LecturerId = '$lectureId'";
    } elseif (!empty($department)) {
        $whereClause = "WHERE lecturer.Department = '$department'";
    } elseif (!empty($lectureId)) {
        $whereClause = "WHERE lecturer.LecturerId = '$lectureId'";
    }

    $query = "SELECT lecturer.LecturerId, lecturer.Lecturer_Name, lecturer.Department, lecturer.Email
              FROM lecturer
              $whereClause LIMIT $itemsPerPage OFFSET $offset";
    $result = mysqli_query($conn, $query);

    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedDepartment = $_POST["department"] ?? "";
    $selectedLectureId = $_POST["lecture_id"] ?? "";

    $result = filterLecturers($selectedDepartment, $selectedLectureId, $conn, $offset, $itemsPerPage);
}

if (isset($_POST['lecturerId']) && isset($_POST['name']) && isset($_POST['department'])) {
    $lecturerId = $_POST['lecturerId'];
    $name = $_POST['name'];
    $department = $_POST['department'];

    $updateQuery = "UPDATE lecturer SET Lecturer_Name = '$name', Department = '$department' WHERE LecturerId = '$lecturerId'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        echo "success";
        exit;
    } else {
        echo "failure";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer List</title>
    <link rel="stylesheet" href="lecturer_list_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php
    if (isset($_SESSION["message"])) {
        $message = $_SESSION["message"];
        $messageType = $_SESSION["message_type"];
        unset($_SESSION["message"]);
        unset($_SESSION["message_type"]);
    }
    ?>

    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
            <a href="../MA/managing_assistant_home.php" onclick="expandSidebar(event)"><i class="fa-solid fa-house"></i><span class="menu-text">Dashboard</span></a>
            <a href="../MA/feedback.php" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star"></i><span class="menu-text">Feedback</span></a>
            <a href="../MA/notice.php" onclick="expandSidebar(event)"><i class="fa-solid fa-bell"></i><span class="menu-text">Notice</span></a>
            <a href="lecturer_list.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-user-tie fa-beat"></i><span class="menu-text">Lecturer List</span></a>
            <a href="../Student/student_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-users"></i><span class="menu-text">Students List</span></a>
            <a href="../Course/course_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book "></i><span class="menu-text">Course List</span></a>
            <a href="../MA/question_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-file-lines"></i><span class="menu-text">Questions List</span></a>
            <a href="../Course/course_allocation.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book-medical"></i><span class="menu-text">Course Allocation</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="../MA/index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>

    <div class="content expanded" id="content">
        <form id="filterForm" method="post" style="margin-bottom: 20px;">
            <select name="department" id="department" style="width: 36%;">
                <option value="">All</option>
                <?php
                mysqli_data_seek($resultDepartments, 0);
                while ($row = mysqli_fetch_assoc($resultDepartments)) :
                    $selectedDepartment = isset($_POST["department"]) ? $_POST["department"] : "";
                ?>
                    <option value="<?php echo $row['Department']; ?>" <?php if ($selectedDepartment == $row['Department']) echo "selected"; ?>>
                        <?php echo $row['Department']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit"><i class="fa-solid fa-filter fa-beat" style="margin-right: 7px;"></i>Filter</button>
            <input type="text" name="lecture_id" id="lecture_id" style="width: 36%;" placeholder="Search By Lecture ID" style="width: 45%;" value="<?php echo isset($_POST['lecture_id']) ? $_POST['lecture_id'] : ''; ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass fa-beat" style="margin-right: 7px;"></i>Search</button>
        </form>

        <!-- Lecturer list -->
        <div class="request-table">
            <table>
                <thead>
                    <tr>
                        <th>Lecturer ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['LecturerId'] . "</td>";
                            echo "<td>" . $row['Lecturer_Name'] . "</td>";
                            echo "<td>" . $row['Department'] . "</td>";
                            echo "<td>" . $row['Email'] . "</td>";
                            echo "<td><button class='edit-button' onclick='editLecturer(this)'><i class='far fa-edit ' style='margin-right:8px;'></i>Edit</button> <button class='delete-button' data-lecturerid='" . $row['LecturerId'] . "' data-email='" . $row['Email'] . "' onclick='deleteLecturer(this)'><i class='fa-solid fa-trash' style='margin-right:8px;'></i>Delete</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <button class="addLecturer" onclick="openAddLecturerPopup()"><i class="fa-solid fa-user-tie fa-beat" style="margin-right: 7px;"></i>Add Lecturer</button>
            <button class="uploadExcel" onclick="openUploadExcelPopup()"><i class="fa-solid fa-upload fa-beat" style="margin-right: 7px;"></i>Upload Excel</button>

            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php if ($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>

       
    </div>

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

    <!-- Add Lecturer Popup -->
    <div id="addLecturerPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeAddLecturerPopup()">&times;</span>
            <h2>Add Lecturer</h2>
            <form action="add_lecturer.php" method="post">
                <div class="form-group">
                    <label for="lecturerId">Lecturer ID:</label>
                    <input type="text" id="lecturerId" name="lecturerId" required>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <?php foreach ($enumDepartments as $department) : ?>
                            <option value="<?php echo $department; ?>"><?php echo $department; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Add Lecturer">
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Excel Popup -->
    <div id="uploadExcelPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeUploadExcelPopup()">&times;</span>
            <h3 style="margin-bottom: 35px;">Upload Excel File</h3>
            <form id="uploadExcelForm" action="upload_excel.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="excelFile" style="margin-bottom: 7px;">Select Lecturer List Excel File</label>
                    <input type="file" id="excelFile" name="excelFile" accept=".xls, .xlsx" required>
                </div>
                <button type="submit" class="uploadExcel"><i class="fa-solid fa-upload fa-beat" style="margin-right: 7px;"></i>Upload</button>
            </form>
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

        function submitForm() {
            document.getElementById("filterForm").submit();
        }

        function openAddLecturerPopup() {
            var popup = document.getElementById("addLecturerPopup");
            popup.style.display = "block";
        }

        function closeAddLecturerPopup() {
            document.getElementById("addLecturerPopup").style.display = "none";
        }

        function openUploadExcelPopup() {
            var popup = document.getElementById("uploadExcelPopup");
            popup.style.display = "block";
        }

        function closeUploadExcelPopup() {
            var popup = document.getElementById("uploadExcelPopup");
            popup.style.display = "none";
        }

        function editLecturer(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            for (var i = 1; i < cells.length - 1; i++) {
                cells[i].setAttribute("contenteditable", "true");
            }
            button.innerHTML = '<i class="fa-solid fa-floppy-disk fa-beat" style="margin-right:5px;"></i> Save';
            button.setAttribute("onclick", "saveLecturer(this)");
            button.classList.remove("edit-button");
            button.classList.add("save-button");
        }

        function saveLecturer(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            var lecturerId = cells[0].textContent;
            var name = cells[1].textContent;
            var department = cells[2].textContent;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText === "success") {
                        showSuccessPopup("Lecturer details updated successfully.");
                        button.innerHTML = '<i class="far fa-edit " style="margin-right:5px; "></i> Edit';
                        button.setAttribute("onclick", "editLecturer(this)");
                        button.classList.remove("save-button");
                        button.classList.add("edit-button");
                        for (var i = 1; i < cells.length - 1; i++) {
                            cells[i].removeAttribute("contenteditable");
                        }
                    } else {
                        showErrorPopup("Failed to update lecturer details.");
                    }
                }
            };
            xhttp.open("POST", "lecturer_list.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("lecturerId=" + encodeURIComponent(lecturerId) + "&name=" + encodeURIComponent(name) + "&department=" + encodeURIComponent(department));
        }

        function deleteLecturer(button) {
            var lecturerId = button.getAttribute("data-lecturerid");
            var email = button.getAttribute("data-email");
            if (confirm("Are you sure you want to delete this lecturer?")) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText === "success") {
                            showSuccessPopup("Lecturer deleted successfully.");
                            location.reload();
                        } else {
                            showErrorPopup("Failed to delete lecturer.");
                        }
                    }
                };
                xhttp.open("POST", "delete_lecturer.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("lecturerId=" + encodeURIComponent(lecturerId) + "&email=" + encodeURIComponent(email));
            }
        }

        function clearLectureId() {
            document.getElementById("lecture_id").value = "";
            document.getElementById("department").value = "";
            submitForm();
        }

        document.getElementById("lecture_id").addEventListener("input", updateLecturerList);

        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($messageType) && $messageType == 'error') : ?>
                showErrorPopup("<?php echo $message; ?>");
            <?php elseif (isset($messageType) && $messageType == 'success') : ?>
                showSuccessPopup("<?php echo $message; ?>");
            <?php endif; ?>
        });
    </script>
</body>

</html>
