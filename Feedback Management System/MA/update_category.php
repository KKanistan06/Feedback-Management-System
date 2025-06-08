<?php

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldQueType = $_POST["oldQueType"];
    $updatedTypes = $_POST["updatedTypes"];
    $tableName = $_POST["tableName"];

    $query = "UPDATE $tableName SET QueType=? WHERE QueType=?";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "ss", $updatedQueType, $oldQueType);

        $updatedQueType = $updatedTypes;

        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "failed";
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}
?>
