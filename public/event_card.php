<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/event_page_style.css">
    <?php
            require __DIR__.'/../app/bootstrap.php';
            if(isset($_GET['eid'])){
                $eid=$_GET['eid'];
                $query=$connection->prepare('select * from event_details where eid=?');
                $query->bind_param('i',$eid);
                $query->execute();
                $result=$query->get_result();
                $row=$result->fetch_assoc();
                }
    echo"
        <style>
            .cover{
                background-image:url('../uploads/".$row["event_image_path"]."');
                background-size: cover;
                background-position: center;
                width   : 100%;

            }
        </style>
    ";
    ?>
</head>
<body>
    <?php include 'header.php';?>
    <main class=" ">
        <div class="cover overlae"></div>
            <div class="flex_content centered">
                <div class="event_left">           
                    <img src='../uploads/<?php echo $row['event_image_path'];?>' alt=''>
                </div>
                <div class="event_right">
                    <h2><?php echo $row['event_name'];?></h2>
                    <div class="date_location_container">
                        <div class="date">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H17V3C17 2.73478 16.8946 2.48043 16.7071 2.29289C16.5196 2.10536 16.2652 2 16 2C15.7348 2 15.4804 2.10536 15.2929 2.29289C15.1054 2.48043 15 2.73478 15 3V4H9V3C9 2.73478 8.89464 2.48043 8.70711 2.29289C8.51957 2.10536 8.26522 2 8 2C7.73478 2 7.48043 2.10536 7.29289 2.29289C7.10536 2.48043 7 2.73478 7 3V4H5C4.20435 4 3.44129 4.31607 2.87868 4.87868C2.31607 5.44129 2 6.20435 2 7V19C2 19.7956 2.31607 20.5587 2.87868 21.1213C3.44129 21.6839 4.20435 22 5 22H19C19.7956 22 20.5587 21.6839 21.1213 21.1213C21.6839 20.5587 22 19.7956 22 19V7C22 6.20435 21.6839 5.44129 21.1213 4.87868C20.5587 4.31607 19.7956 4 19 4ZM20 19C20 19.2652 19.8946 19.5196 19.7071 19.7071C19.5196 19.8946 19.2652 20 19 20H5C4.73478 20 4.48043 19.8946 4.29289 19.7071C4.10536 19.5196 4 19.2652 4 19V12H20V19ZM20 10H4V7C4 6.73478 4.10536 6.48043 4.29289 6.29289C4.48043 6.10536 4.73478 6 5 6H7V7C7 7.26522 7.10536 7.51957 7.29289 7.70711C7.48043 7.89464 7.73478 8 8 8C8.26522 8 8.51957 7.89464 8.70711 7.70711C8.89464 7.51957 9 7.26522 9 7V6H15V7C15 7.26522 15.1054 7.51957 15.2929 7.70711C15.4804 7.89464 15.7348 8 16 8C16.2652 8 16.5196 7.89464 16.7071 7.70711C16.8946 7.51957 17 7.26522 17 7V6H19C19.2652 6 19.5196 6.10536 19.7071 6.29289C19.8946 6.48043 20 6.73478 20 7V10Z" fill="black"/>
                        </svg>
                        <p><?php echo date("D, d M, Y",strtotime($row["event_date"]));?></p>
                    </div>
                    <div class="location">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 11.5C11.337 11.5 10.7011 11.2366 10.2322 10.7678C9.76339 10.2989 9.5 9.66304 9.5 9C9.5 8.33696 9.76339 7.70107 10.2322 7.23223C10.7011 6.76339 11.337 6.5 12 6.5C12.663 6.5 13.2989 6.76339 13.7678 7.23223C14.2366 7.70107 14.5 8.33696 14.5 9C14.5 9.3283 14.4353 9.65339 14.3097 9.95671C14.1841 10.26 13.9999 10.5356 13.7678 10.7678C13.5356 10.9999 13.26 11.1841 12.9567 11.3097C12.6534 11.4353 12.3283 11.5 12 11.5ZM12 2C10.1435 2 8.36301 2.7375 7.05025 4.05025C5.7375 5.36301 5 7.14348 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 7.14348 18.2625 5.36301 16.9497 4.05025C15.637 2.7375 13.8565 2 12 2Z" fill="black"/>
                        </svg>
                        <p><?php echo $row['event_location'];?></p>
                    </div>
                    </div>
                    <p class="description"><?php echo $row['event_description'];?></p>
                    <div class="buy_ticket_container">
                        <h2>Select Tickets</h2>
                    <div class='ticket_type'>
                        <div class="ticket_price">
                            <p>Vip TIcket</p>
                            <p>Rs.1999</p>
                        </div>
                            <button class="ticket_button">ADD</button>
                        </div>
                    <div class='ticket_type'>
                            <div class="ticket_price">
                            <p>Vip TIcket</p>
                            <p>Rs.1999</p>
                        </div>
                            <button class="ticket_button">ADD</button>
                        </div>
                    <div class='ticket_type'>
                          <div class="ticket_price">
                        <p>Vip TIcket</p>
                        <p>Rs.1999</p>
                       </div>
                        <button class="ticket_button">ADD</button>
                    </div>
                    <button class="buy_button">Buy Ticket</button>
                </div>
            </div>
        </div>
           <div class="centered">
                <iframe src="<?php echo htmlspecialchars($row['event_maps_link'])?>" frameborder="2"></iframe>
           </div>
    </main>
    <?php include 'footer.php';?>
</body>

</html>