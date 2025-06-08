<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["usertype"] !== "Managing assistant") {
    header("location: index.php");
    exit;
}

require_once "config.php";

$errorMessage = "";

$queryDistinctCategories = "SELECT DISTINCT QueType FROM (
                                SELECT QueType FROM course_feedback_contains
                                UNION
                                SELECT QueType FROM lecturer_feedback_contains
                            ) AS combined_categories";
$resultDistinctCategories = mysqli_query($conn, $queryDistinctCategories);

$combinedCategories = array();

while ($row = mysqli_fetch_assoc($resultDistinctCategories)) {
    $category = $row['QueType'];
    $combinedCategories[$category] = array('Course' => false, 'Lecturer' => false);
}

$queryCourseFeedback = "SELECT QueId, QueType, QueText FROM course_feedback_contains";
$resultCourseFeedback = mysqli_query($conn, $queryCourseFeedback);

$queryLecturerFeedback = "SELECT QueId, QueType, QueText FROM lecturer_feedback_contains";
$resultLecturerFeedback = mysqli_query($conn, $queryLecturerFeedback);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question List</title>
    <link rel="stylesheet" href="question_list_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="sidebar collapsed" id="sidebar">
        <div class="logo-container">
            <img src="../image/LOGO.png" alt="Logo" class="logo">
        </div>
        <div class="menu">
        <div class="menu">
            <a href="managing_assistant_home.php"  onclick="expandSidebar(event)"><i class="fa-solid fa-house "></i><span class="menu-text">Dashboard</span></a>
            <a href="feedback.php" onclick="expandSidebar(event)"><i class="fa-solid fa-ranking-star"></i><span class="menu-text">Feedback</span></a>
            <a href="notice.php" onclick="expandSidebar(event)"><i class="fa-solid fa-bell"></i><span class="menu-text">Notice</span></a>
            <a href="../Lecturer/lecturer_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-user-tie"></i><span class="menu-text">Lecturer List</span></a>
            <a href="../Student/student_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-users"></i><span class="menu-text">Students List</span></a>
            <a href="../Course/course_list.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book"></i><span class="menu-text">Course List</span></a>
            <a href="question_list.php" class="active" onclick="expandSidebar(event)"><i class="fa-solid fa-file-lines fa-beat"></i><span class="menu-text">Questions List</span></a>
            <a href="../Course/course_allocation.php" onclick="expandSidebar(event)"><i class="fa-solid fa-book-medical"></i><span class="menu-text">Course Allocation</span></a>
            <a href="#" onclick="openResetPasswordPopup(); expandSidebar(event)"><i class="fa-solid fa-lock"></i><span class="menu-text">Reset Password</span></a>
            <a href="index.php" onclick="expandSidebar(event)"><i class="fa-solid fa-right-from-bracket"></i><span class="menu-text">Logout</span></a>
        </div>
        </div>
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-angle-double-right" id="toggleIcon"></i></button>
    </div>

    <div class="content expanded" id="content">
        <!-- Category table -->
        <div class="container">
            <div class="category-table">
                <h2>Category</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Types</th>
                            <th>QueType</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryCourseFeedbackDistinct = "SELECT DISTINCT QueType FROM course_feedback_contains";
                        $resultCourseFeedbackDistinct = mysqli_query($conn, $queryCourseFeedbackDistinct);

                        $queryLecturerFeedbackDistinct = "SELECT DISTINCT QueType FROM lecturer_feedback_contains";
                        $resultLecturerFeedbackDistinct = mysqli_query($conn, $queryLecturerFeedbackDistinct);

                        $combinedCategories = [];

                        while ($row = mysqli_fetch_assoc($resultCourseFeedbackDistinct)) {
                            $category = $row['QueType'];
                            $combinedCategories[$category] = ['Course' => true, 'Lecturer' => false];
                        }

                        while ($row = mysqli_fetch_assoc($resultLecturerFeedbackDistinct)) {
                            $category = $row['QueType'];
                            if (isset($combinedCategories[$category])) {
                                $combinedCategories[$category]['Lecturer'] = true;
                            } else {
                                $combinedCategories[$category] = ['Course' => false, 'Lecturer' => true];
                            }
                        }

                        foreach ($combinedCategories as $category => $types) {
                            echo "<tr>";
                            echo "<td contenteditable='false'>";
                            if ($types['Course']) {
                                echo "Course";
                            }
                            if ($types['Lecturer']) {
                                echo ($types['Course'] ? ', ' : '') . "Lecturer";
                            }
                            echo "</td>";
                            echo "<td contenteditable='false'>" . $category . "</td>";
                            echo "<td>";
                            echo "<button class='edit-button' onclick='editCategory(this)'><i class='far fa-pen-to-square' style='margin-right: 8px;''></i>Edit</button>";
                            echo "<button class='delete-button' data-category='" . $category . "' onclick='deleteCategory(this)'><i class='fa-solid fa-trash' style='margin-right:8px;'></i>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Course Questions table -->
        <div class="container">
            <div class="question-table">
                <h2>Questions - Course</h2>
                <table>
                    <thead>
                        <tr>
                            <th>QueId</th>
                            <th>QueType</th>
                            <th>Question</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultCourseFeedback && mysqli_num_rows($resultCourseFeedback) > 0) {
                            while ($row = mysqli_fetch_assoc($resultCourseFeedback)) {
                                echo "<tr>";
                                echo "<td>" . $row['QueId'] . "</td>";
                                echo "<td>" . $row['QueType'] . "</td>";
                                echo "<td>" . $row['QueText'] . "</td>";
                                echo "<td><button class='edit-button' onclick='editQuestion(this)'><i class='fa-solid fa-file-pen' style='margin-right: 8px;'></i>Edit</button> <button class='delete-button' data-queid='" . $row['QueId'] . "' data-quetype='Course' onclick='deleteQuestion(this)'><i class='fa-solid fa-trash' style='margin-right:8px;'></i>Delete</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <button class="addQuestion" onclick="openAddCourseQuestionPopup()"><i class="fa-solid fa-file-circle-plus fa-beat" style="margin-right:5px;"></i>Add Question</button>
            </div>
        </div>


        <!-- Add Course Question Popup -->
        <div id="addCourseQuestionPopup" class="popup">
            <div class="popup-content">
                <span class="close" onclick="closeAddCourseQuestionPopup()">&times;</span>
                <h2>Add Course Question</h2>
                <form id="addCourseQuestionForm" action="add_course_question.php" method="post">
                    <label for="courseQueId">Question Id</label>
                    <input type="text" id="courseQueId" name="courseQueId" required><br><br>
                    <label for="courseQueType">Question Type</label>
                    <select id="courseQueType" name="courseQueType" required onchange="toggleNewTypeInput1()">
                        <?php

                        require_once "config.php";

                        $queryQueTypes = "SELECT DISTINCT QueType FROM course_feedback_contains";
                        $resultQueTypes = mysqli_query($conn, $queryQueTypes);

                        if ($resultQueTypes && mysqli_num_rows($resultQueTypes) > 0) {
                            while ($row = mysqli_fetch_assoc($resultQueTypes)) {
                                echo "<option value='" . $row['QueType'] . "'>" . $row['QueType'] . "</option>";
                            }
                        }
                        ?>
                        <option value="New Type">New Type</option>
                    </select><br>
                    <input type="text" id="newCourseQueType" name="newCourseQueType" placeholder="New Type" style="display:none;"><br><br>
                    <label for="courseQueText">Question Text</label>
                    <input type="text" id="courseQueText" name="courseQueText" required><br><br>
                    <button type="submit" class="addQuestion"><i class="fa-solid fa-file-circle-plus fa-beat" style="margin-right: 7px;"></i>Add Question</button>
                </form>
            </div>
        </div>

        <!-- Lecturer Questions table -->
        <div class="container">
            <div class="question-table">
                <h2>Questions - Lecturer</h2>
                <table>
                    <thead>
                        <tr>
                            <th>QueId</th>
                            <th>QueType</th>
                            <th>Question</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultLecturerFeedback && mysqli_num_rows($resultLecturerFeedback) > 0) {
                            while ($row = mysqli_fetch_assoc($resultLecturerFeedback)) {
                                echo "<tr>";
                                echo "<td>" . $row['QueId'] . "</td>";
                                echo "<td>" . $row['QueType'] . "</td>";
                                echo "<td>" . $row['QueText'] . "</td>";
                                echo "<td><button class='edit-button' onclick='editQuestion(this)'><i class='fa-solid fa-file-pen' style='margin-right: 8px;'></i>Edit</button> <button class='delete-button' data-queid='" . $row['QueId'] . "' data-quetype='Lecturer' onclick='deleteQuestion(this)'><i class='fa-solid fa-trash' style='margin-right:8px;'></i>Delete</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <button class="addQuestion" onclick="openAddLecturerQuestionPopup()"><i class="fa-solid fa-file-circle-plus fa-beat" style="margin-right: 7px;"></i>Add Question</button>
            </div>
        </div>

    </div>

    <!-- Add Lecturer Question Popup -->
    <div id="addLecturerQuestionPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeAddLecturerQuestionPopup()">&times;</span>
            <h2>Add New Lecturer Question</h2>
            <form id="addLecturerQuestionForm" action="add_lecturer_question.php" method="post">
                <label for="lecturerQueId">Question ID:</label>
                <input type="text" id="lecturerQueId" name="lecturerQueId" required><br><br>
                <label for="lecturerQueType">Question Type:</label>
                <select id="lecturerQueType" name="lecturerQueType" required onchange="toggleNewTypeInput2()">
                    <?php
                    require_once "config.php";

                    $queryQueTypes = "SELECT DISTINCT QueType FROM lecturer_feedback_contains";
                    $resultQueTypes = mysqli_query($conn, $queryQueTypes);

                    if ($resultQueTypes && mysqli_num_rows($resultQueTypes) > 0) {
                        while ($row = mysqli_fetch_assoc($resultQueTypes)) {
                            echo "<option value='" . $row['QueType'] . "'>" . $row['QueType'] . "</option>";
                        }
                    }
                    ?>
                    <option value="New Type">New Type</option>
                </select>
                <input type="text" id="newLecturerQueType" name="newLecturerQueType" placeholder="New Type" style="display:none;"><br><br>
                <label for="lecturerQueText">Question Text:</label>
                <input type="text" id="lecturerQueText" name="lecturerQueText" required><br><br>
                <button type="submit" class="addQuestion"><i class="fa-solid fa-file-circle-plus fa-beat" style="margin-right: 7px;"></i>Add Question</button>
            </form>
        </div>
    </div>

    <!-- Reset Password Popup -->
    <div id="resetPasswordPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeResetPasswordPopup()">&times;</span>
            <h2>Reset Password</h2>
            <form id="resetPasswordForm" action="reset_password.php" method="post">
                <div class="form-group">
                    <label for="currentPassword">Current Password:</label>
                    <input type="password" id="currentPassword" name="currentPassword" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Reset Password">
                </div>
            </form>
        </div>
    </div>



    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var content = document.getElementById("content");
            var logoContainer = document.querySelector(".logo-container");
            var toggleIcon = document.getElementById("toggleIcon");
            if (sidebar.classList.contains("collapsed")) {
                sidebar.classList.remove("collapsed");
                sidebar.classList.add("expanded");
                content.classList.remove("expanded");
                content.classList.add("collapsed");
                logoContainer.style.display = "block";
                toggleIcon.classList.remove("fa-angle-double-right");
                toggleIcon.classList.add("fa-angle-double-left");
            } else {
                sidebar.classList.remove("expanded");
                sidebar.classList.add("collapsed");
                content.classList.remove("collapsed");
                content.classList.add("expanded");
                logoContainer.style.display = "none";
                toggleIcon.classList.remove("fa-angle-double-left");
                toggleIcon.classList.add("fa-angle-double-right");
            }
        }

        function expandSidebar(event) {
            var sidebar = document.getElementById("sidebar");
            var content = document.getElementById("content");
            var logoContainer = document.querySelector(".logo-container");
            sidebar.classList.remove("collapsed");
            sidebar.classList.add("expanded");
            content.classList.remove("expanded");
            content.classList.add("collapsed");
            logoContainer.style.display = "block";
            var toggleIcon = document.getElementById("toggleIcon");
            toggleIcon.classList.remove("fa-angle-double-left");
            toggleIcon.classList.add("fa-angle-double-right");
        }


        function editCategory(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");

            cells[1].setAttribute("contenteditable", "true");

            var oldQueType = cells[0].textContent;

            button.dataset.oldQueType = oldQueType;

            button.innerHTML = '<i class="fa-solid fa-floppy-disk fa-beat" style="margin-right:5px;"></i> Save';
            button.setAttribute("onclick", "saveCategory(this)");
            button.classList.remove("edit-button");
            button.classList.add("save-button");
        }

        function saveCategory(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            var typesCell = cells[0];
            var queTypeCell = cells[1];
            var oldQueType = queTypeCell.textContent.trim();

            if (oldQueType === "") {
                alert("QueType cannot be empty.");
                return;
            }

            queTypeCell.textContent = oldQueType;

            var types = typesCell.textContent.trim();
            var updatedTypes = [];

            if (types.includes("Course")) {
                updatedTypes.push("Course");
            }
            if (types.includes("Lecturer")) {
                updatedTypes.push("Lecturer");
            }

            var updatedTypesStr = updatedTypes.join(", ");

            var tableName;
            if (updatedTypes.includes("Course") && updatedTypes.includes("Lecturer")) {
                tableName = "both_tables";
            } else if (updatedTypes.includes("Course")) {
                tableName = "course_feedback_contains";
            } else if (updatedTypes.includes("Lecturer")) {
                tableName = "lecturer_feedback_contains";
            } else {
                alert("Invalid types.");
                return;
            }

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText === "success") {
                        alert("Category updated successfully.");
                        button.innerHTML = '<i class="far fa-edit " style="margin-right:5px; "></i> Edit';
                        button.setAttribute("onclick", "editCategory(this)");
                        button.classList.remove("save-button");
                        button.classList.add("edit-button");

                        queTypeCell.removeAttribute("contenteditable");
                    } else {
                        alert("Failed to update category.");
                    }
                }
            };
            xhttp.open("POST", "update_category.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("oldQueType=" + encodeURIComponent(oldQueType) + "&updatedTypes=" + encodeURIComponent(updatedTypesStr) + "&tableName=" + encodeURIComponent(tableName));
        }



        function deleteCategory(button) {
            var category = button.getAttribute("data-category");

            if (confirm("Are you sure you want to delete this category?")) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText === "success") {
                            alert("Category deleted successfully.");
                            var row = button.parentNode.parentNode;
                            row.parentNode.removeChild(row);
                        } else {
                            alert("Failed to delete category.");
                        }
                    }
                };
                xhttp.open("GET", "delete_category.php?category=" + encodeURIComponent(category), true);
                xhttp.send();
            }
        }



        function editQuestion(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            for (var i = 1; i < cells.length - 1; i++) {
                cells[i].setAttribute("contenteditable", "true");
            }
            button.innerHTML = '<i class="fa-solid fa-floppy-disk fa-beat" style="margin-right:5px;"></i> Save';
            button.setAttribute("onclick", "saveQuestion(this)");
            button.classList.remove("edit-button");
            button.classList.add("save-button");
        }

        function saveQuestion(button) {
            var row = button.parentNode.parentNode;
            var cells = row.getElementsByTagName("td");
            var queId = cells[0].textContent;
            var queType = cells[1].textContent;
            var queText = cells[2].textContent;
            var tableName;

            if (queId.startsWith("CQ")) {
                tableName = "course_feedback_contains";
            } else if (queId.startsWith("LQ")) {
                tableName = "lecturer_feedback_contains";
            } else {
                alert("Invalid QueId format.");
                return;
            }

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText === "success") {
                        alert("Question updated successfully.");

                        button.innerHTML = '<i class="fa-solid fa-file-pen " style="margin-right:5px; "></i> Edit';
                        button.setAttribute("onclick", "editQuestion(this)");
                        button.classList.remove("save-button");
                        button.classList.add("edit-button");

                        for (var i = 1; i < cells.length - 1; i++) {
                            cells[i].removeAttribute("contenteditable");
                        }
                    } else {
                        alert("Failed to update question.");
                    }
                }
            };
            xhttp.open("POST", "update_question.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("tableName=" + tableName + "&queId=" + encodeURIComponent(queId) + "&queType=" + encodeURIComponent(queType) + "&queText=" + encodeURIComponent(queText));
        }


        function deleteQuestion(button) {
            var row = button.parentNode.parentNode;
            var queId = button.getAttribute("data-queid");
            var tableName;

            if (queId.startsWith("CQ")) {
                tableName = "course_feedback_contains";
            } else if (queId.startsWith("LQ")) {
                tableName = "lecturer_feedback_contains";
            } else {
                alert("Invalid QueId format.");
                return;
            }

            if (confirm("Are you sure you want to delete this question?")) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText === "success") {
                            alert("Question deleted successfully.");
                            row.parentNode.removeChild(row);
                        } else {
                            alert("Failed to delete question.");
                        }
                    }
                };
                xhttp.open("GET", "delete_question.php?tableName=" + tableName + "&queId=" + encodeURIComponent(queId), true);
                xhttp.send();
            }
        }

        function openAddCourseQuestionPopup() {
            document.getElementById("addCourseQuestionPopup").style.display = "block";
        }

        function closeAddCourseQuestionPopup() {
            document.getElementById("addCourseQuestionPopup").style.display = "none";
        }

        function openAddLecturerQuestionPopup() {
            var popup = document.getElementById("addLecturerQuestionPopup");
            popup.style.display = "block";
        }

        function closeAddLecturerQuestionPopup() {
            var popup = document.getElementById("addLecturerQuestionPopup");
            popup.style.display = "none";
        }

        function toggleNewTypeInput2() {
            var select = document.getElementById("lecturerQueType");
            var input = document.getElementById("newLecturerQueType");
            if (select.value === "New Type") {
                input.style.display = "inline-block";
                input.setAttribute("required", "required");
            } else {
                input.style.display = "none";
                input.removeAttribute("required");
            }
        }

        function toggleNewTypeInput1() {
            var select = document.getElementById("courseQueType");
            var input = document.getElementById("newCourseQueType");
            if (select.value === "New Type") {
                input.style.display = "inline-block";
                input.setAttribute("required", "required");
            } else {
                input.style.display = "none";
                input.removeAttribute("required");
            }
        }

        function openResetPasswordPopup() {
            var resetPopup = document.getElementById("resetPasswordPopup");
            resetPopup.classList.remove("hide");
            resetPopup.classList.add("show");
            resetPopup.style.display = "block";
        }

        function closeResetPasswordPopup() {
            var resetPopup = document.getElementById("resetPasswordPopup");
            resetPopup.classList.remove("show");
            resetPopup.classList.add("hide");
            setTimeout(function() {
                resetPopup.style.display = "none";
            }, 500);
        }
    </script>
</body>

</html>