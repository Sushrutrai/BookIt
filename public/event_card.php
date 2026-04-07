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
            // Load ticket tiers for this event
            $tickets = [];
            if (!empty($row) && isset($row['eid'])) {
                $ticketQuery = $connection->prepare("SELECT * FROM ticket_type WHERE eid=? ORDER BY ticket_id ASC");
                $ticketQuery->bind_param('i', $row['eid']);
                $ticketQuery->execute();
                $ticketResult = $ticketQuery->get_result();
                while ($t = $ticketResult->fetch_assoc()) {
                    $tickets[] = $t;
                }
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
                    <div class="flex_content">
                        <h2><?php echo $row['event_name'];?></h2>
                        <?php
                            $activeClass = '';
                            $isDisabled = false;
                            $disableReason = '';
                            
                            // Only show bookmark button for logged-in regular users
                            if (!isset($_SESSION['role'])) {
                                $isDisabled = true;
                                $disableReason = 'Please log in to bookmark events';
                            } elseif ($_SESSION['role'] === 'admin') {
                                $isDisabled = true;
                                $disableReason = 'Admins cannot bookmark events';
                            } elseif (isset($_SESSION['id'])) {
                                $query=$connection->prepare("SELECT 1 FROM bookmarks WHERE eid=? AND id=?;");
                                $query->bind_param('ii',$row['eid'],$_SESSION['id']);
                                $query->execute();
                                $result=$query->get_result();
                                $activeClass = ($result->num_rows>0) ? 'is_active' : '';
                            }
                        ?>
                        <button class="favourite <?php echo $activeClass;?>" <?php echo $isDisabled ? 'disabled title="' . $disableReason . '"' : ''; ?>>
                            <svg  width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.27 2 8.5C2 5.41 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5C22 12.27 18.6 15.36 13.45 20.03L12 21.35Z" stroke="black" stroke-width="1.5"/>
                        </svg>
                        </button>
                    </div>
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
                        <form method="post" action="process_payment.php">
                            <input type="hidden" name="eid" value="<?php echo (int)$row['eid']; ?>">
                            <?php if (empty($tickets)): ?>
                                <p style="margin: 0.5rem 0; color:#666;">No ticket tiers available for this event yet.</p>
                            <?php else: ?>
                                <?php foreach ($tickets as $t):
                                    $remaining = (int)$t['capacity'] - (int)($t['sold_count'] ?? 0);
                                    if ($remaining < 0) $remaining = 0;
                                    $isAvailable = ((string)$t['status'] === 'available') && $remaining > 0;
                                ?>
                                    <div class='ticket_type'>
                                        <div class="ticket_price">
                                            <p><?php echo htmlspecialchars($t['ticket_name']); ?></p>
                                            <p>Rs.<?php echo htmlspecialchars(number_format((float)$t['price'], 2)); ?></p>
                                        </div>
                                        <div style="display:flex; align-items:center; gap: 0.5rem;">
                                            <input type="number"
                                                name="quantity[<?php echo (int)$t['ticket_id']; ?>]"
                                                min="0"
                                                max="<?php echo (int)$remaining; ?>"
                                                value="0"
                                                style="width: 90px; padding: 0.4rem; border: 1px solid rgba(0,0,0,0.2); border-radius: 0.5rem;"
                                                <?php echo $isAvailable ? '' : 'disabled'; ?>
                                            >
                                            <span style="color:#666; font-size: 0.9rem;">
                                                <?php echo $isAvailable ? ($remaining . " left") : "Unavailable"; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <button class="buy_button" type="submit">Purchase Ticket</button>
                            <?php endif; ?>
                        </form>
                </div>
            </div>
        </div>
           <div class="centered">
                <iframe src="<?php echo htmlspecialchars($row['event_maps_link'])?>" frameborder="2"></iframe>
           </div>
    </main>
  
    <?php include 'footer.php';?>
</body>
  <script>
    const favourite=document.querySelector('.favourite');
    favourite.addEventListener('click',async function(){
        // Check if button is disabled (user not logged in or admin)
        if (this.hasAttribute('disabled')) {
            const title = this.getAttribute('title');
            alert(title || 'This action is not available');
            return;
        }

        const isActive=this.classList.toggle('is_active');
        const userID=<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 0; ?>;
        const eid=<?php echo $row['eid'];?>;
        
        try{
            const response=await fetch('../app/actions/bookmarks.php',{
                method:'POST',
                headers:{
                    'Content-Type':'application/json'
                },
                body:JSON.stringify({
                    id:userID,
                    eid:eid,
                    liked:isActive
                })
            });

            const result=await response.json();

            if(result && result.success){
                alert(isActive?'Added to favourite':'Removed from favourite');
            }
            else{
                this.classList.toggle('is_active');
                alert('ERROR '+(result.message||'database error'));
            }
        }catch(error){
            this.classList.toggle('is_active');
            console.error('Network error',error)
        }
    })
    </script>
</html>