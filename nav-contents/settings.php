<?php
// DATABASE CONNECTION
include '../config.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    /* SELECT TABLE FROM USERS AND DISPLAY DATA */
    $query1 = "SELECT firstName, lastName, email, images, dark_mode FROM users WHERE user_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    /* VARIABLES TO DISPLAY DATA IN HTML */
    if($result1->num_rows > 0) {
        $row = $result1->fetch_assoc();
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $email = $row['email'];
        $userImage = $row['images'];    /* IMAGE */

        /* DARK MODES */
        $darkMode = $row['dark_mode'];
        $_SESSION['dark_mode'] = $darkMode;
    }
}

if(isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $user_id = $_SESSION['user_id'];

    $message = "";

    /* IF THE USER ENTER NOT EMAIL, DISPLAY INVALID */
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } 
    else {
        $query = "SELECT firstName, lastName, email FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($currentFirstName, $currentLastName, $currentEmail);
        $stmt->fetch();
        $stmt->close();

        $nameChanged = ($firstName !== $currentFirstName || $lastName !== $currentLastName);
        $emailChanged = ($email !== $currentEmail);

        if($nameChanged || $emailChanged) {
            $updateQuery = "UPDATE users SET firstName = ?, lastName = ?, email = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssi", $firstName, $lastName, $email, $user_id);

            if($stmt->execute()) {
                $message = "Information Updated.";
            } 
            else {
                $message = "Failed to Update.";
            }

            $stmt->close();
        }

        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileName = $_FILES['image']['name'];
            $tempName = $_FILES['image']['tmp_name'];

            $targetDir = '../assets/profile_users/';
            $targetPath = $targetDir . $fileName;

            if(move_uploaded_file($tempName, $targetPath)) {
                $imageQuery = "UPDATE users SET images = ? WHERE user_id = ?";
                $stmt = $conn->prepare($imageQuery);
                $stmt->bind_param("si", $fileName, $user_id);

                if ($stmt->execute()) {
                    $message .= " Image Uploaded.";
                } else {
                    $message .= " Failed to Update.";
                }

                $stmt->close();
            } 
            else {
                $message .= " Error uploading file.";
            }
        }
    }
    
    /* REDIRECT BACK TO THE PREVIOUS PAGE OR HANDLE SUCCES MESSAGE */
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/* DARK MODE */
if(isset($_POST['toggleDarkMode'])) {
    $darkMode = $_POST['darkMode'];
    $_SESSION['dark_mode'] = $darkMode;

    /* UPDATE THE USERS TABLE FOR DARK_MODE */
    $updateDarkModeQuery = "UPDATE users SET dark_mode = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateDarkModeQuery);
    $stmt->bind_param("ii", $darkMode, $user_id);
    $stmt->execute();
    $stmt->close();

    /* REDIRECT BACK TO THE PREVIOUS PAGE OR HANDLE SUCCES MESSAGE */
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Techtilanotes | Profile</title>
    <link rel="icon" type="image/x=icon" href="../assets/ibon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/535a9c8dda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/note.css">
</head>
<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">
    <nav class="fix-position">
        <ul>
            <li>
                <a href="" class="user">
                    <!-- NAVIGATION PROFILE -->
                    <img src="<?php echo isset($userImage) ? '../assets/profile_users/' . $userImage : '../assets/ibon.png'; ?>" alt="" class="logo">
                    <!-- USER DISPLAY NAME AND EMAIL -->
                    <div>
                        <p class="bold"><?php echo $firstName; ?></p>
                        <p><?php echo $email; ?></p>
                    </div>
                </a>
            </li>
            <li class="menu">   <!-- NOTES -->
                <a href="../note.php">
                    <i class='bx bxs-note' ></i>
                    <span class="nav-item">Notes</span>
                </a>
            </li>
            <li class="menu">   <!-- POMODORO -->
                <a href="../nav-contents/stopwatch.php">
                    <i class='bx bxs-stopwatch' ></i>
                    <span class="nav-item">Stop watch</span>
                </a>
            </li>
            <li class="menu">   <!-- USER PROFILE -->
                <a href="#" class="active">
                    <i class="fa-solid fa-user"></i>
                    <span class="nav-item">Profile</span>
                </a>
            </li>
            <li class="menu">   <!-- DARK MODE -->
                <div id="theme-toggle">
                    <!-- UPDATE THE ICON AND DISPLAY NAME BASE ON THE DARK MODE -->
                    <i class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'fa-solid fa-moon' : 'fa-solid fa-sun'; ?>"></i>
                    <span class="nav-item"><?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'Dark Mode' : 'Light Mode'; ?></span>
                </div>
            </li>
            <li class="menu">   <!-- LOGOUT BUTTON -->
                <a href="../logout.php" class="logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- USER DISPLAY PROFILE DATA -->
    <section class="contact-container">
        <form action="" method="POST" enctype="multipart/form-data" class="contact-left">
            <!-- DISPLAY MESSAGE -->
            <?php 
                if(!empty($message)) {
                echo '<p>' . $message . '</p>';
            }?>
            <!-- DISPLAY IMAGE -->
            <label for="image-upload" class="profile">
                <img id="preview-image" src="<?php echo isset($userImage) ? '../assets/profile_users/' . $userImage : '../assets/ibon.png'; ?>" alt="Upload Image">
            </label>
            <!-- UPLOAD IMAGE -->
            <input id="image-upload" type="file" name="image" accept=".jpg, .jpeg, .png" style="display:none;">
            <!-- DISPLAY NAME AND EMAIL -->
            <div class="two-col">
                <input type="text" name="firstName" value="<?php echo $firstName; ?>" class="contact-inputs">
                <input type="text" name="lastName" value="<?php echo $lastName; ?>" class="contact-inputs">
            </div>
            <input type="text" name="email" value="<?php echo $email; ?>" class="contact-inputs">
            <!-- SUBMIT BUTTON -->
            <button type="submit" name="submit">Save</button>
        </form>
    </section>
    
    <!-- DARK MODE FORM WHEN CLICK THE BUTTON FORM NAVIGATION IT WILL SUBMIT -->
    <form method="POST" id="darkModeForm" style="display: none;">
        <input type="hidden" name="darkMode" id="darkModeInput">
        <input type="hidden" name="toggleDarkMode" value="1">
    </form>

    <!-- SCRIPT FOR IMAGE -->
    <script>
        /* FUNCTION FOR PUTING PICTURE TO SEE THE IMAGE */
        document.getElementById('image-upload').addEventListener('change', function() {
            var input = this;

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('preview-image').setAttribute('src', e.target.result);
                }

                /* NEW IMAGE DISPLAY */
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
    <!-- DARK MODE SCRIPT -->
    <script src="../script/dark_mode.js"></script>
</body>
</html>