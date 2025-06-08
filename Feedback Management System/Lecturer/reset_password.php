<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return false;
    }
    return true;
}

function validatePassword($email, $password) {
    global $conn;

    $sql = "SELECT password FROM user WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_SESSION["email"];
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];

    if (validatePassword($email, $currentPassword)) {
        if ($newPassword === $confirmPassword) {
            if (validatePasswordStrength($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $sql = "UPDATE user SET password = ? WHERE email = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $hashedPassword, $email);
                    if ($stmt->execute()) {
                        $_SESSION["message"] = "Password reset successfully.";
                        $_SESSION["message_type"] = "success";
                        exit;
                    } else {
                        $_SESSION["message"] = "Oops! Something went wrong. Please try again later.";
                        $_SESSION["message_type"] = "error";
                        exit;
                    }
                } else {
                    $_SESSION["message"] = "Oops! Something went wrong. Please try again later.";
                    $_SESSION["message_type"] = "error";
                    exit;
                }
            } else {
                $_SESSION["message"] = "New password does not meet the strength requirements.";
                $_SESSION["message_type"] = "error";
                exit;
            }
        } else {
            $_SESSION["message"] = "New password and confirm password do not match.";
            $_SESSION["message_type"] = "error";
            exit;
        }
    } else {
        $_SESSION["message"] = "Incorrect current password.";
        $_SESSION["message_type"] = "error";
        header("location: index.php");
        exit;
    }
}
?>
