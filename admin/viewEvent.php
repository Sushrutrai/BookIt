<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin_form_style.css">
    <link rel="stylesheet" href="../../assets/css/viewEvent.css">
    <title>View Event</title>
</head>

<body>
    <?php
    include 'partial/adminheader.php';
    require __DIR__ . '/../app/config/config.php';

    $eid = isset($_GET['eid']) ? (int)$_GET['eid'] : 0;
    if ($eid <= 0) {
        header("Location:ViewEventList.php");
        exit();
    }

    $eventStmt = $connection->prepare("SELECT * FROM event_details WHERE eid = ?");
    $eventStmt->bind_param('i', $eid);
    $eventStmt->execute();
    $eventRes = $eventStmt->get_result();
    $event = $eventRes->fetch_assoc();

    if (!$event) {
        header("Location:ViewEventList.php");
        exit();
    }

    $ticketStmt = $connection->prepare("SELECT * FROM ticket_type WHERE eid = ? ORDER BY ticket_id ASC");
    $ticketStmt->bind_param('i', $eid);
    $ticketStmt->execute();
    $ticketRes = $ticketStmt->get_result();
    $tickets = [];
    while ($t = $ticketRes->fetch_assoc()) {
        $tickets[] = $t;
    }

    // Calculate totals
    $total_sold = 0;
    $total_capacity = 0;
    foreach ($tickets as $t) {
        $total_sold += (int)$t['sold_count'];
        $total_capacity += (int)$t['capacity'];
    }
    $available = $total_capacity - $total_sold;

    $total_revenue_query=$connection->prepare("SELECT
                        e.eid,
                        e.event_name,
                        SUM(bi.quantity * bi.price_at_purchase) AS total_revenue
                        FROM bookings b
                        JOIN booking_items bi ON b.booking_id = bi.booking_id
                        JOIN ticket_type tt ON bi.ticket_type_id = tt.ticket_id
                        JOIN event_details e ON tt.eid = e.eid
                        WHERE b.payment_status = 'completed' and e.eid= ?
                        GROUP BY e.eid, e.event_name;");
                        $total_revenue_query->bind_param("i",$eid);
                        $total_revenue_query->execute();
                        $res=$total_revenue_query->get_result();
                        $total_revenue=$res->fetch_assoc();
                            ?>

    <div class="head ">
        <a href="ViewEventList.php">
            <p>Back to Event List</p>
        </a>
        <h1>View Event Details</h1>
    </div>

    <div class="form-container centered">
        <div class="form-section">
            <h2>Event Information</h2>
            <div class="form-group">
                <label>Event Name:</label>
                <p><?php echo htmlspecialchars($event['event_name']); ?></p>
            </div>
            <div class="form-group">
                <label>Event Date:</label>
                <p><?php echo htmlspecialchars($event['event_date']); ?></p>
            </div>
            <div class="form-group">
                <label>Event Location:</label>
                <p><?php echo htmlspecialchars($event['event_location']); ?></p>
            </div>
            <div class="form-group">
                <label>Event Description:</label>
                <p><?php echo htmlspecialchars($event['event_description']); ?></p>
            </div>
            <div class="form-group">
                <label>Event Status:</label>
                <p><?php echo htmlspecialchars($event['event_status']); ?></p>
            </div>
            <div class="form-group">
                <label>Event Image:</label>
                <img src="../../uploads/<?php echo htmlspecialchars($event['event_image_path']); ?>" alt="Event Image" style="max-width: 200px;">
            </div>
        </div>

        <div class="form-section">
            <h2>Ticket Information</h2>
            <div class="form-group">
                <label>Total Tickets Sold:</label>
                <p><?php echo $total_sold; ?></p>
            </div>
            <div class="form-group">
                <label>Total Capacity:</label>
                <p><?php echo $total_capacity; ?></p>
            </div>
            <div class="form-group">
                <label>Available Tickets:</label>
                <p><?php echo $available; ?></p>
            </div>
            <div class="form-group">
                <label>Total Revenue:</label>
                <p>Rs.<?php echo $total_revenue["total_revenue"]; ?></p>
            </div>

            <h3>Ticket Types</h3>
       <div class="table-wrapper">
         <table class="table">
                <thead>
                    <tr>
                        <th>Ticket Name</th>
                        <th>Price</th>
                        <th>Capacity</th>
                        <th>Sold Count</th>
                        <th>Available</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['ticket_name']); ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format($t['price'], 2)); ?></td>
                            <td><?php echo (int)$t['capacity']; ?></td>
                            <td><?php echo (int)$t['sold_count']; ?></td>
                            <td><?php echo (int)$t['capacity'] - (int)$t['sold_count']; ?></td>
                            <td><?php echo htmlspecialchars($t['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
       </div>
        </div>
    </div>

    <?php include 'partial/adminFooter.php';?>
</body>
</html>