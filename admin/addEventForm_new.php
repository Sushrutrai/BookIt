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
    <form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
        <div>
            <h2>Event Details</h2>
            <label for="event-name">Event Name<input type="text" id="event-name" name="event_name" placeholder="Enter name of the event" required></label>
        <label for="event-date" >Date<input type="date" id="event-date" name="event_date" required></label>
        <label for="event-date">Category
            <select name="event_category" id="event_category" required>
        <?php 
            require __DIR__.'/../app/config/config.php';

            $statement=$connection->prepare('select *from event_categories order by category_name asc');
            $statement->execute();

            $result=$statement->get_result();
            while($row=$result->fetch_assoc()){
                echo"<option value='".$row['category_id']."'>".$row['category_name']."</option>";
            }
        ?>
            </select>
        </label>
        <label for="event-description">Event Description
            <input id="event-description" name="event_description" placeholder="Describe the event in detail" required>
        </label>
        <label for="event-location">Event Location<input type="text" id="event-location" name="event_location" placeholder="Enter event location" required></label>
        <label for="maps-link">Maps embed Link<input type="text" id="maps-link" name="maps_link" placeholder="https://maps.google.com/"></label>
        <label for="event_image">Event Image<input type="file" id="event-image" name="event_image" required></label>
        </div>
        <div>
            <h2>Ticket Tiers</h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 1.5rem;">Add ticket types for your event. Set custom names, prices, and quantities for each tier.</p>
            
            <div id="ticket-tiers-container">
                <!-- Ticket tiers will be added here dynamically -->
            </div>
            
            <button type="button" id="add-ticket-btn" class="btn btn-add-ticket" onclick="addTicketTier()">+ Add Ticket Tier</button>
        </div>
        <div class="buttons">
            <button type="submit" class="btn btn-primary" name="add" value="add">Publish Event</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
       
    </form>
    <?php include 'partial/adminFooter.php';?>

    <script>
        let ticketCount = 0;

        function addTicketTier() {
            ticketCount++;
            const container = document.getElementById('ticket-tiers-container');
            
            const tierHTML = `
                <div class="ticket-tier" id="ticket-tier-${ticketCount}">
                    <div class="ticket-tier-header">
                        <h3>Ticket Tier ${ticketCount}</h3>
                        <button type="button" class="btn-remove-ticket" onclick="removeTicketTier(${ticketCount})">Remove</button>
                    </div>
                    
                    <label for="ticket-name-${ticketCount}">Ticket Name
                        <input type="text" id="ticket-name-${ticketCount}" name="ticket_name[]" placeholder="e.g., VIP, Standard, General" required>
                    </label>
                    
                    <label for="ticket-price-${ticketCount}">Price (Rs.)
                        <input type="number" id="ticket-price-${ticketCount}" name="ticket_price[]" placeholder="1000" step="0.01" min="0" required>
                    </label>
                    
                    <label for="ticket-capacity-${ticketCount}">Capacity
                        <input type="number" id="ticket-capacity-${ticketCount}" name="ticket_capacity[]" placeholder="100" min="1" required>
                    </label>
                    
                    <label for="ticket-sale-start-${ticketCount}">Sale Start Date
                        <input type="datetime-local" id="ticket-sale-start-${ticketCount}" name="ticket_sale_start[]">
                    </label>
                    
                    <label for="ticket-sale-end-${ticketCount}">Sale End Date
                        <input type="datetime-local" id="ticket-sale-end-${ticketCount}" name="ticket_sale_end[]">
                    </label>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', tierHTML);
        }

        function removeTicketTier(id) {
            const tier = document.getElementById(`ticket-tier-${id}`);
            if (tier) {
                tier.remove();
            }
            if (document.querySelectorAll('.ticket-tier').length === 0) {
                document.getElementById('ticket-tiers-container').innerHTML = '';
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            if (document.querySelectorAll('.ticket-tier').length === 0) {
                addTicketTier();
            }
        });
    </script>
</body>

</html>
