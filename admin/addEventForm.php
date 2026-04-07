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
            echo "<div style='max-width: 900px; margin: 1rem auto; padding: 1rem; border: 2px solid #e11d48; border-radius: 0.75rem; background: #fff5f7;'>
                    <h3 style='margin:0 0 0.75rem 0; color:#e11d48;'>Please fix the following issues:</h3>
                    <ul style='margin:0; padding-left: 1.2rem;'>";
            foreach ($errors as $err) {
                echo "<li style='margin:0.25rem 0;'>".htmlspecialchars((string)$err)."</li>";
            }
            echo "  </ul>
                </div>";
        }
    ?>
    <form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
        <div>
            <h2>Event Details</h2>
            <label for="event-name">Event Name
                <input type="text" id="event-name" name="event_name" placeholder="Enter name of the event"
                    value="<?php echo htmlspecialchars((string)($old['event_name'] ?? ''), ENT_QUOTES); ?>">
            </label>
        <label for="event-date">Date
                <input type="date" id="event-date" name="event_date"
                    value="<?php echo htmlspecialchars((string)($old['event_date'] ?? ''), ENT_QUOTES); ?>">
            </label>
        <label for="event-date">Category
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
        <label for="event-description">Event Description
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
        <label for="event_image">Event Image<input type="file" id="event-image" name="event_image"></label>
        </div>
         <div>
                <h2>Ticket Tiers</h2>
                <div style="display:grid; gap: 1.25rem; max-width: 900px; margin: 0 auto;">
                    <?php for ($i = 0; $i < 3; $i++): 
                        $ticketName = $old['ticket_name'][$i] ?? '';
                        $price = $old['price'][$i] ?? '';
                        $capacity = $old['capacity'][$i] ?? '';
                        $saleStart = $old['sale_start'][$i] ?? '';
                        $saleEnd = $old['sale_end'][$i] ?? '';
                        $status = $old['status'][$i] ?? 'available';
                    ?>
                        <div style="padding: 0.75rem; border: 1px solid rgba(0,0,0,0.15); border-radius: 0.75rem;">
                            <h3 style="margin: 0 0 0.75rem 0;">Tier <?php echo $i + 1; ?></h3>
                            <div style="display:grid; gap: 0.5rem;">
                                <label for="ticket-name-<?php echo $i; ?>">
                                    Ticket Type
                                    <input type="text" id="ticket-name-<?php echo $i; ?>" name="ticket_name[]" placeholder="e.g., VIP" value="<?php echo htmlspecialchars((string)$ticketName, ENT_QUOTES); ?>">
                                </label>
                                <label for="ticket-price-<?php echo $i; ?>">
                                    Price
                                    <input type="number" step="0.01" id="ticket-price-<?php echo $i; ?>" name="price[]" placeholder="e.g., 1999" value="<?php echo htmlspecialchars((string)$price, ENT_QUOTES); ?>">
                                </label>
                                <label for="ticket-capacity-<?php echo $i; ?>">
                                    Capacity
                                    <input type="number" step="1" min="1" id="ticket-capacity-<?php echo $i; ?>" name="capacity[]" placeholder="e.g., 100" value="<?php echo htmlspecialchars((string)$capacity, ENT_QUOTES); ?>">
                                </label>
                                <label for="sale-start-<?php echo $i; ?>">
                                    Sale Start
                                    <input type="datetime-local" id="sale-start-<?php echo $i; ?>" name="sale_start[]" value="<?php echo htmlspecialchars((string)$saleStart, ENT_QUOTES); ?>">
                                </label>
                                <label for="sale-end-<?php echo $i; ?>">
                                    Sale End
                                    <input type="datetime-local" id="sale-end-<?php echo $i; ?>" name="sale_end[]" value="<?php echo htmlspecialchars((string)$saleEnd, ENT_QUOTES); ?>">
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
                </div>
        </div>
        <div class="buttons">
            <button type="submit" class="btn btn-primary" name="add" value="add">Publish Event</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
       
    </form>
    <?php include 'partial/adminFooter.php';?>
</body>

</html>