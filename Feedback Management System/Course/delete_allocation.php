<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "../MA/config.php";

    $input = json_decode(file_get_contents('php://input'), true);
    $course_id = $input['course_id'];
    $academic_year = $input['academic_year'];

    $queryEnroll = "DELETE FROM enroll WHERE CourseId = ? AND AY = ?";
    $stmtEnroll = $conn->prepare($queryEnroll);
    $stmtEnroll->bind_param("ss", $course_id, $academic_year);
    $stmtEnroll->execute();

    $queryTeach = "DELETE FROM teach WHERE CourseId = ? AND AY = ?";
    $stmtTeach = $conn->prepare($queryTeach);
    $stmtTeach->bind_param("ss", $course_id, $academic_year);
    $stmtTeach->execute();

    if ($stmtEnroll->affected_rows > 0 && $stmtTeach->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmtEnroll->close();
    $stmtTeach->close();
    $conn->close();
}
?>
