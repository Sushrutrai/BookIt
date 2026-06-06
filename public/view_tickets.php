<?php
session_start();
require __DIR__ . '/../app/bootstrap.php';

if (!isset($_SESSION['id'])) {
    header('Location: Form.php');
    exit;
}

$booking_id = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;

if (!$booking_id) {
    die('Invalid booking ID');
}

// Verify ownership: user can only view their own tickets
$ownershipStmt = $connection->prepare('SELECT b.booking_id FROM bookings b WHERE b.booking_id = ? AND b.user_id = ?');
$ownershipStmt->bind_param('ii', $booking_id, $_SESSION['id']);
$ownershipStmt->execute();
if ($ownershipStmt->get_result()->num_rows === 0) {
    die('Unauthorized: This booking does not belong to you');
}

// Fetch booking details with event info
$bookingStmt = $connection->prepare('
    SELECT b.booking_id, b.booking_reference, b.total_amount, b.created_at, b.payment_status,
           e.event_name, e.event_date, e.event_location,
           u.name
    FROM bookings b
    INNER JOIN booking_items bi ON b.booking_id = bi.booking_id
    INNER JOIN ticket_type tt ON bi.ticket_type_id = tt.ticket_id
    INNER JOIN event_details e ON tt.eid = e.eid
    INNER JOIN users u ON b.user_id = u.id
    WHERE b.booking_id = ?
    LIMIT 1
');
$bookingStmt->bind_param('i', $booking_id);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();

if ($bookingResult->num_rows === 0) {
    die('Booking not found');
}

$booking = $bookingResult->fetch_assoc();
$userName = isset($booking['name']) ? (string)$booking['name'] : 'User';

// Fetch all booking items for the summary table
$bookingItemsStmt = $connection->prepare('
    SELECT bi.item_id, bi.quantity, bi.price_at_purchase, tt.ticket_name
    FROM booking_items bi
    INNER JOIN ticket_type tt ON bi.ticket_type_id = tt.ticket_id
    WHERE bi.booking_id = ?
');
$bookingItemsStmt->bind_param('i', $booking_id);
$bookingItemsStmt->execute();
$bookingItemsResult = $bookingItemsStmt->get_result();
$bookingItems = [];
while ($row = $bookingItemsResult->fetch_assoc()) {
    $bookingItems[] = $row;
}

// Fetch all tickets for this booking
$ticketsStmt = $connection->prepare('
    SELECT t.ticket_instance_id, t.ticket_hash, tt.ticket_name, t.ticket_type_id
    FROM tickets t
    INNER JOIN ticket_type tt ON t.ticket_type_id = tt.ticket_id
    WHERE t.booking_id = ?
    ORDER BY t.ticket_instance_id ASC
');
$ticketsStmt->bind_param('i', $booking_id);
$ticketsStmt->execute();
$ticketsResult = $ticketsStmt->get_result();
$tickets = [];
while ($row = $ticketsResult->fetch_assoc()) {
    $tickets[] = $row;
}

$eventName = (string)$booking['event_name'];
$eventDate = (string)$booking['event_date'];
$eventLocation = (string)$booking['event_location'];
$bookingReference = (string)$booking['booking_reference'];
$totalAmount = (float)$booking['total_amount'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets | BookIt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/confirmation_style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="conf-wrap">
        <h1>Your Tickets</h1>
        <p class="conf-muted">Booking status: <strong style="color: #22C55E;">Confirmed</strong></p>

        <div class="conf-card">
            <div class="conf-row">
                <div>
                    <h2><?php echo htmlspecialchars($eventName); ?></h2>
                    <div class="conf-muted">Date: <?php echo htmlspecialchars(date('D, d M, Y', strtotime($eventDate))); ?></div>
                    <div class="conf-muted">Location: <?php echo htmlspecialchars($eventLocation); ?></div>
                </div>
                <div>
                    <div class="conf-muted">Booked By</div>
                    <div><strong><?php echo htmlspecialchars($userName); ?></strong></div>
                    <div class="conf-muted" style="margin-top:10px;">Booking Reference</div>
                    <div><strong><?php echo htmlspecialchars($bookingReference); ?></strong></div>
                </div>
            </div>

            <table class="conf-table">
                <thead>
                    <tr>
                        <th>Ticket Type</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookingItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['ticket_name']); ?></td>
                            <td><?php echo (int)$item['quantity']; ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format((float)$item['price_at_purchase'], 2)); ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format((float)$item['quantity'] * (float)$item['price_at_purchase'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3"><strong>Total Amount</strong></td>
                        <td><strong>Rs.<?php echo htmlspecialchars(number_format((float)$totalAmount, 2)); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="conf-card">
            <h2>Issued Ticket IDs</h2>
            <p class="conf-muted">Each ticket has a unique Ticket ID. Print this page (or Save as PDF) as your ticket(s).</p>
            <?php foreach ($tickets as $ticket): ?>
                <div class="conf-hash"><?php echo htmlspecialchars($ticket['ticket_hash']); ?></div>
            <?php endforeach; ?>
        </div>

        <div class="conf-actions">
            <button class="conf-btn" type="button" onclick="window.print()">Print / Save as PDF</button>
            <a class="conf-btn conf-btn-secondary" href="myEvents.php?view=purchased">Go to My Purchased</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
