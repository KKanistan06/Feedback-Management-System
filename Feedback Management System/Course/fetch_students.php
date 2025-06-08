<?php
require_once "../MA/config.php";

$courseId = $_GET['course_id'];
$batchNo = $_GET['batch_no'];
$academicYear = $_GET['academic_year'];

$query = "SELECT s.RegNo, s.Student_Name FROM enroll e JOIN student s ON e.RegNo = s.RegNo WHERE e.CourseId = '$courseId' AND s.BatchNo = '$batchNo' AND e.AY = '$academicYear'";
$result = mysqli_query($conn, $query);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

header('Content-Type: application/json');
echo json_encode($students);
?>
