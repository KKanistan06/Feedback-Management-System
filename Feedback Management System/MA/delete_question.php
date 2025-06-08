<?php

require_once "config.php";

if(isset($_GET["tableName"], $_GET["queId"])) {
    $tableName = $_GET["tableName"];
    $queId = $_GET["queId"];

    $query = "DELETE FROM $tableName WHERE QueId=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $queId);

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
