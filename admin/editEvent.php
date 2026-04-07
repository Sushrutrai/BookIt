<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin_form_style.css">
    <title>Edit Event</title>
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

    $errors = isset($_SESSION['form_errors']) && is_array($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
    unset($_SESSION['form_errors']);

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
    ?>

    <div class="head ">
        <a href="ViewEventList.php">
            <p>Back to Event List</p>
        </a>
        <h1>Edit Event</h1>
        <p>Update event and ticket tier details</p>
    </div>

    <?php
    if (!empty($errors)) {
        echo "<div style='max-width: 900px; margin: 1rem auto; padding: 1rem; border: 2px solid #e11d48; border-radius: 0.75rem; background: #fff5f7;'>
                <h3 style='margin:0 0 0.75rem 0; color:#e11d48;'>Please fix the following issues:</h3>
                <ul style='margin:0; padding-left: 1.2rem;'>";
        foreach ($errors as $err) {
            echo "<li style='margin:0.25rem 0;'>" . htmlspecialchars((string)$err) . "</li>";
        }
        echo "  </ul>
            </div>";
    }
    ?>

    <form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
        <input type="hidden" name="eid" value="<?php echo (int)$event['eid']; ?>">
        <div>
            <h2>Event Details</h2>
            <label for="event-name">Event Name
                <input type="text" id="event-name" name="event_name" value="<?php echo htmlspecialchars((string)$event['event_name'], ENT_QUOTES); ?>">
            </label>
            <label for="event-date">Date
                <input type="date" id="event-date" name="event_date" value="<?php echo htmlspecialchars((string)$event['event_date'], ENT_QUOTES); ?>">
            </label>
            <label for="event_category">Category
                <select name="event_category" id="event_category">
                    <?php
                    $catStmt = $connection->prepare('select *from event_categories order by category_name asc');
                    $catStmt->execute();
                    $catRes = $catStmt->get_result();
                    while ($row = $catRes->fetch_assoc()) {
                        $selected = ((string)$event['category_id'] === (string)$row['category_id']) ? 'selected' : '';
                        echo "<option value='" . $row['category_id'] . "' " . $selected . ">" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                    ?>
                </select>
            </label>
            <label for="event-description">Event Description
                <input id="event-description" name="event_description" value="<?php echo htmlspecialchars((string)$event['event_description'], ENT_QUOTES); ?>">
            </label>
            <label for="event-location">Event Location
                <input type="text" id="event-location" name="event_location" value="<?php echo htmlspecialchars((string)$event['event_location'], ENT_QUOTES); ?>">
            </label>
            <label for="maps-link">Maps embed Link
                <input type="text" id="maps-link" name="maps_link" value="<?php echo htmlspecialchars((string)$event['event_maps_link'], ENT_QUOTES); ?>">
            </label>
            <label for="event_image">Event Image (optional)
                <input type="file" id="event-image" name="event_image">
            </label>
        </div>

        <div>
            <h2>Ticket Tiers</h2>
            <div style="display:grid; gap: 1.25rem; max-width: 900px; margin: 0 auto;">
                <?php
                $allowedStatuses = ['available', 'hidden', 'sold_out', 'expired'];

                $rowsToRender = max(count($tickets), 1);
                for ($i = 0; $i < $rowsToRender; $i++):
                    $t = $tickets[$i] ?? null;
                    $status = $t ? (string)$t['status'] : 'available';
                    if (!in_array($status, $allowedStatuses, true)) {
                        $status = 'available';
                    }
                ?>
                    <div style="padding: 0.75rem; border: 1px solid rgba(0,0,0,0.15); border-radius: 0.75rem;">
                        <h3 style="margin: 0 0 0.75rem 0;">Tier <?php echo $i + 1; ?></h3>
                        <input type="hidden" name="ticket_id[]" value="<?php echo $t ? (int)$t['ticket_id'] : 0; ?>">
                        <div style="display:grid; gap: 0.5rem;">
                            <label for="ticket-name-<?php echo $i; ?>">
                                Ticket Type
                                <input type="text" id="ticket-name-<?php echo $i; ?>" name="ticket_name[]" value="<?php echo htmlspecialchars((string)($t['ticket_name'] ?? ''), ENT_QUOTES); ?>">
                            </label>
                            <label for="ticket-price-<?php echo $i; ?>">
                                Price
                                <input type="number" step="0.01" id="ticket-price-<?php echo $i; ?>" name="price[]" value="<?php echo htmlspecialchars((string)($t['price'] ?? ''), ENT_QUOTES); ?>">
                            </label>
                            <label for="ticket-capacity-<?php echo $i; ?>">
                                Capacity
                                <input type="number" step="1" min="1" id="ticket-capacity-<?php echo $i; ?>" name="capacity[]" value="<?php echo htmlspecialchars((string)($t['capacity'] ?? ''), ENT_QUOTES); ?>">
                            </label>
                            <label for="sale-start-<?php echo $i; ?>">
                                Sale Start
                                <input type="datetime-local" id="sale-start-<?php echo $i; ?>" name="sale_start[]" value="<?php echo $t && !empty($t['sale_start']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($t['sale_start'])), ENT_QUOTES) : ''; ?>">
                            </label>
                            <label for="sale-end-<?php echo $i; ?>">
                                Sale End
                                <input type="datetime-local" id="sale-end-<?php echo $i; ?>" name="sale_end[]" value="<?php echo $t && !empty($t['sale_end']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($t['sale_end'])), ENT_QUOTES) : ''; ?>">
                            </label>
                            <label for="ticket-status-<?php echo $i; ?>">
                                Status
                                <select id="ticket-status-<?php echo $i; ?>" name="status[]">
                                    <option value="available" <?php echo $status === 'available' ? 'selected' : ''; ?>>available</option>
                                    <option value="hidden" <?php echo $status === 'hidden' ? 'selected' : ''; ?>>hidden</option>
                                    <option value="sold_out" <?php echo $status === 'sold_out' ? 'selected' : ''; ?>>sold_out</option>
                                    <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>expired</option>
                                </select>
                            </label>
                        </div>
                    </div>
                <?php endfor; ?>

                <!-- Optional extra blank row for adding a new tier -->
                <div style="padding: 0.75rem; border: 1px dashed rgba(0,0,0,0.25); border-radius: 0.75rem;">
                    <h3 style="margin: 0 0 0.75rem 0;">Add New Tier</h3>
                    <input type="hidden" name="ticket_id[]" value="0">
                    <div style="display:grid; gap: 0.5rem;">
                        <label for="ticket-name-new">
                            Ticket Type
                            <input type="text" id="ticket-name-new" name="ticket_name[]" placeholder="e.g., General">
                        </label>
                        <label for="ticket-price-new">
                            Price
                            <input type="number" step="0.01" id="ticket-price-new" name="price[]" placeholder="e.g., 999">
                        </label>
                        <label for="ticket-capacity-new">
                            Capacity
                            <input type="number" step="1" min="1" id="ticket-capacity-new" name="capacity[]" placeholder="e.g., 250">
                        </label>
                        <label for="sale-start-new">
                            Sale Start
                            <input type="datetime-local" id="sale-start-new" name="sale_start[]">
                        </label>
                        <label for="sale-end-new">
                            Sale End
                            <input type="datetime-local" id="sale-end-new" name="sale_end[]">
                        </label>
                        <label for="ticket-status-new">
                            Status
                            <select id="ticket-status-new" name="status[]">
                                <option value="available" selected>available</option>
                                <option value="hidden">hidden</option>
                                <option value="sold_out">sold_out</option>
                                <option value="expired">expired</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="buttons">
            <button type="submit" class="btn btn-primary" name="update_event" value="update_event">Update Event</button>
            <a class="btn btn-secondary" href="ViewEventList.php" style="text-decoration:none; display:inline-block; text-align:center;">Cancel</a>
        </div>
    </form>

    <?php include 'partial/adminFooter.php'; ?>
</body>

</html>

