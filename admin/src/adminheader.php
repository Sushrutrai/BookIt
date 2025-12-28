<?php
    session_start();
    if(!isset($_SESSION['role'])==='admin'){
        header("Location:..\src\index.php");
        exit();
    }
    // admin-account:
// email:sushrutrai99@gmail.com,password:123abc

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/adminstyle.css">
    <title>Document</title>
</head>

<body>
    <header>
        <div class="header centered">
            <img class="logo " src="../icons/Bookit.svg" alt="BookIt logo" id="header_logo" />
            <nav>
                <ul id="navigation">
                    <li><a href="logout.php" onclick="return confirm('Are you sure you want to log out?')">Log out</a>
                    </li>
                    <li><a href=""><img src="..\icons\Profile.svg" alt="Profile" class="icon"></a></li>
                </ul>
            </nav>
        </div>
    </header>