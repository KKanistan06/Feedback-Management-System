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

            $insertLecturerQuery = "INSERT INTO lecturer (LecturerId, Lecturer_Name, Department, Email) VALUES (?, ?, ?, ?)";
            $lecturerStmt = mysqli_prepare($conn, $insertLecturerQuery);

            $insertUserQuery = "INSERT INTO user (UserType, Email, Password, Approved) VALUES ('Lecturer', ?, ?, 1)";
            $userStmt = mysqli_prepare($conn, $insertUserQuery);

            for ($row = 2; $row <= $highestRow; $row++) {
                $lecturerId = $sheet->getCell("A" . $row)->getValue();
                $lecturerName = $sheet->getCell("B" . $row)->getValue();
                $department = $sheet->getCell("C" . $row)->getValue();
                $email = $sheet->getCell("D" . $row)->getValue();
                $password = $sheet->getCell("E" . $row)->getValue();

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                mysqli_stmt_bind_param($lecturerStmt, "ssss", $lecturerId, $lecturerName, $department, $email);
                mysqli_stmt_execute($lecturerStmt);

                mysqli_stmt_bind_param($userStmt, "ss", $email, $hashedPassword);
                mysqli_stmt_execute($userStmt);
            }

            mysqli_stmt_close($lecturerStmt);
            mysqli_stmt_close($userStmt);

            unlink($uploadFile);

            $_SESSION["message"] = "Lecturers uploaded successfully.";
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
    header("location: lecturer_list.php");
    exit;
}
?>
