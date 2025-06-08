<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";
require_once "phpspreadsheet/vendor/autoload.php"; 

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

            $insertStudentQuery = "INSERT INTO student (RegNo, Student_Name, Address, PhoneNo, BatchNo, Email, Semester) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertUserQuery = "INSERT INTO user (UserType, Email, Password, Approved) VALUES ('student', ?, ?, 1)";
            $studentStmt = mysqli_prepare($conn, $insertStudentQuery);
            $userStmt = mysqli_prepare($conn, $insertUserQuery);

            for ($row = 2; $row <= $highestRow; $row++) {
                $regNo = $sheet->getCell("A" . $row)->getValue();
                $studentName = $sheet->getCell("B" . $row)->getValue();
                $address = $sheet->getCell("C" . $row)->getValue();
                $phone = $sheet->getCell("D" . $row)->getValue();
                $batchNo = $sheet->getCell("E" . $row)->getValue();
                $email = $sheet->getCell("F" . $row)->getValue();
                $semester = $sheet->getCell("G" . $row)->getValue();
                $password = $sheet->getCell("H" . $row)->getValue(); 
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 

                // Skip rows where RegNo is NULL
                if ($regNo === null) {
                    echo "Row $row: RegNo is NULL. Skipping this row.<br>";
                    continue;
                } else {
                    echo "Row $row: RegNo = $regNo<br>";
                }

                mysqli_stmt_bind_param($studentStmt, "sssssss", $regNo, $studentName, $address, $phone, $batchNo, $email, $semester);
                mysqli_stmt_execute($studentStmt);

                mysqli_stmt_bind_param($userStmt, "ss", $email, $hashedPassword);
                mysqli_stmt_execute($userStmt);
            }

            mysqli_stmt_close($studentStmt);
            mysqli_stmt_close($userStmt);

            unlink($uploadFile);

            header("location: upload_success.php");
            exit;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Invalid file type. Please upload an Excel file.";
    }
} else {
    header("location: student_list.php");
    exit;
}
?>
