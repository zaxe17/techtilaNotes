<?php
// DATABASE CONNECTION
include("config.php");
session_start();

// USERS LOGIN
if(isset($_POST['login_submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // CHECK IF THE INPUT IS EMPTY
    if(empty($email) || empty($password)) {
        $loginError[] = "Please fill in all fields.";
    } 
    else {
        // ERROR FOR EMAIL FORMAT
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loginError[] = "Invalid email format.";
        } else {
            // QUERY FOR USERS EMAIL
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            // EMAIL AND PASSWORD VERIFICATION BEFORE LOGIN
            if ($row) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    header("Location: note.php");
                    exit();
                } else {
                    // ERROR IF THE PASSWORD IS INCORRECT
                    $loginError[] = "Incorrect Password.";
                }
            } else {
                // ERROR IF THE EMAIL IS INCORRECT
                $loginError[] = "Incorrect Email.";
            }
        }
    }
}

// USERS REGISTRATION
if(isset($_POST["register_submit"])) {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $registerError = [];

    // CHECK IF THE INPUT IS EMPTY
    if(empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $registerError[] = "Please fill in all fields.";
    } 
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // CHECK IF THE EMAIL IS VALID
        $registerError[] = "Invalid email format.";
    }
    else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // DOUBLE CHECK IF THE EMAIL ALREADY EXISTS
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0) {
            $registerError[] = "Email already exist.";
        } 
        else {
            // INSERTING THE NEW USER IN DATABASE
            $stmt = mysqli_prepare($conn, "INSERT INTO users (firstName, lastName, email, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $firstName, $lastName, $email, $hashedPassword);
            mysqli_stmt_execute($stmt);
            $registerError[] = "You can sign in.";
            $color[] = "00ff33";    // TEXT COLOR LIGHT GREEN
        }
    }
}

