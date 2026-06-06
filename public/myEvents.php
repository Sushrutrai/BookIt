<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events | BookIt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/grids.css">
    <link rel="stylesheet" href="../assets/css/myEvents.css">
</head>
<body>
     <?php 
     session_start();
     include "header.php"; ?>
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

        $bookmarkedActive = ($view === 'bookmarked') ? 'is-active' : '';
        $purchasedActive = ($view === 'purchased') ? 'is-active' : '';

        // Toggle UI (GET param based, no stateful JS needed).
        echo "<section class='centered main_content my-events-header'>
                <div class='my-events-toggle' role='tablist' aria-label='My events view'>
                    <a class='my-events-toggle__tab ".$bookmarkedActive."' href='myEvents.php?view=bookmarked' role='tab' aria-selected='".($view === 'bookmarked' ? "true" : "false")."'>
                        Bookmarked
                    </a>
                    <a class='my-events-toggle__tab ".$purchasedActive."' href='myEvents.php?view=purchased' role='tab' aria-selected='".($view === 'purchased' ? "true" : "false")."'>
                        Purchased
                    </a>
                </div>
                <h1 class='my-events-title'>MY EVENTS</h1>
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
                    b.booking_id,
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
                ORDER BY b.created_at DESC, e.event_date DESC
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
                // Group by booking (not just event) so multiple bookings for same event display correctly.
                $bookingsById = [];
                while ($row = $purchasedResult->fetch_assoc()) {
                    $bookingId = (int)$row['booking_id'];
                    if (!isset($bookingsById[$bookingId])) {
                        $bookingsById[$bookingId] = [
                            'booking_id' => $bookingId,
                            'booking_reference' => $row['booking_reference'],
                            'eid' => (int)$row['eid'],
                            'event_name' => $row['event_name'],
                            'event_date' => $row['event_date'],
                            'event_location' => $row['event_location'],
                            'event_image_path' => $row['event_image_path'],
                            'ticket_lines' => [],
                            'total_amount' => 0
                        ];
                    }

                    $qty = (int)$row['quantity'];
                    $priceAtPurchase = (float)$row['price_at_purchase'];
                    $subtotal = $qty * $priceAtPurchase;

                    $bookingsById[$bookingId]['ticket_lines'][] = [
                        'ticket_name' => $row['ticket_name'],
                        'quantity' => $qty,
                        'subtotal' => $subtotal
                    ];
                    $bookingsById[$bookingId]['total_amount'] += $subtotal;
                }

                echo "<section class='centered main_content'>
                        <div class='grid my-events-grid'>";

                foreach ($bookingsById as $event) {
                    echo "<article class='grid-item '>
                        <div class='banner_img_container'>
                            <img class='banner_img' src='../uploads/".htmlspecialchars($event['event_image_path'])."' alt='".htmlspecialchars($event['event_name'])."'>
                        </div>
                        <h3 class='event-title' onclick=\"location.href='event_card.php?eid=".$event['eid']."'\">".htmlspecialchars($event['event_name'])."</h3>
                        <ul class='event-info'>
                            <li><img class='icon' src='../assets/icons/calender.svg' alt='Date'><span>".date('d M ,Y', strtotime($event['event_date']))."</span></li>
                            <li><img class='icon' src='../assets/icons/time.svg' alt='Location'><span>".htmlspecialchars($event['event_location'])."</span></li>
                        </ul>
                        <div class='my-events-purchased-meta'>
                            <p class='my-events-purchased-meta__title'>Tickets</p>
                            <ul class='my-events-purchased-meta__list'>";

                    foreach ($event['ticket_lines'] as $line) {
                        echo "<li class='my-events-purchased-meta__item'>
                                ".htmlspecialchars($line['ticket_name']).": ".htmlspecialchars((string)$line['quantity'])."
                             </li>";
                    }

                    $bookingRef = htmlspecialchars($event['booking_reference']);
                    $total = number_format((float)$event['total_amount'], 2);

                    echo "        </ul>
                        <p class='my-events-purchased-meta__summary'>
                            Booking: <span class='my-events-purchased-meta__strong'>".$bookingRef."</span> | Total: Rs.".$total."
                        </p>
                       <form method='post'>
                         <button class='buy_button my-events-purchased-meta__cta' type='button' onclick=\"location.href='event_card.php?eid=".$event['eid']."'\">View Event</button>
                        <button class='buy_button my-events-purchased-meta__cta' type='button' onclick=\"location.href='view_tickets.php?bid=".$event['booking_id']."'\">View Tickets</button>
                       </form>
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
