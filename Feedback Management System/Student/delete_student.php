<?php

require_once "../MA/config.php";

if (isset($_GET["regNo"]) && !empty(trim($_GET["regNo"]))) {
    $regNo = trim($_GET["regNo"]);

    $sqlGetEmail = "SELECT Email FROM student WHERE RegNo = ?";
    if ($stmtGetEmail = mysqli_prepare($conn, $sqlGetEmail)) {
        mysqli_stmt_bind_param($stmtGetEmail, "s", $regNo);

        if (mysqli_stmt_execute($stmtGetEmail)) {
            mysqli_stmt_store_result($stmtGetEmail);
            if (mysqli_stmt_num_rows($stmtGetEmail) == 1) {
                mysqli_stmt_bind_result($stmtGetEmail, $email);
                mysqli_stmt_fetch($stmtGetEmail);

                $sqlDeleteStudent = "DELETE FROM student WHERE RegNo = ?";
                $sqlDeleteUser = "DELETE FROM user WHERE email = ?";

                if ($stmtDeleteStudent = mysqli_prepare($conn, $sqlDeleteStudent)) {
                    mysqli_stmt_bind_param($stmtDeleteStudent, "s", $regNo);

                    if (mysqli_stmt_execute($stmtDeleteStudent)) {
                        if ($stmtDeleteUser = mysqli_prepare($conn, $sqlDeleteUser)) {
                            mysqli_stmt_bind_param($stmtDeleteUser, "s", $email);

                            if (mysqli_stmt_execute($stmtDeleteUser)) {
                                echo "success";
                            } else {
                                echo "Failed to delete user: " . mysqli_error($conn);
                            }
                            mysqli_stmt_close($stmtDeleteUser);
                        } else {
                            echo "Failed to prepare user delete statement: " . mysqli_error($conn);
                        }
                    } else {
                        echo "Failed to delete student: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmtDeleteStudent);
                } else {
                    echo "Failed to prepare student delete statement: " . mysqli_error($conn);
                }
            } else {
                echo "No student found with the provided RegNo.";
            }
        } else {
            echo "Failed to retrieve email: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmtGetEmail);
    } else {
        echo "Failed to prepare email retrieval statement: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    header("location: error.php");
    exit();
}
?>
