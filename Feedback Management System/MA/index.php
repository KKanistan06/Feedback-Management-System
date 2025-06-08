<?php

require_once "config.php";

$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {

        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } else {
            $email = trim($_POST["email"]);
        }

        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty($email_err) && empty($password_err)) {
            $sql = "SELECT usertype, email, password, Approved FROM user WHERE email = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $param_email);

                $param_email = $email;

                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($usertype, $email, $hashed_password, $approved);
                        if ($stmt->fetch()) {
                            if ($approved == 1) {
                                if (password_verify($password, $hashed_password)) {

                                    session_start();
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["usertype"] = $usertype;
                                    $_SESSION["email"] = $email;
                                    if ($usertype == "Student") {
                                        header("location: ../Student/student_home.php");
                                    } elseif ($usertype == "Lecturer") {
                                        header("location: ../Lecturer/lecturer_home.php");
                                    } elseif ($usertype == "Managing assistant") {
                                        header("location: managing_assistant_home.php");
                                    }
                                } else {
                                    $password_err = "The password you entered is not valid.";
                                }
                            } else {
                                $email_err = "Your account has not been approved yet";
                            }
                        }
                    } else {
                        $email_err = "Email not registered.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                $stmt->close();
            }
        }
    } elseif (isset($_POST['register'])) {


        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } else {
            $email = trim($_POST["email"]);

            $sql = "SELECT email FROM user WHERE email = ?";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $param_email);

                $param_email = $email;

                if ($stmt->execute()) {
                    $stmt->store_result();

                    if ($stmt->num_rows == 1) {
                        $email_err = "This email is already registered.";
                    } else {

                        if (empty(trim($_POST["password"]))) {
                            $password_err = "Please enter a password.";
                        } else {
                            $password = trim($_POST["password"]);
                        }

                        if (empty(trim($_POST["confirm_password"]))) {
                            $confirm_password_err = "Please confirm password.";
                        } else {
                            $confirm_password = trim($_POST["confirm_password"]);
                            if ($password != $confirm_password) {
                                $confirm_password_err = "Password did not match.";
                            }
                        }

                        if (strpos($email, "@") !== false) {
                            $email_parts = explode("@", $email);
                            $domain_parts = explode(".", $email_parts[1]);
                            $domain = $domain_parts[0];
                            if (preg_match("/^ma\.([a-zA-Z0-9_]+)@/", $email)) {
                                $usertype = "Managing Assistant";
                            } elseif (preg_match("/^\d{4}[eE]\d{3}@/", $email)) {
                                $usertype = "Student";
                            } elseif (preg_match("/^[a-zA-Z0-9._%+-]+@/", $email)) {
                                $usertype = "Lecturer";
                            } else {
                                $usertype_err = "Invalid email format.";
                            }
                        } else {
                            $email_err = "Invalid email format.";
                        }
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                $stmt->close();
            }
        }

        if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
            $sql = "INSERT INTO user (email, password, usertype, Approved) VALUES (?, ?, ?, 0)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $param_email, $param_password, $param_usertype);

                $param_email = $email;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_usertype = $usertype;

                if ($stmt->execute()) {

                    header("location: index.php");
                } else {
                    echo "Something went wrong. Please try again later.";
                }
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Register Form</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Marvel">
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            if (password !== confirmPassword) {
                document.getElementById("confirm_password").setCustomValidity("Passwords do not match");
                return false;
            } else {
                document.getElementById("confirm_password").setCustomValidity('');
                return true;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var inputs = document.querySelectorAll(".input");
            inputs.forEach(function(input) {
                input.addEventListener("focus", function() {
                    this.classList.add("focus");
                    if (this.classList.contains("error")) {
                        this.classList.remove("error");
                        var errorMsg = this.nextElementSibling;
                        if (errorMsg && errorMsg.classList.contains("error-msg")) {
                            errorMsg.style.display = 'none';
                        }
                    }
                });
                input.addEventListener("blur", function() {
                    this.classList.remove("focus");
                });
            });

            var loginLabel = document.getElementById('login-label');
            var registerLabel = document.getElementById('register-label');
    
            loginLabel.addEventListener('click', function() {
                changeBackgroundVideo('../image/login4.mp4');
                document.querySelector('.container').classList.remove('right-aligned');
            });

            registerLabel.addEventListener('click', function() {
                changeBackgroundVideo('../image/register5.mp4');
            });

        });

        function changeBackgroundVideo(src) {
            var video = document.getElementById('background-video');
            video.classList.add('fade-out');

            setTimeout(function() {
                var source = video.querySelector('source');
                source.src = src;
                video.load(); 

                video.addEventListener('loadeddata', function() {
                    video.classList.remove('fade-out');
                }, { once: true });
            }, 300); 
        }
    </script>
</head>

<body>
    <div class="container">
        <video autoplay muted loop id="background-video">
            <source src="../image/login4.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="main">
            <input type="checkbox" id="chk" aria-hidden="true">

            <div class="login">
                <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label for="chk" id="login-label" aria-hidden="true">Log in</label>
                    <input class="input <?php echo (!empty($email_err)) ? 'error' : ''; ?>" type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <?php echo (!empty($email_err)) ? '<span class="error-msg">' . htmlspecialchars($email_err) . '</span>' : ''; ?>
                    <input class="input <?php echo (!empty($password_err)) ? 'error' : ''; ?>" type="password" name="password" placeholder="Password" required>
                    <?php echo (!empty($password_err)) ? '<span class="error-msg">' . htmlspecialchars($password_err) . '</span>' : ''; ?>
                    <button class="btn-21" type="submit" name="login"><span><i class="fa-solid fa-user" style="margin-right: 15px;"></i>Log in</span></button>
                </form>
            </div>

            <div class="register">
                <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validatePassword()">
                    <label for="chk" id="register-label" aria-hidden="true">Register</label>
                    <input class="input <?php echo (!empty($email_err)) ? 'error' : ''; ?>" type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <?php echo (!empty($email_err)) ? '<span class="error-msg">' . htmlspecialchars($email_err) . '</span>' : ''; ?>
                    <input class="input <?php echo (!empty($password_err)) ? 'error' : ''; ?>" type="password" name="password" id="password" placeholder="Password" required>
                    <?php echo (!empty($password_err)) ? '<span class="error-msg">' . htmlspecialchars($password_err) . '</span>' : ''; ?>
                    <input class="input <?php echo (!empty($confirm_password_err)) ? 'error' : ''; ?>" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <?php echo (!empty($confirm_password_err)) ? '<span class="error-msg">' . htmlspecialchars($confirm_password_err) . '</span>' : ''; ?>
                    <button class="btn-21" type="submit" name="register"><span><i class="fa-solid fa-user-plus" style="margin-right: 15px;"></i>Register</span></button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
