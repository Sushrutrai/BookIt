<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/grids.css">

    <title>BookIt</title>
</head>

<body>
    <?php include "header.php"; ?> 
    <main>
        <section class="hero" id="hero">
            <div class="overlay" id="left"></div>
            <div class="overlay" id="right"></div>
        </section>
         <section class="centered main_content">
            <div class="grid">
                <!-- <article class="grid-item aspect">  
                    <img class="banner_img"src="../uploads/event_card/ab67616d0000b273254442c08e03a0be65dee761.jpg" alt="Indie Gateaway Event">
                    <h3>Indie Gateaway Ft.Shailu Rai, The Alley & Ketchup</h3>
                    <ul>
                        <li><img class="icon" src="../icons/calender.svg" alt="">Fri, 05 Dec</li>
                        <li><img class="icon" src="../icons/time.svg" alt="">Reggae Bar Thamel</li>
                    </ul>
                    <button type=""onclick="return alert('ticket puchased')">Buy Ticket</button>
                </article>
               -->
                <?php
                    require '..\backend\config.php';

                    $statement=$connection->prepare('select * from event_details order by event_date desc;');
                    $statement->execute();
                    $result=$statement->get_result();

                    while($row=$result->fetch_assoc()){
                        echo "<article class='grid-item aspect'>
                            <img class='banner_img' src='../uploads/".$row["event_image_path"]."' alt='".htmlspecialchars($row["event_name"])."'>
                            <h3>".htmlspecialchars($row['event_name'])."</h3>
                    <ul>
                        <li><img class='icon' src='../icons/calender.svg' > ".date('D ,d M', strtotime($row['event_date'])) ."</li>
                        <li><img class='icon' src='../icons/time.svg' >".htmlspecialchars($row["event_location"])."</li>
                    </ul>
                    <button type='button' onclick=\"return alert('ticket purchased')\">Buy Ticket</button>
                </article>
                        ";
                    }
                ?>
            </div>
        </section> 
        <div></div>
    </main>
    <?php include "footer.php"; ?>
    
    <script src="../js/home_interactivity.js"></script>
</body>
</html>