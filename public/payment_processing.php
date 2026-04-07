<?php
session_start();
require __DIR__ . '/../app/bootstrap.php';

if (!isset($_SESSION['id'])) {
    header("Location:Form.php");
    exit();
}

// This page expects POST from event_card.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

$eid = isset($_POST['eid']) ? (int)$_POST['eid'] : 0;
if ($eid <= 0) {
    die("Invalid event.");
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location:event_card.php?eid=" . $eid);
    exit();
}

$quantities = isset($_POST['quantity']) && is_array($_POST['quantity']) ? $_POST['quantity'] : [];
$selected = [];
foreach ($quantities as $ticketIdRaw => $qtyRaw) {
    $ticketId = (int)$ticketIdRaw;
    $qty = (int)$qtyRaw;
    if ($ticketId > 0 && $qty > 0) {
        $selected[$ticketId] = $qty;
    }
}

if (empty($selected)) {
    // Send them back to event card with a simple message.
    header("Location:event_card.php?eid=".$eid);
    exit();
}

// Load event
$eventStmt = $connection->prepare("SELECT * FROM event_details WHERE eid = ?");
$eventStmt->bind_param('i', $eid);
$eventStmt->execute();
$eventRes = $eventStmt->get_result();
$event = $eventRes->fetch_assoc();
if (!$event) {
    die("Event not found.");
}

// Load ticket type details for selected tickets (no locking here; final validation happens in process_payment.php)
$ticketStmt = $connection->prepare("SELECT ticket_id, ticket_name, price, capacity, sold_count, status FROM ticket_type WHERE eid=? AND ticket_id=?");

$lines = [];
$total = 0.0;
foreach ($selected as $ticketId => $qty) {
    $ticketStmt->bind_param('ii', $eid, $ticketId);
    $ticketStmt->execute();
    $res = $ticketStmt->get_result();
    $t = $res->fetch_assoc();
    if (!$t) {
        continue;
    }

    $price = (float)$t['price'];
    $lineTotal = $price * $qty;
    $total += $lineTotal;

    $remaining = (int)$t['capacity'] - (int)($t['sold_count'] ?? 0);
    if ($remaining < 0) $remaining = 0;

    $lines[] = [
        'ticket_id' => (int)$t['ticket_id'],
        'ticket_name' => (string)$t['ticket_name'],
        'price' => $price,
        'quantity' => $qty,
        'remaining' => $remaining,
        'status' => (string)$t['status'],
        'line_total' => $lineTotal
    ];
}

if (empty($lines)) {
    header("Location:event_card.php?eid=".$eid);
    exit();
}

$userName = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/payment_processing_style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="pp-wrap">
        <div class="pp-card">
            <div class="pp-row">
                <div>
                    <h2><?php echo htmlspecialchars((string)$event['event_name']); ?></h2>
                    <div class="pp-muted"><?php echo htmlspecialchars(date('D, d M, Y', strtotime((string)$event['event_date']))); ?> • <?php echo htmlspecialchars((string)$event['event_location']); ?></div>
                </div>
                <div>
                    <div class="pp-muted">Purchasing as</div>
                    <div><strong><?php echo htmlspecialchars($userName); ?></strong></div>
                </div>
            </div>

            <table class="pp-table">
                <thead>
                    <tr>
                        <th>Ticket Type</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lines as $line): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($line['ticket_name']); ?></td>
                            <td><?php echo (int)$line['quantity']; ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format((float)$line['price'], 2)); ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format((float)$line['line_total'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>Rs.<?php echo htmlspecialchars(number_format((float)$total, 2)); ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <form method="post" action="process_payment.php">
                <input type="hidden" name="eid" value="<?php echo (int)$eid; ?>">
                <?php foreach ($selected as $ticketId => $qty): ?>
                    <input type="hidden" name="quantity[<?php echo (int)$ticketId; ?>]" value="<?php echo (int)$qty; ?>">
                <?php endforeach; ?>

                <div class="pp-actions">
                    <a class="pp-btn secondary pp-btn-link" href="event_card.php?eid=<?php echo (int)$eid; ?>">Back</a>
                    <button class="pp-btn" type="submit">Confirm & Pay (Dummy)</button>
                </div>
            </form>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>

