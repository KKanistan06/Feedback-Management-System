<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

$selectedSemester = isset($_POST['semester']) ? $_POST['semester'] : '';
$selectedAY = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';

$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$querySemesters = "SHOW COLUMNS FROM course LIKE 'Semester'";
$resultSemesters = mysqli_query($conn, $querySemesters);
$rowSemesters = mysqli_fetch_assoc($resultSemesters);
preg_match("/^enum\(\'(.*)\'\)$/", $rowSemesters['Type'], $matchesSemesters);
$semesters = explode("','", $matchesSemesters[1]);

$queryAY = "SELECT DISTINCT AY FROM enroll ORDER BY AY DESC";
$resultAY = mysqli_query($conn, $queryAY);
$academicYears = [];
while ($rowAY = mysqli_fetch_assoc($resultAY)) {
    $academicYears[] = $rowAY['AY'];
}

$queryCount = "
    SELECT COUNT(DISTINCT c.Semester, c.CourseId, e.AY) as total_count
    FROM course c
    JOIN enroll e ON c.CourseId = e.CourseId
    JOIN student s ON e.RegNo = s.RegNo
    JOIN teach t ON c.CourseId = t.CourseId AND e.AY = t.AY
    JOIN lecturer l ON t.LecturerId = l.LecturerId
";

if ($selectedSemester || $selectedAY) {
    $queryCount .= " WHERE";
    if ($selectedSemester) {
        $queryCount .= " c.Semester = '$selectedSemester'";
        if ($selectedAY) {
            $queryCount .= " AND";
        }
    }
    if ($selectedAY) {
        $queryCount .= " e.AY = '$selectedAY'";
    }
}

$resultCount = mysqli_query($conn, $queryCount);
$totalItems = mysqli_fetch_assoc($resultCount)['total_count'];
$totalPages = ceil($totalItems / $itemsPerPage);

$queryAllocations = "
    SELECT 
        c.Semester, 
        c.CourseId, 
        c.Course_Name,
        e.AY AS academic_year,
        COUNT(DISTINCT e.RegNo) AS enrolled_student_count,
        GROUP_CONCAT(DISTINCT s.BatchNo ORDER BY s.BatchNo) AS batch_numbers,
        COUNT(DISTINCT t.LecturerId) AS lecturer_count,
        GROUP_CONCAT(DISTINCT l.LecturerId ORDER BY l.LecturerId) AS lecturer_ids,
        GROUP_CONCAT(DISTINCT l.Lecturer_Name ORDER BY l.LecturerId) AS lecturer_names
    FROM 
        course c
    JOIN 
        enroll e ON c.CourseId = e.CourseId
    JOIN 
        student s ON e.RegNo = s.RegNo
    JOIN 
        teach t ON c.CourseId = t.CourseId AND e.AY = t.AY
    JOIN 
        lecturer l ON t.LecturerId = l.LecturerId
";

if ($selectedSemester || $selectedAY) {
    $queryAllocations .= " WHERE";
    if ($selectedSemester) {
        $queryAllocations .= " c.Semester = '$selectedSemester'";
        if ($selectedAY) {
            $queryAllocations .= " AND";
        }
    }
    if ($selectedAY) {
        $queryAllocations .= " e.AY = '$selectedAY'";
    }
}

$queryAllocations .= " GROUP BY c.Semester, c.CourseId, c.Course_Name, e.AY";
$queryAllocations .= " ORDER BY e.AY DESC";
$queryAllocations .= " LIMIT $itemsPerPage OFFSET $offset";

