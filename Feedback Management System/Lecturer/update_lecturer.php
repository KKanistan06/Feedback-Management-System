<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Lecturer") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lecturerId = $_POST["lecturerId"];
    $lecturerName = $_POST["lecturerName"];
    $department = $_POST["department"];
    $email = $_POST["email"];

    $sql = "UPDATE lecturer SET LecturerId = ?, Lecturer_Name = ?, Department = ? WHERE Email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $lecturerId, $lecturerName, $department, $email);

        if ($stmt->execute()) {
            header("location: lecturer_home.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            error_log("Error: " . $sql . "\n" . $conn->error);
        }

        $stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        error_log("Error: " . $sql . "\n" . $conn->error);
    }

    $conn->close();
}
