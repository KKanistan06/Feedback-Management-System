<?php
require_once "../MA/config.php";

$courseId = $_GET['course_id'];
$academicYear = $_GET['academic_year'];

$query = "SELECT l.LecturerId, l.Lecturer_Name FROM teach t JOIN lecturer l ON t.LecturerId = l.LecturerId WHERE t.CourseId = '$courseId' AND t.AY = '$academicYear'";
$result = mysqli_query($conn, $query);

$lecturers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $lecturers[] = $row;
}

header('Content-Type: application/json');
echo json_encode($lecturers);
?>