$resultAllocations = mysqli_query($conn, $queryAllocations);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Allocation</title>
    <link rel="stylesheet" href="course_allocation_style.css">
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
            <a href="course_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book "></i><span class="menu-text">Course List</span></a>
            <a href="../MA/question_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-file-lines"></i><span class="menu-text">Questions List</span></a>
            <a href="course_allocation.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-book-medical fa-beat"></i><span class="menu-text">Course Allocation</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="../MA/index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>

    <div class="content expanded" id="content">
        <h2>Course Allocation</h2>
        <div class="filters">
            <form id="filterForm" method="post">
                <select name="semester" id="semester" style="width: 36%;">
                    <option value="">All Semesters</option>
                    <?php foreach ($semesters as $semester) : ?>
                        <option value="<?php echo $semester; ?>" <?php if ($selectedSemester == $semester) echo "selected"; ?>>
                            <?php echo $semester; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="academic_year" id="academic_year" style="width: 36%;">
                    <option value="">All Academic Years</option>
                    <?php foreach ($academicYears as $ay) : ?>
                        <option value="<?php echo $ay; ?>" <?php if ($selectedAY == $ay) echo "selected"; ?>>
                            <?php echo $ay; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"><i class="fa-solid fa-filter fa-beat" style="margin-right: 7px;"></i>Filter</button>
            </form>
        </div>

        <table id="allocationTable">
            <thead>
                <tr>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>Course</th>
                    <th>Students</th>
                    <th>Lecturer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultAllocations)) {
                    $batches = explode(',', $row['batch_numbers']);
                    $studentButtons = [];
                    foreach ($batches as $batch) {
                        $queryTotalStudents = "SELECT COUNT(RegNo) AS total_count FROM student WHERE BatchNo = '$batch'";
                        $resultTotalStudents = mysqli_query($conn, $queryTotalStudents);
                        $totalStudents = mysqli_fetch_assoc($resultTotalStudents)['total_count'];

                        $queryRegisteredStudents = "SELECT COUNT(DISTINCT s.RegNo) AS count FROM enroll e JOIN student s ON e.RegNo = s.RegNo WHERE e.CourseId = '{$row['CourseId']}' AND s.BatchNo = '$batch' AND e.AY = '{$row['academic_year']}'";
                        $resultRegisteredStudents = mysqli_query($conn, $queryRegisteredStudents);
                        $registeredStudents = mysqli_fetch_assoc($resultRegisteredStudents)['count'];

                        $studentButtons[] = "<button class='btn student-button' onclick=\"showStudents('{$row['CourseId']}', '{$batch}', '{$row['academic_year']}')\"><i class='fa fa-user-graduate' style='margin-right:7px;'></i>$batch-$registeredStudents</button>";
                    }
                    $studentButtonText = implode('<br>', $studentButtons);

                    $lecturerCount = $row['lecturer_count'];
                    $lecturerText = $lecturerCount . '-lecturer' . ($lecturerCount > 1 ? 's' : '');
                    $lecturerDetails = "<button class='btn lecturer-button' onclick=\"showLecturers('{$row['CourseId']}', '{$row['academic_year']}')\"><i class='fa fa-chalkboard-teacher' style='margin-right:7px;'></i>$lecturerText</button>";
                ?>
                    <tr id="allocation-<?php echo $row['CourseId'] . '-' . $row['academic_year']; ?>">
                        <td><?php echo $row['academic_year']; ?></td>
                        <td><?php echo $row['Semester']; ?></td>
                        <td><?php echo $row['CourseId']; ?></td>
                        <td><?php echo $studentButtonText; ?></td>
                        <td><?php echo $lecturerDetails; ?></td>
                        <td>
                            <button class="btn edit-button" onclick="editAllocation('<?php echo $row['CourseId']; ?>', '<?php echo $row['academic_year']; ?>', '<?php echo $row['Semester']; ?>')"><i class='fa fa-edit' style='margin-right:7px;'></i>Edit</button>
                            <button class="btn delete-button" onclick="deleteAllocation('<?php echo $row['CourseId']; ?>', '<?php echo $row['academic_year']; ?>')"><i class='fa fa-trash' style='margin-right:7px;'></i>Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?php echo $i; ?>" class="<?php if ($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>

        <button class="btn add-allocation-button" onclick="window.location.href='add_allocation.php'"><i class="fa fa-plus fa-beat" style="margin-right:7px;"></i>Allocate Course</button>


        <!-- Students Popup -->
        <div id="studentsPopup" class="popup">
            <div class="popup-content">
                <span class="close" onclick="closePopup('studentsPopup')">&times;</span>
                <h2>Students List</h2>
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody id="studentsList">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lecturers Popup -->
        <div id="lecturersPopup" class="popup">
            <div class="popup-content">
                <span class="close" onclick="closePopup('lecturersPopup')">&times;</span>
                <h2>Lecturers List</h2>
                <table id="lecturersTable">
                    <thead>
                        <tr>
                            <th>Lecturer ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody id="lecturersList">
                    </tbody>
                </table>
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



        function closePopup(popupId) {
            document.getElementById(popupId).style.display = "none";
        }

        function showStudents(courseId, batchNo, academicYear) {
            fetch(`fetch_students.php?course_id=${courseId}&batch_no=${batchNo}&academic_year=${academicYear}`)
                .then(response => response.json())
                .then(data => {
                    const studentsList = document.getElementById('studentsList');
                    studentsList.innerHTML = '';

                    data.forEach(student => {
                        const row = document.createElement('tr');
                        const regNoCell = document.createElement('td');
                        const nameCell = document.createElement('td');

                        regNoCell.textContent = student.RegNo;
                        nameCell.textContent = student.Student_Name;

                        row.appendChild(regNoCell);
                        row.appendChild(nameCell);

                        studentsList.appendChild(row);
                    });

                    document.querySelector('#studentsPopup h2').textContent = `Students List for Batch ${batchNo}`;
                    document.getElementById('studentsPopup').style.display = 'block';
                });
        }

        function showLecturers(courseId, academicYear) {
            fetch(`fetch_lecturers.php?course_id=${courseId}&academic_year=${academicYear}`)
                .then(response => response.json())
                .then(data => {
                    const lecturersList = document.getElementById('lecturersList');
                    lecturersList.innerHTML = '';

                    data.forEach(lecturer => {
                        const row = document.createElement('tr');
                        const idCell = document.createElement('td');
                        const nameCell = document.createElement('td');

                        idCell.textContent = lecturer.LecturerId;
                        nameCell.textContent = lecturer.Lecturer_Name;

                        row.appendChild(idCell);
                        row.appendChild(nameCell);

                        lecturersList.appendChild(row);
                    });

                    document.querySelector('#lecturersPopup h2').textContent = 'Lecturers List';
                    document.getElementById('lecturersPopup').style.display = 'block';
                });
        }

        function editAllocation(courseId, academicYear, semester) {
            window.location.href = `add_allocation.php?course_id=${courseId}&academic_year=${academicYear}&semester=${semester}`;
        }

        function deleteAllocation(courseId, academicYear) {
            if (confirm("Are you sure you want to delete this allocation?")) {
                fetch('delete_allocation.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            course_id: courseId,
                            academic_year: academicYear
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`allocation-${courseId}-${academicYear}`).remove();
                            alert("Allocation deleted successfully.");
                        } else {
                            alert("Failed to delete allocation.");
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($messageType) && $messageType == 'error') : ?>
                showErrorPopup("<?php echo $message; ?>");
            <?php elseif (isset($messageType) && $messageType == 'success') : ?>
                showSuccessPopup("<?php echo $message; ?>");
            <?php endif; ?>
        });

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