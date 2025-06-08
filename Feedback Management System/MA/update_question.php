<?php

require_once "config.php";

if(isset($_POST["tableName"], $_POST["queId"], $_POST["queType"], $_POST["queText"])) {
    $tableName = $_POST["tableName"];
    $queId = $_POST["queId"];
    $queType = $_POST["queType"];
    $queText = $_POST["queText"];

    $query = "UPDATE $tableName SET QueType=?, QueText=? WHERE QueId=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $queType, $queText, $queId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
} else {
    echo "invalid request";
}

$conn->close();
?>
