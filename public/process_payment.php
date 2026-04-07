<?php
require __DIR__ . '/../app/bootstrap.php';

if (!isset($_SESSION['id'])) {
    header("Location:Form.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:index.php");
    exit();
}

$userId = (int)$_SESSION['id'];
$userName = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'User';

$eid = isset($_POST['eid']) ? (int)$_POST['eid'] : 0;
if ($eid <= 0) {
    die("Invalid event.");
}

// Normalize quantities: quantity[ticket_id] => qty
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
    die("Please select at least one ticket quantity.");
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

// Validate each selected ticket type (availability and capacity)
$ticketInfo = [];
$totalAmount = 0.0;

$connection->begin_transaction();
try {
    // Lock selected rows for capacity checks
    $ticketSelectStmt = $connection->prepare("SELECT * FROM ticket_type WHERE ticket_id = ? AND eid = ? FOR UPDATE");

    foreach ($selected as $ticketId => $qty) {
        $ticketSelectStmt->bind_param('ii', $ticketId, $eid);
        $ticketSelectStmt->execute();
        $ticketRes = $ticketSelectStmt->get_result();
        $t = $ticketRes->fetch_assoc();

        if (!$t) {
            throw new Exception("Invalid ticket selection.");
        }

        if ((string)$t['status'] !== 'available') {
            throw new Exception("Ticket type '".(string)$t['ticket_name']."' is not available.");
        }

        $capacity = (int)$t['capacity'];
        $sold = (int)($t['sold_count'] ?? 0);
        $remaining = $capacity - $sold;
        if ($remaining < $qty) {
            throw new Exception("Not enough availability for '".(string)$t['ticket_name']."'. Remaining: ".$remaining);
        }

        $price = (float)$t['price'];
        $lineTotal = $price * $qty;
        $totalAmount += $lineTotal;

        $ticketInfo[] = [
            'ticket_id' => (int)$t['ticket_id'],
            'ticket_name' => (string)$t['ticket_name'],
            'price' => $price,
            'quantity' => $qty,
            'line_total' => $lineTotal,
        ];
    }

    // Insert booking
    $bookingReference = 'BK' . date('YmdHis') . substr(bin2hex(random_bytes(4)), 0, 8);
    $paymentStatus = 'completed';
    $paymentMethod = 'cash';

    $bookingStmt = $connection->prepare("
        INSERT INTO bookings (user_id, booking_reference, total_amount, payment_status, payment_method)
        VALUES (?, ?, ?, ?, ?)
    ");
    $bookingStmt->bind_param('isdss', $userId, $bookingReference, $totalAmount, $paymentStatus, $paymentMethod);
    $bookingStmt->execute();
    if ($bookingStmt->error) {
        throw new Exception("Failed to create booking: ".$bookingStmt->error);
    }

    $bookingId = (int)$connection->insert_id;

    // Insert booking items + ticket instances and update sold_count
    $itemStmt = $connection->prepare("
        INSERT INTO booking_items (booking_id, ticket_type_id, quantity, price_at_purchase)
        VALUES (?, ?, ?, ?)
    ");

    $ticketInstanceStmt = $connection->prepare("
        INSERT INTO tickets (booking_id, ticket_type_id, ticket_hash)
        VALUES (?, ?, ?)
    ");

    $updateSoldStmt = $connection->prepare("
        UPDATE ticket_type
        SET sold_count = sold_count + ?
        WHERE ticket_id = ? AND eid = ?
    ");

    $issuedTicketHashes = [];
    foreach ($ticketInfo as $line) {
        $ticketTypeId = (int)$line['ticket_id'];
        $qty = (int)$line['quantity'];
        $priceAtPurchase = (float)$line['price'];

        $itemStmt->bind_param('iiid', $bookingId, $ticketTypeId, $qty, $priceAtPurchase);
        $itemStmt->execute();
        if ($itemStmt->error) {
            throw new Exception("Failed to create booking item: ".$itemStmt->error);
        }

        for ($i = 0; $i < $qty; $i++) {
            $hash = hash('sha256', $bookingId . '|' . $ticketTypeId . '|' . $userId . '|' . microtime(true) . '|' . random_bytes(8));
            $ticketInstanceStmt->bind_param('iis', $bookingId, $ticketTypeId, $hash);
            $ticketInstanceStmt->execute();
            if ($ticketInstanceStmt->error) {
                throw new Exception("Failed to issue ticket: ".$ticketInstanceStmt->error);
            }
            $issuedTicketHashes[] = $hash;
        }

        $updateSoldStmt->bind_param('iii', $qty, $ticketTypeId, $eid);
        $updateSoldStmt->execute();
        if ($updateSoldStmt->error) {
            throw new Exception("Failed to update inventory: ".$updateSoldStmt->error);
        }
    }

    $connection->commit();

} catch (Exception $e) {
    $connection->rollback();
    die("Payment failed: " . htmlspecialchars($e->getMessage()));
}

// Printable HTML ticket (user can print to PDF)
$eventName = (string)$event['event_name'];
$eventDate = (string)$event['event_date'];
$eventLocation = (string)$event['event_location'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; color: #111; }
        .wrap { max-width: 900px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 12px; padding: 18px; margin: 16px 0; }
        .row { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border-bottom: 1px solid #eee; padding: 10px 6px; text-align: left; }
        .hash { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 12px; word-break: break-all; }
        .actions { display:flex; gap: 12px; margin-top: 16px; }
        .btn { display:inline-block; padding: 10px 14px; border-radius: 10px; border: 1px solid #111; background: #111; color:#fff; text-decoration:none; cursor:pointer; }
        .btn.secondary { background: #fff; color:#111; }
        @media print {
            .actions { display:none; }
            body { padding: 0; }
            .card { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Ticket Confirmation</h1>
        <p class="muted">Payment status: <strong>Success</strong></p>

        <div class="card">
            <div class="row">
                <div>
                    <h2 style="margin:0 0 8px 0;"><?php echo htmlspecialchars($eventName); ?></h2>
                    <div class="muted">Date: <?php echo htmlspecialchars(date('D, d M, Y', strtotime($eventDate))); ?></div>
                    <div class="muted">Location: <?php echo htmlspecialchars($eventLocation); ?></div>
                </div>
                <div>
                    <div class="muted">Booked By</div>
                    <div><strong><?php echo htmlspecialchars($userName); ?></strong></div>
                    <div class="muted" style="margin-top:10px;">Booking Reference</div>
                    <div><strong><?php echo htmlspecialchars($bookingReference); ?></strong></div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Ticket Type</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ticketInfo as $line): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($line['ticket_name']); ?></td>
                            <td><?php echo (int)$line['quantity']; ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format((float)$line['price'], 2)); ?></td>
                            <td>Rs.<?php echo htmlspecialchars(number_format((float)$line['line_total'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Total Amount</strong></td>
                        <td><strong>Rs.<?php echo htmlspecialchars(number_format((float)$totalAmount, 2)); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 style="margin-top:0;">Issued Ticket IDs</h2>
            <p class="muted">Each ticket has a unique Ticket ID. Print this page (or Save as PDF) as your ticket.</p>
            <?php foreach ($issuedTicketHashes as $hash): ?>
                <div class="hash"><?php echo htmlspecialchars($hash); ?></div>
            <?php endforeach; ?>
        </div>

        <div class="actions">
            <button class="btn" type="button" onclick="window.print()">Print / Save as PDF</button>
            <a class="btn secondary" href="myEvents.php?view=purchased">Go to My Purchased</a>
        </div>
    </div>
</body>
</html>

