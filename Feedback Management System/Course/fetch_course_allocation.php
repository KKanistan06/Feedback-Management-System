<?php
require_once "../MA/config.php";

$semester = isset($_POST['semester']) ? $_POST['semester'] : '';
$batch = isset($_POST['batch']) ? $_POST['batch'] : '';

$query = "
    SELECT c.Semester, c.CourseId, c.Course_Name, COUNT(e.RegNo) AS student_count, GROUP_CONCAT(l.Lecturer_Name) AS lecturers
    FROM course_allocation ca
    JOIN course c ON ca.CourseId = c.CourseId
    LEFT JOIN enroll e ON c.CourseId = e.CourseId
    LEFT JOIN lecturer_allocation la ON c.CourseId = la.CourseId
    LEFT JOIN lecturer l ON la.LecturerId = l.LecturerId
    LEFT JOIN student s ON e.RegNo = s.RegNo
    WHERE 1 = 1
";

if (!empty($semester)) {
    $query .= " AND c.Semester = '$semester'";
}

if (!empty($batch)) {
    $query .= " AND s.BatchNo = '$batch'";
}

$query .= "
    GROUP BY c.CourseId
    ORDER BY c.Semester, c.Course_Name
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $courseId = $row['CourseId'];
    $batchNo = $batch;
    echo "<tr>
        <td>{$row['Semester']}</td>
        <td>{$row['Course_Name']}</td>
        <td><a href='javascript:void(0);' onclick='showStudentsDetails(\"$courseId\", \"$batchNo\")'>{$row['student_count']}</a></td>
        <td>{$row['lecturers']}</td>
        <td><a href='javascript:void(0);' onclick='showLecturersPopup(\"$courseId\")'><i class='fas fa-eye'></i> View</a></td>
    </tr>";
}

mysqli_close($conn);
?>
