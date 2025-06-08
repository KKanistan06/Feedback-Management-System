<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

$errorMessage = "";
$result = null;

$queryEnumBatch = "SHOW COLUMNS FROM student LIKE 'BatchNo'";
$resultEnumBatch = mysqli_query($conn, $queryEnumBatch);
$rowEnumBatch = mysqli_fetch_assoc($resultEnumBatch);
$enumBatch = substr($rowEnumBatch['Type'], 5, -1);

$queryEnumSemester = "SHOW COLUMNS FROM student LIKE 'Semester'";
$resultEnumSemester = mysqli_query($conn, $queryEnumSemester);
$rowEnumSemester = mysqli_fetch_assoc($resultEnumSemester);
$enumSemester = substr($rowEnumSemester['Type'], 5, -1);

$batchOptions = explode("','", substr($enumBatch, 1, -1));
$semesterOptions = explode("','", substr($enumSemester, 1, -1));

$queryBatches = "SELECT DISTINCT BatchNo FROM student";
$resultBatches = mysqli_query($conn, $queryBatches);

function filterStudents($batch, $regNo, $conn, $offset, $studentsPerPage)
{
    $whereClause = "";

    if (!empty($batch) && $batch != "All Batches") {
        $whereClause = "WHERE BatchNo = '$batch'";
    }

    if (!empty($regNo)) {
        $whereClause .= ($whereClause != "") ? " AND RegNo = '$regNo'" : "WHERE RegNo = '$regNo'";
    }

    $query = "SELECT s.RegNo, 
    CONCAT(SUBSTRING_INDEX(s.Student_Name, ' ', 1), ' ', SUBSTRING_INDEX(s.Student_Name, ' ', -1)) AS Student_Name,
            s.Email, 
            s.BatchNo, 
            u.approved 
    FROM student s 
    LEFT JOIN user u ON s.Email = u.email 
    $whereClause
    ORDER BY s.BatchNo
    LIMIT $offset, $studentsPerPage";

    $result = mysqli_query($conn, $query);
    return $result;
}

$studentsPerPage = 10; 
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $studentsPerPage;

$totalStudentsQuery = "SELECT COUNT(*) as count FROM student";
$totalStudentsResult = mysqli_query($conn, $totalStudentsQuery);
$totalStudents = mysqli_fetch_assoc($totalStudentsResult)['count'];
$totalPages = ceil($totalStudents / $studentsPerPage);

