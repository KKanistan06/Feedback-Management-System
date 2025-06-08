<?php

require_once "config.php";

if(isset($_GET["category"])) {
    $category = $_GET["category"];

    $query = "DELETE FROM category_table WHERE Category=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category);

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
