<?php
// DATABASE CONNECTION
include '../config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    /* SELECT TABLE FROM USERS AND DISPLAY DATA */
    $query1 = "SELECT firstName, lastName, email, images, dark_mode FROM users WHERE user_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    /* VARIABLES TO DISPLAY DATA IN HTML */
    if ($result1->num_rows > 0) {
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

/* DARK MODE */
if (isset($_POST['toggleDarkMode'])) {
    $darkMode = $_POST['darkMode'];
    $_SESSION['dark_mode'] = $darkMode;

    $updateDarkModeQuery = "UPDATE users SET dark_mode = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateDarkModeQuery);
    $stmt->bind_param("ii", $darkMode, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Techtilanotes | Pomodoro</title>
    <!-- WEB ICON -->
    <link rel="icon" type="image/x=icon" href="../assets/ibon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS FILE -->
    <link rel="stylesheet" href="../styles/note.css">
    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/535a9c8dda.js" crossorigin="anonymous"></script>
</head>

<!-- UPDATE THE CLASS NAME IN BODY -->
<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">
    
    <!-- NAVIGATION SIDE BAR -->
    <nav class="fix-position">
        <ul>
            <li>
                <a href="" class="user">
                    <!-- NAVIGATION PROFILE -->
                    <img src="<?php echo isset($userImage) ? '../image/' . $userImage : '../assets/ibon.png'; ?>" alt="" class="logo">
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
                <a href="#" class="active">
                    <i class='bx bxs-stopwatch' ></i>
                    <span class="nav-item">Stop watch</span>
                </a>
            </li>
            <li class="menu">   <!-- USER PROFILE -->
                <a href="../nav-contents/settings.php">
                    <i class="fa-solid fa-user"></i>
                    <span class="nav-item">Profile</span>
                </a>
            </li>
            <li class="menu">   <!-- DARK MODE BUTTON -->
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

    <!-- POMODORO -->
    <section id="pomodoro">
        <div class="container">
            <!-- TEXT FOR WORK AND BREAK -->
            <div class="painel">
                <p id="work">Work&nbsp;&nbsp;</p>
                <p>|</p>
                <p id="break">&nbsp;&nbsp;Break</p>
            </div>
            <!-- TIME -->
            <div class="timer">
                <div class="circle">
                    <div class="time">
                        <p id="minutes"></p>    <!-- MINUTES -->
                        <p>:</p>
                        <p id="seconds"></p>    <!-- SECONDS -->
                    </div>
                </div>
                <!-- CONTROL PAUSE PLAY AND RESTART -->
                <div class="controls">
                    <button id="pause" onclick="pause()"><i class="fa-solid fa-pause"></i></button>
                    <button id="start" onclick="start()"><i class="fa-solid fa-play"></i></button>
                    <button id="reset" onclick="reset()"><i class="fa-solid fa-arrow-rotate-left"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- DARK MODE FORM WHEN CLICK THE BUTTON FORM NAVIGATION IT WILL SUBMIT -->
    <form method="POST" id="darkModeForm" style="display: none;">
        <input type="hidden" name="darkMode" id="darkModeInput">
        <input type="hidden" name="toggleDarkMode" value="1">
    </form>

    <!-- POMODORO SCRIPT -->
    <script src="../script/pomodoro.js"></script>
    <!-- DARK MODE SCRIPT -->
    <script src="../script/dark_mode.js"></script>
</body>
</html>