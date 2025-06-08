<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Student") {
    header("location: ../MA/index.php");
    exit;
}

require_once "../MA/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $studentName = $_POST["studentName"];
    $regNo = $_POST["regNo"];
    $batchNo = $_POST["batchNo"];
    $semester = $_POST["semester"];

    $sql = "UPDATE student SET Student_Name = ?, RegNo = ?, BatchNo = ?, Semester = ? WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $studentName, $regNo, $batchNo, $semester, $_SESSION["email"]);

        if ($stmt->execute()) {
            header("location: student_home.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Details</title>
    <link rel="stylesheet" href="update_student_style.css">
</head>

<body>
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Edit Student Details</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="studentName">Student Name:</label>
                    <input type="text" id="studentName" name="studentName" value="<?php echo htmlspecialchars($Student_Name); ?>">
                </div>
                <div class="form-group">
                    <label for="regNo">Reg No:</label>
                    <input type="text" id="regNo" name="regNo" value="<?php echo htmlspecialchars($RegNo); ?>">
                </div>
                <div class="form-group">
                    <label for="batchNo">Batch No:</label>
                    <input type="text" id="batchNo" name="batchNo" value="<?php echo htmlspecialchars($BatchNo); ?>">
                </div>
                <div class="form-group">
                    <label for="semester">Semester:</label>
                    <input type="text" id="semester" name="semester" value="<?php echo htmlspecialchars($Semester); ?>">
                </div>
                <div class="form-group">
                    <input type="submit" value="Submit">
                    <button type="button" class="close" onclick="closePopup()">Close</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
</body>

</html>