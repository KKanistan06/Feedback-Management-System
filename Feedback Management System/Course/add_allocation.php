<?php
require_once "../MA/config.php";

$academic_year = "";
$semester = "";
$course_id = "";
$selected_courses = [];
$selected_batches = [];
$selected_students = [];
$selected_lecturers = [];

if (isset($_GET['course_id']) && isset($_GET['academic_year']) && isset($_GET['semester'])) {
    $course_id = $_GET['course_id'];
    $academic_year = $_GET['academic_year'];
    $semester = $_GET['semester'];

    $selected_courses[] = $course_id;

    $queryBatches = "
        SELECT DISTINCT s.BatchNo 
        FROM student s
        JOIN enroll e ON s.RegNo = e.RegNo
        WHERE e.CourseId = '$course_id' AND e.AY = '$academic_year'
    ";
    $resultBatches = mysqli_query($conn, $queryBatches);
    while ($row = mysqli_fetch_assoc($resultBatches)) {
        $selected_batches[] = $row['BatchNo'];
    }

    $queryStudents = "
        SELECT DISTINCT s.RegNo, s.Student_Name 
        FROM student s
        JOIN enroll e ON s.RegNo = e.RegNo
        WHERE e.CourseId = '$course_id' AND e.AY = '$academic_year'
    ";
    $resultStudents = mysqli_query($conn, $queryStudents);
    while ($row = mysqli_fetch_assoc($resultStudents)) {
        $selected_students[] = $row;
    }

    $queryLecturers = "
        SELECT DISTINCT l.LecturerId, l.Lecturer_Name 
        FROM lecturer l
        JOIN teach t ON l.LecturerId = t.LecturerId
        WHERE t.CourseId = '$course_id' AND t.AY = '$academic_year'
    ";
    $resultLecturers = mysqli_query($conn, $queryLecturers);
    while ($row = mysqli_fetch_assoc($resultLecturers)) {
        $selected_lecturers[] = $row;
    }
}

$querySemesters = "SHOW COLUMNS FROM course LIKE 'Semester'";
$resultSemesters = mysqli_query($conn, $querySemesters);
$rowSemesters = mysqli_fetch_assoc($resultSemesters);
preg_match("/^enum\(\'(.*)\'\)$/", $rowSemesters['Type'], $matchesSemesters);
$semesters = explode("','", $matchesSemesters[1]);

$queryCourses = "SELECT CourseId, Course_Name FROM course WHERE Semester = '$semester'";
$resultCourses = mysqli_query($conn, $queryCourses);

$queryBatches = "SHOW COLUMNS FROM student LIKE 'BatchNo'";
$resultBatches = mysqli_query($conn, $queryBatches);
$rowBatches = mysqli_fetch_assoc($resultBatches);
preg_match("/^enum\(\'(.*)\'\)$/", $rowBatches['Type'], $matchesBatches);
$batches = explode("','", $matchesBatches[1]);

$queryAllLecturers = "SELECT LecturerId, Lecturer_Name FROM lecturer";
$resultAllLecturers = mysqli_query($conn, $queryAllLecturers);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $course_id ? 'Edit' : 'Add'; ?> Course Allocation</title>
    <link rel="stylesheet" href="add_allocation_style.css">

</head>

