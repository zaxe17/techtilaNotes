<?php
// DATABASE CONNECTION
include '../config.php';
session_start();

$message = "";

// Fetch user data
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user details
    $query1 = "SELECT firstName, lastName, email, images, dark_mode FROM users WHERE user_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($result1->num_rows > 0) {
        $row = $result1->fetch_assoc();
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $email = $row['email'];
        $userImage = $row['images'];
        $darkMode = $row['dark_mode'];
    }

    // Fetch user notes
    $query2 = "SELECT * FROM notess WHERE user_id = ? ORDER BY pinned DESC";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $notes = array();
    if ($result2->num_rows > 0) {
        while ($row = $result2->fetch_assoc()) {
            $notes[] = $row;
        }
    }
}

// Update user information
if (isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $user_id = $_SESSION['user_id'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Check if name or email has changed
        $query = "SELECT firstName, lastName, email FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($currentFirstName, $currentLastName, $currentEmail);
        $stmt->fetch();
        $stmt->close();

        $nameChanged = ($firstName !== $currentFirstName || $lastName !== $currentLastName);
        $emailChanged = ($email !== $currentEmail);

        if ($nameChanged || $emailChanged) {
            $updateQuery = "UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssi", $firstName, $lastName, $email, $user_id);

            if ($stmt->execute()) {
                $message = "Information Updated.";
            } else {
                $message = "Failed to Update.";
            }

            $stmt->close();
        }

        // Check if a new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileName = $_FILES['image']['name'];
            $tempName = $_FILES['image']['tmp_name'];

            $targetDir = '../image/';
            $targetPath = $targetDir . $fileName;

            if (move_uploaded_file($tempName, $targetPath)) {
                $imageQuery = "UPDATE users SET images = ? WHERE user_id = ?";
                $stmt = $conn->prepare($imageQuery);
                $stmt->bind_param("si", $fileName, $user_id);

                if ($stmt->execute()) {
                    $message .= " Image Uploaded.";
                } else {
                    $message .= " Failed to Update Image.";
                }

                $stmt->close();
            } else {
                $message .= " Error uploading file.";
            }
        }
    }
}

// Update dark mode preference
if (isset($_POST['toggleDarkMode'])) {
    $darkMode = $_POST['darkMode'];
    $updateDarkModeQuery = "UPDATE users SET dark_mode = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateDarkModeQuery);
    $stmt->bind_param("ii", $darkMode, $user_id);

    if ($stmt->execute()) {
        $message = "Dark mode preference updated.";
    } else {
        $message = "Failed to update dark mode preference.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Techtilanotes | Pomodoro</title>
    <link rel="icon" type="image/x-icon" href="../assets/ibon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/535a9c8dda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/note.css">
    <style>
        
        .dark-mode .fix-position ul li a {
            color: #ffffff;
        }
        
        /* Add more dark mode styles as needed */
    </style>
</head>
<body class="<?php echo $darkMode ? 'dark-mode' : ''; ?>">
    <nav class="fix-position">
        <ul>
            <li>
                <a href="" class="user">
                    <img src="<?php echo isset($userImage) ? '../image/' . $userImage : '../assets/ibon.png'; ?>" alt="" class="logo">
                    <div>
                        <p class="bold"><?php echo htmlspecialchars($firstName); ?></p>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </a>
            </li>
            <li class="menu">
                <a href="../note.php">
                    <i class='bx bxs-note' ></i>
                    <span class="nav-item">Notes</span>
                </a>
            </li>
            <li class="menu">
                <a href="../nav-contents/stopwatch.php">
                    <i class='bx bxs-stopwatch' ></i>
                    <span class="nav-item">Stop watch</span>
                </a>
            </li>
            <li class="menu">
                <a href="#">
                    <i class='bx bxs-calendar' ></i>
                    <span class="nav-item">Calendar</span>
                </a>
            </li>
            <li class="menu">
                <a href="#" class="active">
                    <i class='bx bxs-cog' ></i>
                    <span class="nav-item">Settings</span>
                </a>
            </li>
            <li class="menu">
                <div id="theme-toggle">
                    <i class="fa-solid fa-sun"></i>
                    <span class="nav-item">Dark Mode</span>
                </div>
            </li>
            <li class="menu">
                <a href="../logout.php" class="logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <section class="contact-container">
        <form action="" id="form" method="POST" enctype="multipart/form-data" class="contact-left">
            <div class="upload">
                <img src="<?php echo isset($userImage) ? '../image/' . $userImage : '../assets/ibon.png'; ?>" width="125" height="125">
                <div class="round">
                    <input type="hidden" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                    <input type="hidden" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
            <input type="submit" name="submit" value="Save Changes">
        </form>
    </section>

    <form method="POST" id="darkModeForm" style="display: none;">
        <input type="hidden" name="darkMode" id="darkModeInput">
        <input type="hidden" name="toggleDarkMode" value="1">
    </form>

    <script>
        document.getElementById("image").onchange = function() {
            document.getElementById('form').submit();
        }

        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const darkModeForm = document.getElementById('darkModeForm');
        const darkModeInput = document.getElementById('darkModeInput');

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDarkMode = body.classList.contains('dark-mode');
            darkModeInput.value = isDarkMode ? 1 : 0;
            darkModeForm.submit();
        });
    </script>

</body>
</html>
