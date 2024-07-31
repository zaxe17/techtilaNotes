<?php
// DATABASE CONNECTION
include 'config.php';
session_start();

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    /* SELECT TABLE FROM USERS AND DISPLAY DATA */
    $query1 = "SELECT firstName, email, images, dark_mode FROM users WHERE user_id = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    /* VARIABLES TO DISPLAY DATA IN HTML */
    if($result1->num_rows > 0) {
        $row = $result1->fetch_assoc();
        $firstName = $row['firstName'];
        $email = $row['email'];
        $userImage = $row['images']; /* IMAGE */

        /* DARK MODE */
        $darkMode = $row['dark_mode'];
        $_SESSION['dark_mode'] = $darkMode;
    }

    /* ORDERED BASE ON PIN 1 FOR PIN AND 0 FOR UNPIN */
    $query2 = "SELECT * FROM notess WHERE user_id = ? ORDER BY pinned DESC";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $notes = array();

    if($result2->num_rows > 0) {
        while ($row = $result2->fetch_assoc()) {
            $notes[] = $row;
        }
    }
}

/* DARK MODE */
if (isset($_POST['toggleDarkMode'])) {
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
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Techtilanotes | Notes</title>
    <link rel="icon" type="image/x=icon" href="./assets/ibon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS FILE -->
    <link rel="stylesheet" href="./styles/note.css">
    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SCRIPT FOR PREVENT BACK -->
    <script src="./script/window.js"></script>
    <!-- LINK FOR ICONS -->
    <script src="https://kit.fontawesome.com/535a9c8dda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
</head>

<!-- UPDATE THE CLASS NAME IN BODY -->
<body class="<?php echo isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'dark-mode' : ''; ?>">
    <!-- NAVIGATION SIDE BAR -->
    <nav class="fix-position">
        <ul>
            <li>
                <a href="" class="user">
                    <!-- NAVIGATION PROFILE -->
                    <img src="<?php echo isset($userImage) ? './image/' . $userImage : './assets/ibon.png'; ?>" alt="" class="logo">
                    <!-- USER DISPLAY NAME AND EMAIL -->
                    <div>
                        <p class="bold"><?php echo $firstName; ?></p>
                        <p><?php echo $email; ?></p>
                    </div>
                </a>
            </li>
            <li class="menu">   <!-- NOTES -->
                <a href="#" class="active">
                    <i class='bx bxs-note' ></i>
                    <span class="nav-item">Notes</span>
                </a>
            </li>
            <li class="menu">   <!-- POMODORO WATCH -->
                <a href="./nav-contents/stopwatch.php">
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
                <a href="logout.php" class="logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- NOTES -->
    <section id="notes">
        <!-- IF ADD BUTTON OR THE NOTES CLICKED, THE FORM WILL SHOW -->
        <div class="popup-box">
            <div class="popup">
                <div class="content">
                    <header>
                        <p></p>
                        <i class="uil uil-times"></i>
                    </header>
                    <!-- ADD AND EDIT FORM -->
                    <form id="noteForm" action="" method="POST">
                        <div class="row title">
                            <label>Title</label>
                            <input type="text" spellcheck="false" name="title" id="title" placeholder="Title">
                        </div>
                        <div class="row description">
                            <label>Notes</label>
                            <textarea spellcheck="false" name="content" id="content" placeholder="Notes"></textarea>
                        </div>
                        <!-- BUTTON FOR ADD AND UPDATE-->
                        <input type="hidden" name="note_id" id="note_id" >
                        <input id="add_update" type="submit" value="" name="">
                    </form>
                </div>
            </div>
        </div>
        <!-- DISPLAY USER NOTES AND THE ADD BUTTON -->
        <div class="wrapper">
            <!-- ADD BUTTON -->
            <li class="add-box">
                <div class="chicken">
                    <div class="comb1"></div>
                    <div class="comb2"></div>
                    <div class="eye"></div>
                    <div class="beak"></div>
                    <div class="wattle"></div>
                </div>
                <p>Add new note</p>
            </li>
            <!-- DISPLAY USER NOTES -->
            <?php foreach ($notes as $note) : ?>    <!-- START OF READING DATA FOR USER -->
                <li class="note <?php echo ($note['pinned'] == 1) ? 'pinned' : ''; ?>">
                    <!-- CLICK THE NOTE TO EDIT -->
                    <div class="note-content" onclick="updateNote(<?php echo $note['note_id']; ?>, '<?php echo htmlspecialchars(nl2br($note['title']) ? str_replace(array("\r\n"), '<br/>', addslashes($note['title'])) : '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars(nl2br($note['content']) ? str_replace(array("\r\n"), '<br/>', addslashes($note['content'])) : '', ENT_QUOTES); ?>')">
                        <div class="details">
                            <p><?php echo nl2br($note['title']); ?></p>
                            <span><?php echo nl2br($note['content']); ?></span>
                        </div>
                    </div>
                    <div class="bottom-content">
                        <span><?php echo $note['currentDate']; ?></span>    <!-- CURRENT DATE -->
                        <!-- SETTINGS BUTTON -->
                        <div class="settings">
                            <i onclick="showMenu(this)" class="fa-solid fa-feather-pointed <?php echo ($note['pinned'] == 1) ? 'pinned' : ''; ?>"></i>
                            <ul class="menu <?php echo ($note['pinned'] == 1) ? 'pinned' : ''; ?>">
                                <!-- PIN BUTTON -->
                                <form action="./query/notePin.php" method="POST">
                                    <li>
                                        <input type="hidden" name="note_id" value="<?php echo $note['note_id']; ?>">
                                        <button type="submit" class="pin-btn" name="toggle_pin">
                                            <i class='bx bxs-pin'></i> <?php echo ($note['pinned'] == 1) ? 'Unpin' : 'Pin'; ?>
                                        </button>
                                    </li>
                                </form>
                                <!-- ADD LABEL -->
                                <li>
                                    <button type="submit" class="pin-btn" name="toggle_pin">
                                        <i class="fa-solid fa-tag"></i> Add Label
                                    </button>
                                </li>
                                <!-- DELETE BUTTON -->
                                <form action="./query/delete.php" method="POST">
                                    <li>
                                        <input type="hidden" name="note_id" value="<?php echo $note['note_id']; ?>">
                                        <button type="submit" name="delete">
                                            <i class='bx bxs-trash' ></i> delete
                                        </button>
                                    </li>
                                </form>
                            </ul>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>    <!-- END OF READING DATA FOR USER -->
        </div>
    </section>
    
    <!-- DARK MODE FORM WHEN CLICK THE BUTTON FORM NAVIGATION IT WILL SUBMIT -->
    <form method="POST" id="darkModeForm" style="display: none;">
        <input type="hidden" name="darkMode" id="darkModeInput">
        <input type="hidden" name="toggleDarkMode" value="1">
    </form>
    
    <!-- SCRIPT FOR NOTES AND PASSING DATA TO ANOTHER TAG -->
    <script src="./script/script.js"></script>
    <!-- DARK MODE SCRIPT -->
    <script src="./script/dark_mode.js"></script>
</body>
</html>