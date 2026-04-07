<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookmarked Events | BookIt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/grids.css">
    <link rel="stylesheet" href="../assets/css/myEvents.css">
</head>
<body>
     <?php include "header.php"; ?>
     <main>
    <?php
    require __DIR__.'/../app/bootstrap.php';
    if(!isset($_SESSION['id'])){
        echo "<section class='centered main_content my-events-empty'>
                <div class='empty-state login-prompt'>
                    <h2>You're not logged in</h2>
                    <p>Log in to view and manage your bookmarked events.</p>
                    <a href='Form.php' class='cta-button'>Log In Now</a>
                </div>
            </section>";
    }
    else{
        $view = isset($_GET['view']) ? strtolower(trim($_GET['view'])) : 'bookmarked';
        if (!in_array($view, ['bookmarked', 'purchased'], true)) {
            $view = 'bookmarked';
        }

        // Toggle UI (GET param based, no stateful JS needed).
        echo "<section class='centered main_content' style='padding-top: 1rem;'>
                <div style='display:flex; justify-content:center; gap: 1rem; margin: 0 0 1.5rem 0;'>
                    <a href='myEvents.php?view=bookmarked'
                       style='text-decoration:none; padding: 0.7rem 1.25rem; border-radius: 0.75rem; border: 2px solid #2FA84F; font-weight: 700; background: ".($view === 'bookmarked' ? '#2FA84F' : 'transparent')."; color: ".($view === 'bookmarked' ? '#fff' : '#1a1a1a').";'>
                        Bookmarked
                    </a>
                    <a href='myEvents.php?view=purchased'
                       style='text-decoration:none; padding: 0.7rem 1.25rem; border-radius: 0.75rem; border: 2px solid #2FA84F; font-weight: 700; background: ".($view === 'purchased' ? '#2FA84F' : 'transparent')."; color: ".($view === 'purchased' ? '#fff' : '#1a1a1a').";'>
                        Purchased
                    </a>
                </div>
                <h1 style='margin-bottom: 1.25rem;'>MY EVENTS</h1>
            </section>";

        if ($view === 'bookmarked') {
            $query=$connection->prepare("select * from event_details e join bookmarks b on e.eid=b.eid join users u on b.id=u.id where b.id=?;");
            $query->bind_param('i',$_SESSION['id']);
            $query->execute();
            $result=$query->get_result();

            if($result->num_rows === 0) {
                // NO BOOKMARKS - Show empty state
                echo "<section class='centered main_content my-events-empty'>
                        <div class='empty-state'>
                            <h2>No bookmarked events yet</h2>
                            <p>Start exploring and bookmark your favorite events to see them here. Your personalized collection awaits!</p>
                            <a href='index.php' class='cta-button'>Explore Events</a>
                        </div>
                    </section>";
            } else {
                // HAS BOOKMARKS - Show grid
                echo "<section class='centered main_content'>
                        <div class='grid my-events-grid'>";

                while($row=$result->fetch_assoc()){
                    echo "<article class='grid-item '>
                        <div class='banner_img_container'>
                            <img class='banner_img' src='../uploads/".$row["event_image_path"]."' alt='".htmlspecialchars($row["event_name"])."'>
                        </div>
                        <h3 class='event-title' onclick=\"location.href='event_card.php?eid=".$row['eid']."'\">".htmlspecialchars($row['event_name'])."</h3>
                        <ul class='event-info'>
                            <li><img class='icon' src='../assets/icons/calender.svg' alt='Date'><span>".date('d M ,Y', strtotime($row['event_date']))."</span></li>
                            <li><img class='icon' src='../assets/icons/time.svg' alt='Location'><span>".htmlspecialchars($row["event_location"])."</span></li>
                        </ul>
                        <button class='buy_button' type='button' onclick=\"location.href='event_card.php?eid=".$row['eid']."'\">Buy Ticket</button>
                    </article>";
                }

                echo "      </div>
                    </section>";
            }
        } else {
            // Purchased view
            $purchasedQuery = $connection->prepare("
                SELECT
                    e.eid,
                    e.event_name,
                    e.event_date,
                    e.event_location,
                    e.event_image_path,
                    b.booking_reference,
                    tt.ticket_name,
                    bi.quantity,
                    bi.price_at_purchase
                FROM bookings b
                INNER JOIN booking_items bi ON b.booking_id = bi.booking_id
                INNER JOIN ticket_type tt ON bi.ticket_type_id = tt.ticket_id
                INNER JOIN event_details e ON tt.eid = e.eid
                WHERE b.user_id = ? AND b.payment_status = 'completed'
                ORDER BY e.event_date DESC
            ");
            $purchasedQuery->bind_param('i', $_SESSION['id']);
            $purchasedQuery->execute();
            $purchasedResult = $purchasedQuery->get_result();

            if ($purchasedResult->num_rows === 0) {
                echo "<section class='centered main_content my-events-empty'>
                        <div class='empty-state'>
                            <h2>No purchased tickets yet</h2>
                            <p>Purchase tickets for events you like, and they'll appear here.</p>
                            <a href='index.php' class='cta-button'>Explore Events</a>
                        </div>
                    </section>";
            } else {
                $eventsById = [];
                while ($row = $purchasedResult->fetch_assoc()) {
                    $eid = (int)$row['eid'];
                    if (!isset($eventsById[$eid])) {
                        $eventsById[$eid] = [
                            'eid' => $eid,
                            'event_name' => $row['event_name'],
                            'event_date' => $row['event_date'],
                            'event_location' => $row['event_location'],
                            'event_image_path' => $row['event_image_path'],
                            'booking_reference' => $row['booking_reference'],
                            'ticket_lines' => [],
                            'total_amount' => 0
                        ];
                    }

                    $qty = (int)$row['quantity'];
                    $priceAtPurchase = (float)$row['price_at_purchase'];
                    $subtotal = $qty * $priceAtPurchase;

                    $eventsById[$eid]['ticket_lines'][] = [
                        'ticket_name' => $row['ticket_name'],
                        'quantity' => $qty,
                        'subtotal' => $subtotal
                    ];
                    $eventsById[$eid]['total_amount'] += $subtotal;
                }

                echo "<section class='centered main_content'>
                        <div class='grid my-events-grid'>";

                foreach ($eventsById as $event) {
                    echo "<article class='grid-item '>
                        <div class='banner_img_container'>
                            <img class='banner_img' src='../uploads/".htmlspecialchars($event['event_image_path'])."' alt='".htmlspecialchars($event['event_name'])."'>
                        </div>
                        <h3 class='event-title' onclick=\"location.href='event_card.php?eid=".$event['eid']."'\">".htmlspecialchars($event['event_name'])."</h3>
                        <ul class='event-info'>
                            <li><img class='icon' src='../assets/icons/calender.svg' alt='Date'><span>".date('d M ,Y', strtotime($event['event_date']))."</span></li>
                            <li><img class='icon' src='../assets/icons/time.svg' alt='Location'><span>".htmlspecialchars($event['event_location'])."</span></li>
                        </ul>
                        <div style='margin-top: 0.75rem;'>
                            <p style='margin: 0 0 0.25rem 0; font-weight: 700;'>Tickets</p>
                            <ul style='margin: 0; padding-left: 1.2rem; color:#666;'>";

                    foreach ($event['ticket_lines'] as $line) {
                        echo "<li style='margin:0.2rem 0;'>
                                ".htmlspecialchars($line['ticket_name']).": ".htmlspecialchars((string)$line['quantity'])."
                             </li>";
                    }

                    $bookingRef = htmlspecialchars($event['booking_reference']);
                    $total = number_format((float)$event['total_amount'], 2);

                    echo "        </ul>
                        <p style='margin: 0.5rem 0 0 0; color:#666; font-size: 0.95rem;'>
                            Booking: <span style='font-weight:700;'>".$bookingRef."</span> | Total: Rs.".$total."
                        </p>
                        <button class='buy_button' type='button' onclick=\"location.href='event_card.php?eid=".$event['eid']."'\" style='margin-top: 0.75rem;'>View Event</button>
                    </div>
                </article>";
                }

                echo "      </div>
                    </section>";
            }
        }
    }
    ?>
   </main>
     <?php include "footer.php"; ?>
</body>
</html>
