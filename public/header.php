<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>BookIt</title>
</head>

<body>
    <header>
        <div class="header centered">
            <a href="index.php" id="logo_link"><img class="logo" src="../assets/icons/inverted/BookIt0.svg"
                    alt="BookIt logo" id="header_logo" /></a>
            <nav>
                <ul class="navigation">
                    <?php
                    session_start();
                    if (isset($_SESSION['role']) && $_SESSION['role']==='admin') {
                        echo "<li><a href=\"../admin/adminPanel.php\">Admin Dash</a></li>";
                    }
                    ?>
                    <li><a href="about_us.php">About us</a></li>
                    <li><a href="">Explore</a></li>
                    <li><a href="">My Events</a></li>
                    <?php

                    if (empty($_SESSION['name'])) {
                        echo "<li><a href=\"Form.php\">Sign up</a></li>";
                    } else {
                        echo "<li><a href=\"../app/auth/logout.php\" onclick=\"return confirm('Are you sure you want to log out?')\">Log out</a></li>";
                    }
                    
                    ?>
                    <!-- <li><a href=""><img src="../assets/icons/inverted/Profile0.svg" alt="Profile" class="icon"></a></li> -->
                </ul>
            </nav>
            <span class="svg">
                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.5 9H31.5V12H4.5V9ZM4.5 16.5H31.5V19.5H4.5V16.5ZM4.5 24H31.5V27H4.5V24Z" fill="white" />
                </svg>
            </span>
        </div>
    </header>
     <script src="../assets/js/dynamic_nav.js"></script>
</body>
</html>