# Quick Start: Ticket Tier Implementation

## What Changed?

### Before:
- Form had empty placeholder for Ticket Tiers section
- No ability to create custom tickets
- Tickets were hardcoded in event_card.php
- Controller only handled event insertion

### After:
- Dynamic form to add multiple ticket tiers
- Each tier has: Name, Price, Capacity, Sale Start/End dates
- User can add/remove tiers with buttons
- Controller inserts all tickets linked to the event

---

## How to Use (Admin View)

### Step 1: Fill Event Details
```
Event Name: Tech Conference 2026
Date: 2026-05-15
Category: Conferences
Description: Annual tech conference...
Location: Convention Center, XYZ City
Image: [Select file]
```

### Step 2: Add Ticket Tiers
The form auto-generates 1 ticket tier. For each tier, fill:

**Tier 1 - VIP:**
```
Ticket Name: VIP Pass
Price: 5000
Capacity: 50
Sale Start: 2026-04-10 09:00
Sale End: 2026-05-14 23:59
```

**Tier 2 - Standard:**
```
Ticket Name: Standard Pass
Price: 2500
Capacity: 200
Sale Start: 2026-04-10 09:00
Sale End: 2026-05-14 23:59
```

### Step 3: Add More (Optional)
Click "+ Add Ticket Tier" button to add more tiers

### Step 4: Publish
Click "Publish Event" button

**Result:** Event created + 2 ticket tiers inserted into database

---

## Database Results

**After publishing above example:**

### event_details table:
```
eid  | event_name           | event_date | category_id | ...
1    | Tech Conference 2026 | 2026-05-15 | 3           | ...
```

### ticket_type table:
```
ticket_id | eid | ticket_name    | price | capacity | sold_cout | sale_start          | sale_end            | status
1         | 1   | VIP Pass       | 5000  | 50       | 0         | 2026-04-10 09:00:00 | 2026-05-14 23:59:00 | active
2         | 1   | Standard Pass  | 2500  | 200      | 0         | 2026-04-10 09:00:00 | 2026-05-14 23:59:00 | active
```

---

## File Locations

**Form File:**
- Old: `admin/addEventForm.php`
- New: `admin/addEventForm_new.php`
- Replace original with new version

**Controller File:**
- Old: `app/actions/controller.php`
- New: `app/actions/controller_new.php`
- Replace original with new version

---

## Code Examples

### HTML Form Section:
```html
<div id="ticket-tiers-container">
    <!-- Dynamically generated ticket tiers -->
</div>
<button type="button" onclick="addTicketTier()">+ Add Ticket Tier</button>
```

### Generated HTML for one tier:
```html
<div class="ticket-tier" id="ticket-tier-1">
    <h3>Ticket Tier 1</h3>
    <label>Ticket Name
        <input name="ticket_name[]" placeholder="e.g., VIP, Standard, General" />
    </label>
    <label>Price (Rs.)
        <input name="ticket_price[]" type="number" />
    </label>
    <label>Capacity
        <input name="ticket_capacity[]" type="number" />
    </label>
    <label>Sale Start Date
        <input name="ticket_sale_start[]" type="datetime-local" />
    </label>
    <label>Sale End Date
        <input name="ticket_sale_end[]" type="datetime-local" />
    </label>
    <button type="button" onclick="removeTicketTier(1)">Remove</button>
</div>
```

### PHP Processing:
```php
// Get event ID after insertion
$event_id = $connection->insert_id;

// Loop through ticket arrays
foreach($_POST['ticket_name'] as $i => $name) {
    // Insert ticket tier
    $price = $_POST['ticket_price'][$i];
    $capacity = $_POST['ticket_capacity'][$i];
    // ... execute insert
}
```

---

## Key Features

✅ **Dynamic Form** - Add/remove tiers without page reload  
✅ **Data Validation** - HTML5 validation on required fields  
✅ **Flexible Pricing** - Supports decimal prices (e.g., 1999.99)  
✅ **Optional Dates** - Sale start/end dates can be left empty  
✅ **Linked Records** - Tickets automatically linked to event via FK  
✅ **Error Handling** - User-friendly error messages  
✅ **Success Confirmation** - Shows count of ticket tiers created  

---

## Testing Scenarios

### Scenario 1: Single Tier Event
1. Add event with 1 ticket tier
2. Click "Publish Event"
3. Check: 1 record in ticket_type with eid=newly_created_event_id

### Scenario 2: Multi-Tier Event
1. Add event
2. Click "+ Add Ticket Tier" 3 times (total 4 tiers)
3. Fill all tier details
4. Publish
5. Check: 4 records in ticket_type, all linked to same eid

### Scenario 3: Remove Tiers
1. Add event, 5 tiers appear
2. Remove 2 tiers (keep 3)
3. Publish
4. Check: Only 3 records inserted

### Scenario 4: Test Validation
1. Leave a ticket name empty
2. Click Publish
3. Verify: Browser shows validation error (required field)

---

## Success Indicators

After implementation, verify:
1. ✅ Form shows ticket tier section with add/remove buttons
2. ✅ New ticket tiers appear when adding multiple
3. ✅ Event publishes successfully
4. ✅ Records appear in ticket_type table
5. ✅ Ticket records have correct eid (foreign key)
6. ✅ All fields (name, price, capacity, dates, status) populated correctly

