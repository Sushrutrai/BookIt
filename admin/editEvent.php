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
        echo "<div class='admin-alert admin-alert--error'>
                <h3 class='admin-alert__title'>Please fix the following issues:</h3>
                <ul class='admin-alert__list'>";
        foreach ($errors as $err) {
            echo "<li class='admin-alert__item'>" . htmlspecialchars((string)$err) . "</li>";
        }
        echo "  </ul>
            </div>";
    }
    ?>

    <form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
        <input type="hidden" name="eid" value="<?php echo (int)$event['eid']; ?>">
        <div class="admin-card">
            <h2>Event Details</h2>
            <div class="admin-form-grid">
                <label for="event-name">Event Name
                    <input type="text" id="event-name" name="event_name" value="<?php echo htmlspecialchars((string)$event['event_name'], ENT_QUOTES); ?>">
                </label>
                <label for="event-date">Date
                    <input type="date" id="event-date" name="event_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars((string)$event['event_date'], ENT_QUOTES); ?>">
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
                <label for="event-description" class="admin-span-2">Event Description
                    <input id="event-description" name="event_description" value="<?php echo htmlspecialchars((string)$event['event_description'], ENT_QUOTES); ?>">
                </label>
                <label for="event-location">Event Location
                    <input type="text" id="event-location" name="event_location" value="<?php echo htmlspecialchars((string)$event['event_location'], ENT_QUOTES); ?>">
                </label>
                <label for="maps-link">Maps embed Link
                    <input type="text" id="maps-link" name="maps_link" value="<?php echo htmlspecialchars((string)$event['event_maps_link'], ENT_QUOTES); ?>">
                </label>
                <label for="event_image" class="admin-span-2">Event Image (optional)
                    <input type="file" id="event-image" name="event_image">
                </label>
            </div>
        </div>

        <div class="admin-card">
            <h2>Ticket Tiers</h2>
            <div class="ticket-tiers-actions">
                <button type="button" class="btn btn-secondary" id="add-tier-btn">Add another tier</button>
            </div>
            <div class="ticket-tiers-grid" id="ticket-tiers-grid">
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
                    <div class="ticket-tier-card">
                        <div class="ticket-tier-card__header">
                            <h3 class="ticket-tier-card__title">Tier <?php echo $i + 1; ?></h3>
                            <?php if ($i > 0): ?>
                                <button type="button" class="ticket-tier-card__remove" data-remove-tier>Remove</button>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="ticket_id[]" value="<?php echo $t ? (int)$t['ticket_id'] : 0; ?>">
                        <div class="ticket-tier-card__fields">
                            <label>
                                Ticket Type
                                <input type="text" name="ticket_name[]" value="<?php echo htmlspecialchars((string)($t['ticket_name'] ?? ''), ENT_QUOTES); ?>">
                            </label>
                            <label>
                                Price
                                <input type="number" step="0.01" name="price[]" value="<?php echo htmlspecialchars((string)($t['price'] ?? ''), ENT_QUOTES); ?>">
                            </label>
                            <label>
                                Capacity
                                <input type="number" step="1" min="1" name="capacity[]" value="<?php echo htmlspecialchars((string)($t['capacity'] ?? ''), ENT_QUOTES); ?>">
                            </label>
                            <label>
                                Sale Start
                                <input type="datetime-local" name="sale_start[]" value="<?php echo $t && !empty($t['sale_start']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($t['sale_start'])), ENT_QUOTES) : ''; ?>">
                            </label>
                            <label>
                                Sale End
                                <input type="datetime-local" name="sale_end[]" value="<?php echo $t && !empty($t['sale_end']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($t['sale_end'])), ENT_QUOTES) : ''; ?>">
                            </label>
                            <label>
                                Status
                                <select name="status[]">
                                    <option value="available" <?php echo $status === 'available' ? 'selected' : ''; ?>>available</option>
                                    <option value="hidden" <?php echo $status === 'hidden' ? 'selected' : ''; ?>>hidden</option>
                                    <option value="sold_out" <?php echo $status === 'sold_out' ? 'selected' : ''; ?>>sold_out</option>
                                    <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>expired</option>
                                </select>
                            </label>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="buttons">
            <button type="submit" class="btn btn-primary" name="update_event" value="update_event">Update Event</button>
            <a class="btn btn-secondary" href="ViewEventList.php" style="text-decoration:none; display:inline-block; text-align:center;">Cancel</a>
        </div>
    </form>

    <?php include 'partial/adminFooter.php'; ?>
</body>

<template id="ticket-tier-template">
    <div class="ticket-tier-card ticket-tier-card--new" data-tier>
        <div class="ticket-tier-card__header">
            <h3 class="ticket-tier-card__title">Tier</h3>
            <button type="button" class="ticket-tier-card__remove" data-remove-tier>Remove</button>
        </div>
        <input type="hidden" name="ticket_id[]" value="0">
        <div class="ticket-tier-card__fields">
            <label>
                Ticket Type
                <input type="text" name="ticket_name[]" placeholder="e.g., General">
            </label>
            <label>
                Price
                <input type="number" step="0.01" name="price[]" placeholder="e.g., 999">
            </label>
            <label>
                Capacity
                <input type="number" step="1" min="1" name="capacity[]" placeholder="e.g., 250">
            </label>
            <label>
                Sale Start
                <input type="datetime-local" name="sale_start[]">
            </label>
            <label>
                Sale End
                <input type="datetime-local" name="sale_end[]">
            </label>
            <label>
                Status
                <select name="status[]">
                    <option value="available" selected>available</option>
                    <option value="hidden">hidden</option>
                    <option value="sold_out">sold_out</option>
                    <option value="expired">expired</option>
                </select>
            </label>
        </div>
    </div>
</template>

<script>
    (function () {
        const dateInput = document.getElementById('event-date');
        if (dateInput) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const min = `${yyyy}-${mm}-${dd}`;
            dateInput.min = min;
        }

        const grid = document.getElementById('ticket-tiers-grid');
        const addBtn = document.getElementById('add-tier-btn');
        const tpl = document.getElementById('ticket-tier-template');

        function renumber() {
            const tiers = grid ? grid.querySelectorAll('.ticket-tier-card') : [];
            tiers.forEach((tier, idx) => {
                const title = tier.querySelector('.ticket-tier-card__title');
                if (title) title.textContent = `Tier ${idx + 1}`;
            });
        }

        if (addBtn && grid && tpl) {
            addBtn.addEventListener('click', () => {
                const node = tpl.content.cloneNode(true);
                grid.appendChild(node);
                renumber();
            });

            grid.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-remove-tier]');
                if (!btn) return;
                const tier = btn.closest('.ticket-tier-card');
                if (!tier) return;
                tier.remove();
                renumber();
            });
        }

        // Remove button should not appear on the first existing tier.
        if (grid) {
            const first = grid.querySelector('.ticket-tier-card');
            if (first) {
                const removeBtn = first.querySelector('[data-remove-tier]');
                if (removeBtn) removeBtn.remove();
            }
            renumber();
        }
    })();
</script>

</html>

