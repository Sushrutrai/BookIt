<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin_form_style.css">
    <title>Add Event</title>
</head>

<body>
    <?php
include'partial/adminheader.php';?>
    <div class="head ">
        <a href="adminPanel.php">
        <p>Back to Dashboard</p>
    </a>
    <h1>Add your Event</h1>
    <p>Fill the fields to create event</p>
    </div>
    <?php
        $errors = isset($_SESSION['form_errors']) && is_array($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
        $old = isset($_SESSION['old_add_event']) && is_array($_SESSION['old_add_event']) ? $_SESSION['old_add_event'] : [];
        unset($_SESSION['form_errors'], $_SESSION['old_add_event']);

        if (!empty($errors)) {
            echo "<div class='admin-alert admin-alert--error'>
                    <h3 class='admin-alert__title'>Please fix the following issues:</h3>
                    <ul class='admin-alert__list'>";
            foreach ($errors as $err) {
                echo "<li class='admin-alert__item'>".htmlspecialchars((string)$err)."</li>";
            }
            echo "  </ul>
                </div>";
        }
    ?>
    <form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
        <div class="admin-card">
            <h2>Event Details</h2>
            <div class="admin-form-grid">
                <label for="event-name">Event Name
                    <input type="text" id="event-name" name="event_name" placeholder="Enter name of the event"
                        value="<?php echo htmlspecialchars((string)($old['event_name'] ?? ''), ENT_QUOTES); ?>">
                </label>
                <label for="event-date">Date
                    <input type="date" id="event-date" name="event_date"
                        min="<?php echo date('Y-m-d'); ?>"
                        value="<?php echo htmlspecialchars((string)($old['event_date'] ?? ''), ENT_QUOTES); ?>">
                </label>
                <label for="event_category">Category
                    <select name="event_category" id="event_category">
        <?php 
            require __DIR__.'/../app/config/config.php';

            $statement=$connection->prepare('select *from event_categories order by category_name asc');
            $statement->execute();

            $result=$statement->get_result();
            while($row=$result->fetch_assoc()){
                $selected = ((string)($old['event_category'] ?? '') === (string)$row['category_id']) ? 'selected' : '';
                echo"<option value='".$row['category_id']."' ".$selected.">".$row['category_name']."</option>";
            }
        ?>
                    </select>
                </label>
                <label for="event-description" class="admin-span-2">Event Description
                    <input id="event-description" name="event_description" placeholder="Describe the event in detail"
                        value="<?php echo htmlspecialchars((string)($old['event_description'] ?? ''), ENT_QUOTES); ?>">
                </label>
                <label for="event-location">Event Location
                    <input type="text" id="event-location" name="event_location" placeholder="Enter event location"
                        value="<?php echo htmlspecialchars((string)($old['event_location'] ?? ''), ENT_QUOTES); ?>">
                </label>
                <label for="maps-link">Maps embed Link
                    <input type="text" id="maps-link" name="maps_link" placeholder="https://maps.google.com/"
                        value="<?php echo htmlspecialchars((string)($old['maps_link'] ?? ''), ENT_QUOTES); ?>">
                </label>
                <label for="event_image" class="admin-span-2">Event Image
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
                        $ticketNamesOld = isset($old['ticket_name']) && is_array($old['ticket_name']) ? $old['ticket_name'] : [];
                        $pricesOld = isset($old['price']) && is_array($old['price']) ? $old['price'] : [];
                        $capacitiesOld = isset($old['capacity']) && is_array($old['capacity']) ? $old['capacity'] : [];
                        $saleStartsOld = isset($old['sale_start']) && is_array($old['sale_start']) ? $old['sale_start'] : [];
                        $saleEndsOld = isset($old['sale_end']) && is_array($old['sale_end']) ? $old['sale_end'] : [];
                        $statusesOld = isset($old['status']) && is_array($old['status']) ? $old['status'] : [];
                        $tierCount = max(
                            1,
                            count($ticketNamesOld),
                            count($pricesOld),
                            count($capacitiesOld),
                            count($saleStartsOld),
                            count($saleEndsOld),
                            count($statusesOld)
                        );

                        for ($i = 0; $i < $tierCount; $i++):
                            $ticketName = $ticketNamesOld[$i] ?? '';
                            $price = $pricesOld[$i] ?? '';
                            $capacity = $capacitiesOld[$i] ?? '';
                            $saleStart = $saleStartsOld[$i] ?? '';
                            $saleEnd = $saleEndsOld[$i] ?? '';
                            $status = $statusesOld[$i] ?? 'available';
                    ?>
                        <div class="ticket-tier-card" data-tier>
                            <div class="ticket-tier-card__header">
                                <h3 class="ticket-tier-card__title">Tier <?php echo $i + 1; ?></h3>
                                <?php if ($i > 0): ?>
                                    <button type="button" class="ticket-tier-card__remove" data-remove-tier>Remove</button>
                                <?php endif; ?>
                            </div>
                            <div class="ticket-tier-card__fields">
                                <label>
                                    Ticket Type
                                    <input type="text" name="ticket_name[]" placeholder="e.g., VIP" value="<?php echo htmlspecialchars((string)$ticketName, ENT_QUOTES); ?>">
                                </label>
                                <label>
                                    Price
                                    <input type="number" step="0.01" name="price[]" placeholder="e.g., 1999" value="<?php echo htmlspecialchars((string)$price, ENT_QUOTES); ?>">
                                </label>
                                <label>
                                    Capacity
                                    <input type="number" step="1" min="1" name="capacity[]" placeholder="e.g., 100" value="<?php echo htmlspecialchars((string)$capacity, ENT_QUOTES); ?>">
                                </label>
                                <label>
                                    Sale Start
                                    <input type="datetime-local" name="sale_start[]" value="<?php echo htmlspecialchars((string)$saleStart, ENT_QUOTES); ?>">
                                </label>
                                <label>
                                    Sale End
                                    <input type="datetime-local" name="sale_end[]" value="<?php echo htmlspecialchars((string)$saleEnd, ENT_QUOTES); ?>">
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
            <button type="submit" class="btn btn-primary" name="add" value="add">Publish Event</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
       
    </form>
    <?php include 'partial/adminFooter.php';?>
</body>

<template id="ticket-tier-template">
    <div class="ticket-tier-card" data-tier>
        <div class="ticket-tier-card__header">
            <h3 class="ticket-tier-card__title">Tier</h3>
            <button type="button" class="ticket-tier-card__remove" data-remove-tier>Remove</button>
        </div>
        <div class="ticket-tier-card__fields">
            <label>
                Ticket Type
                <input type="text" name="ticket_name[]" placeholder="e.g., VIP">
            </label>
            <label>
                Price
                <input type="number" step="0.01" name="price[]" placeholder="e.g., 1999">
            </label>
            <label>
                Capacity
                <input type="number" step="1" min="1" name="capacity[]" placeholder="e.g., 100">
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
            const tiers = grid ? grid.querySelectorAll('[data-tier]') : [];
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
                const tier = btn.closest('[data-tier]');
                if (!tier) return;
                tier.remove();
                renumber();
            });
        }

        // Initial numbering and ensure first tier has no remove button.
        if (grid) {
            const tiers = grid.querySelectorAll('[data-tier]');
            if (tiers[0]) {
                const removeBtn = tiers[0].querySelector('[data-remove-tier]');
                if (removeBtn) removeBtn.remove();
            }
            renumber();
        }
    })();
</script>

</html>