// FORGOT PASSWORD
if(isset($_POST['forgot_password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // CHECK IF BOTH PASSWORDS ARE PROVIDED
    if(isset($password) && isset($confirm_password)) {
        // CHECK IF THE PASSWORDS MATCH
        if($password == $confirm_password) {
            // CHECK IF EMAIL EXISTS IN DATABASE
            $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) > 0) {
                // EMAIL EXISTS, PROCEED TO UPDATE PASSWORD
                mysqli_stmt_free_result($stmt);
                mysqli_stmt_close($stmt);

                // HASH THE PASSWORD
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // UPDATE USERS PASSWORD
                $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
                mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $email);
                if(mysqli_stmt_execute($stmt)) {
                    // IF SUCCESSFUL, DISPLAY SUCCESS MESSAGE
                    $message[] = "Password Update successfully.";
                    $color[] = "00ff33";    // TEXT COLOR LIGHT GREEN
                } 
                else {
                    $message[] = "Error updating password.";
                }
            } 
            else {
                // IF EMAIL DOES NOT EXIST
                $message[] = "Email does not exist.";
            }
            mysqli_stmt_close($stmt);
        } 
        else {
            // IF PASSWORDS DO NOT MATCH
            $message[] = "Password not match.";
        }
    } 
    else {
        $message[] = "Please provide both password and confirm password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Techtilanotes | Login</title>
    <link rel="icon" type="image/x=icon" href="./assets/ibon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS FILE -->
    <link rel="stylesheet" href="./styles/style1.css">
    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- SLICK CAROUSEL -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SCRIPT FOR PREVENT BACK -->
    <script src="./script/window.js"></script>
</head>
<body>
    <!-- NAVIGATION BAR -->
    <header>
        <!-- NAVIGATION LOGO -->
        <div  class="logo">
            <img src="./assets/ibon.png" alt="">
            <h2>Techtilanotes</h2>
        </div>
        <!-- NAVIGATION PAGE -->
        <nav class="navigation">
            <a href="#Login" class="active">home</a>
            <a href="#About">about</a>
            <a href="#Members">Members</a>
        </nav>
    </header>

    <!-- USERS LOGIN, REGISTRATION FORM -->
    <section id="Login" class="home show-animate">
        <div class="wrapper">
            <span class="bg-animate"></span>
            <span class="bg-animate2"></span>
            <!-- LOGIN FORM -->
            <div class="form-box login">
                <h2 class="animation" style="--i: 0; --j: 15;">Login</h2>
                <form action="" method="POST">
                    <!-- SHOW MESSAGE FOR ALL ERROR OR CHECK -->
                    <?php
                    $color_red = "ff0000";  // TEXT COLOR RED

                    /* LOGIN ERROR */
                    if (isset($loginError)) {
                        foreach ($loginError as $loginError) {
                            echo '<span class="error-msg animation" style="--i: 1; --j: 16;"><i class="bx bxs-error-circle"></i>' . $loginError . '</span>';
                        };
                    }

                    /* REGISTRATION ERROR */
                    if (isset($registerError)) {
                        foreach ($registerError as $registerError) {
                            $color = isset($color[$registerError]) ? htmlspecialchars($color[$registerError]) : $color_red;
                            echo '<span class="error-msg animation" style="--i: 1; --j: 16; color: #' . $color . ';"><i class="bx bxs-error-circle"></i>' . $registerError . '</span>';
                        };
                    }

                    /* FORGOT PASSWORD ERROR */
                    if (isset($message)) {
                        foreach ($message as $msg) {
                            $color = isset($color[$msg]) ? htmlspecialchars($color[$msg]) : $color_red;
                            echo '<span class="error-msg animation" style="--i: 1; --j: 16; color: #' . $color . ';"><i class="bx bxs-error-circle"></i>' . $msg . '</span>';
                        }
                    }
                    ?>
                    <div class="input-box animation" style="--i: 1; --j: 16;">
                        <input type="text" name="email" id="email" required>
                        <label>Email</label>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <div class="input-box animation" style="--i: 2; --j: 17;">
                        <input type="password" name="password" id="password" required>
                        <label>Password</label>
                        <i class='bx bxs-lock-alt' ></i>
                    </div>
                    <button type="submit" class="btn animation" style="--i: 3; --j: 18;" name="login_submit">Sign In</button>
                    <div class="logreg-link animation" style="--i: 4; --j: 19;">
                        <p>
                            Don't have an account? 
                            <a class="register-link">Sign Up</a>
                        </p>
                        <p>
                            <a class="forgot-link">forgot password?</a>
                        </p>
                    </div>
                </form>
            </div>
            <!-- LOGIN TEXT -->
            <div class="info-text login">
                <h2 class="animation" style="--i: 0; --j: 14;">Welcome Back!</h2>
                <p class="animation" style="--i: 1; --j: 15;">We're glad to see you again. Let's dive back into the adventure!</p>
            </div>
            <!-- FORGOT PASSWORD FORM -->
            <div class="form-box forgot">
                <h2 class="animation" style="--i: 2; --j: 6; font-size: 1.6rem;">Forgot Password?</h2>
                <form action="" method="POST">
                    <div class="input-box animation" style="--i: 3; --j: 5;">
                        <input type="text" name="email" id="email" required>
                        <label>Email</label>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <div class="input-box animation" style="--i: 4; --j: 4;">
                        <input type="password" name="password" id="password" required>
                        <label>New Password</label>
                        <i class='bx bxs-lock-alt' ></i>
                    </div>
                    <div class="input-box animation" style="--i: 5; --j: 3;">
                        <input type="password" name="confirm_password" id="confirm_password" required>
                        <label>Confirm Password</label>
                        <i class='bx bxs-lock-alt' ></i>
                    </div>
                    <button type="submit" class="btn animation" style="--i: 6; --j: 2;" name="forgot_password">Update Password</button>
                    <div class="logreg-link animation" style="--i: 7; --j: 1;">
                        <p>Don't have an account? 
                            <a class="LoginForgot">Login</a>
                        </p>
                    </div>
                </form>
            </div>
            <!-- REGISTRATION FORM -->
            <div class="form-box register">
                <h2 class="animation" style="--i: 11; --j: 1;">Sign Up</h2>
                <form action="" method="POST">
                    <div class="two-forms animation" style="--i: 12; --j: 2;">
                        <div class="input-box">
                            <input type="text" name="firstName" required>
                            <label>First Name</label>
                        </div>
                        <div class="input-box">
                            <input type="text" name="lastName" required>
                            <label>Last name</label>
                        </div>
                    </div>
                    <div class="input-box animation" style="--i: 13; --j: 3;">
                        <input type="text" name="email" required>
                        <label>Email</label>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <div class="input-box animation" style="--i: 14; --j: 4;">
                        <input type="password" name="password" required>
                        <label>Password</label>
                        <i class='bx bxs-lock-alt' ></i>
                    </div>
                    <button type="submit" class="btn animation" style="--i: 15; --j: 5;" name="register_submit">Sign Up</button>
                    <div class="logreg-link animation" style="--i: 16; --j: 6;">
                        <p>Already have an account? 
                            <a class="login-link">Login</a>
                        </p>
                    </div>
                </form>
            </div>
            <!-- REGISTRATION TEXT -->
            <div class="info-text register">
                <h2 class="animation" style="--i: 11; --j: 0;">Hello Traveler!!!</h2>
                <p class="animation" style="--i: 12; --j: 1;">Join us today and start your new journey. We're excited to have you!</p>
            </div>
        </div>
    </section>

    <!-- ABOUT PAGE -->
    <section id="About" class="show-animate">
        <div class="sec">
            <!-- ABOUT PAGE HEADER -->
            <div class="heading">
                <h1>App Description</h1>
            </div>
            <div class="container">
                <div class="content" style="--i: 8;">
                    <!-- ABOUT PAGE SUB-HEADER -->
                    <div class="sub-header">
                        <h2 class="text-header">Welcome to Techtilanotes!</h2>
                    </div>
                    <!-- ABOUT PAGE DESCRIPTION -->
                    <p style="--i: 1;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; A unique place for studying! It has never been more fun to keep track of your "what to do" list than with this interesting rooster-inspired notepad. 
                        Experience creativity and productivity with its user-friendly features and a captivating interface as you take notes of your thoughts, ideas, and 
                        reminders—whether you're planning, creating, or brainstorming. Make learning enjoyable with Techtilanotes!
                    </p>
                    <button style="--i: 4;">Contact Us</button>
                </div>
            </div>
        </div>
    </section>

    <!-- MEMBERS PAGES -->
    <section id="Members" class="show-animate">
        <div class="section">
            <div class="title">
                <!-- MEMBER PAGE HEADER -->
                <h2 style="--i: 0;">Techtilanotes <br> Members</h2>
                <!-- ARROW BUTTONS -->
                <div class="arrows" style="--i: 16;">
                    <button><i class='bx bxs-left-arrow' id="left-arrow"></i></button>
                    <button><i class='bx bxs-right-arrow' id="right-arrow"></i></button>
                </div>
            </div>
            <!-- MEMBERS PICTURE CARD -->
            <div class="team-members">
                <div class="member-card" style="--i: 4;">
                    <img src="/assets/members/gamayo.jpg" alt="">
                    <div class="content">
                        <h2>Kelia Audrey</h2>
                        <h3>Project Manager</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 6;">
                    <img src="./assets/members/baladjay.jpg" alt="">
                    <div class="content">
                        <h2>Joyrel</h2>
                        <h3>Technical Designer</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 8;">
                    <img src="./assets/members/jacolbia.jpg" alt="">
                    <div class="content">
                        <h2>Jan Marc</h2>
                        <h3>Developer</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 10;">
                    <img src="./assets/members/gutierrez.jpeg" alt="">
                    <div class="content">
                        <h2>John Evans</h2>
                        <h3>Developer</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 12;">
                    <img src="./assets/members/kais.jpg" alt="">
                    <div class="content">
                        <h2>Alliah Michell</h2>
                        <h3>System Analyst</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 14;">
                    <img src="./assets/members/quintos.jpg" alt="">
                    <div class="content">
                        <h2>Aleah Mae</h2>
                        <h3>Business Analyst</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 16;">
                    <img src="./assets/members/jarabejo.jpeg" alt="">
                    <div class="content">
                        <h2>Jhannaris</h2>
                        <h3>System Analyst</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 18;">
                    <img src="./assets/members/ordillano.jpg" alt="">
                    <div class="content">
                        <h2>Jhuztin Claire</h2>
                        <h3>Technical Writer</h3>
                    </div>
                </div>
                <div class="member-card" style="--i: 20;">
                    <img src="./assets/members/osorio.jpg" alt="">
                    <div class="content">
                        <h2>Nicole Grace</h2>
                        <h3>Technical Writer</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SLICK CAROUSEL -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" ></script>
    <script>
        $('.team-members').slick({
            slidesToShow: 3,
            speed: 300,
            prevArrow: '#left-arrow',
            nextArrow: '#right-arrow',
            centerPadding: '60px',
            infinite: false,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: false,
                    }
                },
                {
                    breakpoint: 900,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        infinite: false,
                    }
                },
                {
                    breakpoint: 760,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: false,
                    }
                }
            ]
        });
    </script>
    <!-- SCRIPT FOR ANIMATIONS -->
    <script src="/script/animation.js"></script>
</body>
</html>