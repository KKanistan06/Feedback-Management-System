<?php
require_once "../MA/config.php";

$batches = explode(',', $_GET['batches']);

$queryStudents = "SELECT RegNo, Student_Name FROM student WHERE BatchNo IN ('" . implode("','", $batches) . "')";
$resultStudents = mysqli_query($conn, $queryStudents);

$students = [];
while ($row = mysqli_fetch_assoc($resultStudents)) {
    $students[] = $row;
}

echo json_encode($students);
?>
