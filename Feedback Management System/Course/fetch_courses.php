<?php
require_once "../MA/config.php";

$semester = $_GET['semester'];

$queryCourses = "SELECT CourseId, Course_Name FROM course WHERE Semester = '$semester'";
$resultCourses = mysqli_query($conn, $queryCourses);

$courses = [];
while ($row = mysqli_fetch_assoc($resultCourses)) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