<body>
    <div class="content" style="margin-top: 30px;">
        <span class="close" onclick="window.location.href='course_allocation.php'">&times;</span>
        <h2><?php echo $course_id ? 'Edit' : 'Add'; ?> Course Allocation</h2>
        <form id="addAllocationForm" action="process_add_allocation.php" method="post">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

            <div class="form-row">
                <div>
                    <label for="academic_year">Academic Year</label>
                    <input type="text" id="academic_year" name="academic_year" value="<?php echo $academic_year; ?>" required>
                </div>
                <div>
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required onchange="updateCourses()">
                        <?php foreach ($semesters as $sem) {
                            $selected = ($semester == $sem) ? "selected" : "";
                            echo "<option value='$sem' $selected>$sem</option>";
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="courses">Courses</label>
                    <div id="courses">
                        <?php while ($row = mysqli_fetch_assoc($resultCourses)) {
                            $checked = in_array($row['CourseId'], $selected_courses) ? "checked" : "";
                            echo "<label class='checkbox-label'>";
                            echo "<input type='radio' name='courses[]' value='{$row['CourseId']}' $checked>{$row['Course_Name']}";
                            echo "</label>";
                        } ?>
                    </div>
                </div>
                <div>
                    <label for="batches">Batch No</label>
                    <div id="batches" onchange="updateRegNos()">
                        <?php foreach ($batches as $batch) {
                            $checked = in_array($batch, $selected_batches) ? "checked" : "";
                            echo "<label class='checkbox-label'>";
                            echo "<input type='checkbox' name='batches[]' value='$batch' $checked>$batch";
                            echo "</label>";
                        } ?>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="students">Students</label>
                    <div id="students">
                        
                    </div>
                </div>
                <div>
                    <label for="lecturers">Lecturers</label>
                    <div id="lecturers">
                        <?php while ($row = mysqli_fetch_assoc($resultAllLecturers)) {
                            $checked = in_array($row['LecturerId'], array_column($selected_lecturers, 'LecturerId')) ? "checked" : "";
                            echo "<label class='checkbox-label'>";
                            echo "<input type='checkbox' name='lecturers[]' value='{$row['LecturerId']}' $checked>{$row['Lecturer_Name']}";
                            echo "</label>";
                        } ?>
                    </div>
                </div>
            </div>
            <input type="submit" value="<?php echo $course_id ? 'Update' : 'Add'; ?> Allocation">
        </form>
    </div>

    <script>
        function updateCourses() {
            const semester = document.getElementById('semester').value;
            const courseId = "<?php echo $course_id; ?>";
            fetch(`fetch_courses.php?semester=${semester}`)
                .then(response => response.json())
                .then(data => {
                    const coursesDiv = document.getElementById('courses');
                    coursesDiv.innerHTML = '';
                    data.forEach(course => {
                        const checked = (courseId == course.CourseId) ? "checked" : "";
                        const label = document.createElement('label');
                        label.classList.add('checkbox-label');
                        const radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = 'courses[]';
                        radio.value = course.CourseId;
                        radio.checked = checked;
                        label.appendChild(radio);
                        label.appendChild(document.createTextNode(course.Course_Name));
                        coursesDiv.appendChild(label);
                    });
                });
        }

        function updateRegNos() {
            const selectedBatches = Array.from(document.querySelectorAll('#batches input[type="checkbox"]:checked')).map(cb => cb.value);
            fetch(`fetch_students_by_batches.php?batches=${selectedBatches.join(',')}`)
                .then(response => response.json())
                .then(data => {
                    const studentsDiv = document.getElementById('students');
                    studentsDiv.innerHTML = '';

                    const selectAllLabel = document.createElement('label');
                    const selectAllCheckbox = document.createElement('input');
                    selectAllCheckbox.type = 'checkbox';
                    selectAllCheckbox.id = 'selectAll';
                    selectAllCheckbox.onclick = () => {
                        const checkboxes = document.querySelectorAll('#students input[type="checkbox"]');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = selectAllCheckbox.checked;
                        });
                    };
                    selectAllLabel.classList.add('checkbox-label');
                    selectAllLabel.appendChild(selectAllCheckbox);
                    selectAllLabel.appendChild(document.createTextNode('Select All'));
                    studentsDiv.appendChild(selectAllLabel);

                    data.forEach(student => {
                        const label = document.createElement('label');
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'students[]';
                        checkbox.value = student.RegNo;
                        label.classList.add('checkbox-label');
                        label.appendChild(checkbox);
                        label.appendChild(document.createTextNode(student.Student_Name));
                        studentsDiv.appendChild(label);
                    });

                    <?php foreach ($selected_students as $student) : ?>
                        document.querySelector(`#students input[value="<?php echo $student['RegNo']; ?>"]`).checked = true;
                    <?php endforeach; ?>
                });
        }

        document.addEventListener("DOMContentLoaded", function() {
            if (document.getElementById('semester').value) {
                updateCourses();
            }
            updateRegNos(); 
        });
    </script>
</body>

</html>