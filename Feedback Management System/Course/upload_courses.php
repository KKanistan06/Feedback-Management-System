<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";
require_once "../Student/phpspreadsheet/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDirectory = "../uploads/";

    if (!is_dir($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0777, true)) {
            die("Failed to create upload directory.");
        }
    }

    $uploadFile = $uploadDirectory . basename($_FILES["excelFile"]["name"]);
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $allowedTypes = array("xls", "xlsx");

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["excelFile"]["tmp_name"], $uploadFile)) {
            $spreadsheet = IOFactory::load($uploadFile);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $insertCourseQuery = "INSERT INTO course (CourseId, Course_Name, Department, Semester, Credit) VALUES (?, ?, ?, ?, ?)";
            $courseStmt = mysqli_prepare($conn, $insertCourseQuery);

            for ($row = 2; $row <= $highestRow; $row++) {
                $courseId = $sheet->getCell("A" . $row)->getValue();
                $courseName = $sheet->getCell("B" . $row)->getValue();
                $department = $sheet->getCell("C" . $row)->getValue();
                $semester = $sheet->getCell("D" . $row)->getValue();
                $credit = $sheet->getCell("E" . $row)->getValue();

                mysqli_stmt_bind_param($courseStmt, "ssssd", $courseId, $courseName, $department, $semester, $credit);
                mysqli_stmt_execute($courseStmt);
            }

            mysqli_stmt_close($courseStmt);

            unlink($uploadFile);

            $_SESSION["message"] = "Courses uploaded successfully.";
            $_SESSION["message_type"] = "success";

            header("location: upload_success.php");
            exit;
        } else {
            $_SESSION["message"] = "Sorry, there was an error uploading your file.";
            $_SESSION["message_type"] = "error";
        }
    } else {
        $_SESSION["message"] = "Invalid file type. Please upload an Excel file.";
        $_SESSION["message_type"] = "error";
    }
} else {
    header("location: course_list.php");
    exit;
}
?>