$result = filterStudents("", "", $conn, $offset, $studentsPerPage);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedBatch = $_POST["batch"] ?? "";
    $searchRegNo = $_POST["regNo"] ?? "";
    $result = filterStudents($selectedBatch, $searchRegNo, $conn, $offset, $studentsPerPage);
} else {
    $selectedBatch = "";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="student_list_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="student_list.js"></script>
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
            <a href="../Lecturer/lecturer_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-user-tie "></i><span class="menu-text">Lecturer List</span></a>
            <a href="student_list.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-users fa-beat"></i><span class="menu-text">Students List</span></a>
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
            <select name="batch" id="batch" style="width: 36%;">
                <option value="">All Batches</option>
                <?php
                foreach ($batchOptions as $batchOption) {
                    $selected = ($batchOption === $selectedBatch) ? 'selected' : '';
                    echo "<option value='$batchOption' $selected>$batchOption</option>";
                }
                ?>
            </select>
            <button type="submit"><i class="fa-solid fa-filter fa-beat" style="margin-right: 7px;"></i>Filter</button>

            <input type="text" name="regNo" id="regNo" style="width: 36%;" placeholder="Search By Student ID" value="<?php echo isset($_POST['regNo']) ? $_POST['regNo'] : ''; ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass fa-beat" style="margin-right: 7px;"></i>Search</button>
        </form>

        <!-- Student list -->
        <div class="request-table">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Batch</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status = ($row['approved'] == 1) ? 'Active' : 'Pending';
                            $names = explode(" ", $row['Student_Name']);
                            $initial = strtoupper(substr($names[0], 0, 1));
                            $lastName = implode(' ', array_slice($names, 1));
                            echo "<tr>";
                            echo "<td>" . $row['RegNo'] . "</td>";
                            echo "<td>" . $initial . ". " . $lastName . "</td>";
                            echo "<td>" . $row['Email'] . "</td>";
                            echo "<td>" . $row['BatchNo'] . "</td>";
                            if ($status == 'Active') {
                                echo "<td class='active-status'>" . $status . "</td>";
                            } else {
                                echo "<td class='pending-status'>" . $status . "</td>";
                            }
                            echo "<td><button class='edit-button' onclick='editStudent(this)'><i class='far fa-edit' style='margin-right:8px;'></i>Edit</button> <button class='delete-button' data-regno='" . $row['RegNo'] . "' data-email='" . $row['Email'] . "' onclick='deleteStudent(this)'><i class='fa-solid fa-trash' style='margin-right:8px;'></i>Delete</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($currentPage > 1) : ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>" class="prev"><i class="fa-solid fa-chevron-left fa-beat"></i></a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= min($totalPages, 2); $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php if ($i == $currentPage) echo 'active'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($totalPages > 2) : ?>
                    <span>...</span>
                    <a href="?page=<?php echo $totalPages; ?>" class="<?php if ($totalPages == $currentPage) echo 'active'; ?>"><?php echo $totalPages; ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages) : ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>" class="next"><i class="fa-solid fa-chevron-right fa-beat"></i></a>
                <?php endif; ?>
            </div>

            <button class="addStudent" onclick="openAddStudentPopup()"><i class="fa-solid fa-user-pen fa-beat" style="margin-right: 7px;"></i>Add Student</button>
            <button class="uploadExcel" onclick="document.getElementById('uploadExcelPopup').style.display='block'"><i class="fa-solid fa-upload fa-beat" style="margin-right: 7px;"></i>Upload Excel</button>
        </div>
    </div>

    <!-- Add Student Popup -->
    <div id="addStudentPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeAddStudentPopup()">&times;</span>
            <h2>Add New Student</h2>
            <form action="add_student.php" method="post">
                <div class="form-group">
                    <label for="regNo">Student Id</label>
                    <input type="text" id="regNo" name="regNo" required>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="batch">Batch</label>
                    <select name="batch" id="batch" required>
                        <option value="">Select Batch</option>
                        <?php
                        foreach ($batchOptions as $batchOption) {
                            echo "<option value='$batchOption'>$batchOption</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester">Semester</label>
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
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="addStudent"><i class="fa-solid fa-user-pen fa-beat" style="margin-right: 7px;"></i>Add Student</button>
            </form>
        </div>
    </div>

    <!-- Upload Excel Popup -->
    <div id="uploadExcelPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeUploadExcelPopup()">&times;</span>
            <h3 style="margin-bottom: 35px;">Upload Excel File</h3>
            <form action="upload_excel.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="excelFile" style="margin-bottom: 7px;">Select Student list Excel File</label>
                    <input type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls" required>
                </div>
                <button type="submit" class="uploadExcel"><i class="fa-solid fa-upload fa-beat" style="margin-right: 7px;"></i>Upload</button>
            </form>
        </div>
    </div>

    <!-- Reset Password Popup -->
    <div id="resetPasswordPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeResetPasswordPopup()">&times;</span>
            <h2>Reset Password</h2>
            <form action="reset_password.php" method="post">
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input type="password" id="currentPassword" name="currentPassword" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitButton">
                        <i class="fa-solid fa-floppy-disk fa-beat" style="margin-right: 7px;"></i> Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</body>
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
        var popup = document.getElementById("resetPasswordPopup");
        popup.style.display = "block";
    }

    function closeResetPasswordPopup() {
        document.getElementById("resetPasswordPopup").style.display = "none";
    }

    function editStudent(button) {
        var row = button.parentNode.parentNode;
        var cells = row.getElementsByTagName("td");
        for (var i = 1; i < cells.length - 2; i++) {
            cells[i].setAttribute("contenteditable", "true");
        }
        button.innerHTML = '<i class="fa-solid fa-floppy-disk fa-beat" style="margin-right:5px;"></i> Save';
        button.setAttribute("onclick", "saveStudent(this)");
        button.classList.remove("edit-button");
        button.classList.add("save-button");
    }

    function saveStudent(button) {
        var row = button.parentNode.parentNode;
        var cells = row.getElementsByTagName("td");
        var regNo = cells[0].textContent;
        var name = cells[1].textContent;
        var email = cells[2].textContent;
        var batchNo = cells[3].textContent;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText === "success") {
                    alert("Student details updated successfully.");
                    button.innerHTML = '<i class="far fa-edit" style="margin-right:5px; "></i> Edit';
                    button.setAttribute("onclick", "editStudent(this)");
                    button.classList.remove("save-button");
                    button.classList.add("edit-button");
                    for (var i = 1; i < cells.length - 2; i++) {
                        cells[i].removeAttribute("contenteditable");
                    }
                } else {
                    alert("Failed to update student details.");
                }
            }
        };
        xhttp.open("POST", "edit_student.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("regNo=" + encodeURIComponent(regNo) + "&name=" + encodeURIComponent(name) + "&email=" + encodeURIComponent(email) + "&batch=" + encodeURIComponent(batchNo));
    }

    function deleteStudent(button) {
        var regNo = button.getAttribute("data-regno");
        var email = button.getAttribute("data-email");
        if (confirm("Are you sure you want to delete this student?")) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText === "success") {
                        alert("Student deleted successfully.");
                        // Remove row and update the table
                        var row = button.parentNode.parentNode;
                        row.parentNode.removeChild(row);
                        // Reload table data
                        loadTableData();
                    } else {
                        alert("Failed to delete student.");
                    }
                }
            };
            xhttp.open("GET", "delete_student.php?regNo=" + encodeURIComponent(regNo) + "&email=" + encodeURIComponent(email), true);
            xhttp.send();
        }
    }

    function loadTableData() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("studentTableBody").innerHTML = this.responseText;
                updatePagination();
            }
        };
        xhttp.open("GET", "fetch_students.php?page=<?php echo $currentPage; ?>", true);
        xhttp.send();
    }

    function updatePagination() {
        // Implement pagination update logic if necessary
        // You may need to adjust pagination links based on the number of pages
    }

    function openAddStudentPopup() {
        var popup = document.getElementById("addStudentPopup");
        popup.style.display = "block";
    }

    function closeAddStudentPopup() {
        var popup = document.getElementById("addStudentPopup");
        popup.style.display = "none";
    }

    function openUploadExcelPopup() {
        document.getElementById('uploadExcelPopup').style.display = 'block';
    }

    function closeUploadExcelPopup() {
        document.getElementById('uploadExcelPopup').style.display = 'none';
    }

    function toggleNav() {
        var x = document.getElementsByClassName('navbar')[0];
        if (x.className === 'navbar') {
            x.className += ' responsive';
        } else {
            x.className = 'navbar';
        }
    }
</script>

</html>

