<?php

require_once "../MA/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regNo = trim($_POST["regNo"]);
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $batch = trim($_POST["batch"]); 

    $sql = "UPDATE student SET Student_Name = ?, Email = ?, BatchNo = ? WHERE RegNo = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_batch, $param_regNo);

        $param_name = $name;
        $param_email = $email;
        $param_batch = $batch;
        $param_regNo = $regNo;

        if (mysqli_stmt_execute($stmt)) {
            echo "success"; 
        } else {
            echo "error"; 
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} else {
    header("location: ../error.php");
    exit;
}
?>
