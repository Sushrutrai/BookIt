# Ticket Tier Management System - Implementation Guide

## Overview
This system allows event organizers to create custom ticket tiers when adding events. Each ticket tier can have its own name, price, capacity, and sale timeframe.

## Files Modified/Created

### 1. **addEventForm_new.php** (Enhanced Form)
**Location:** `admin/addEventForm_new.php`

#### Features:
- **Dynamic Ticket Tier Section**: Users can add/remove ticket tiers on-the-fly
- **Inline Styling**: Custom CSS for ticket tier UI components
- **Form Fields per Ticket Tier**:
  - Ticket Name (text input) - e.g., "VIP", "Standard", "General"
  - Price in Rs. (number input) - supports decimals
  - Capacity (number input) - total tickets available for this tier
  - Sale Start Date (datetime-local) - when tickets become available for sale
  - Sale End Date (datetime-local) - when ticket sales close

#### JavaScript Functions:
```javascript
addTicketTier()       // Adds a new ticket tier with unique IDs
removeTicketTier(id)  // Removes a specific ticket tier
```

#### Form Structure:
```html
<input name="ticket_name[]" />        <!-- Array of ticket names -->
<input name="ticket_price[]" />       <!-- Array of prices -->
<input name="ticket_capacity[]" />    <!-- Array of capacities -->
<input name="ticket_sale_start[]" />  <!-- Array of start dates -->
<input name="ticket_sale_end[]" />    <!-- Array of end dates -->
```

---

### 2. **controller_new.php** (Updated Backend)
**Location:** `app/actions/controller_new.php`

#### Key Changes in ADD ACTION:

**Process Flow:**
1. Insert event into `event_details` table (existing logic)
2. Get the inserted event's ID using `$connection->insert_id`
3. For each ticket tier provided:
   - Extract ticket data from POST arrays
   - Convert HTML5 datetime-local format to MySQL format
   - Insert into `ticket_type` table with the event ID (FK)
   - Set default values: `sold_cout = 0`, `status = 'active'`

**Code Snippet:**
```php
$event_id = $connection->insert_id;

$ticket_stmt = $connection->prepare(
    "INSERT INTO ticket_type (eid, ticket_name, price, capacity, sold_cout, sale_start, sale_end, status) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

for($i = 0; $i < count($ticket_names); $i++) {
    // Insert each ticket tier
}
```

**Data Type Handling:**
- `eid` (int) - Event ID (FK)
- `ticket_name` (string) - Custom ticket tier name
- `price` (decimal) - Ticket price, converted to float
- `capacity` (int) - Number of tickets available
- `sold_cout` (int) - Set to 0 on creation (tracks sales)
- `sale_start` (datetime) - Converted from HTML5 format
- `sale_end` (datetime) - Converted from HTML5 format
- `status` (string) - Set to 'active' on creation

**DateTime Conversion:**
```php
// Convert HTML5 datetime-local (2026-04-06T14:30) to MySQL (2026-04-06 14:30)
$sale_start = str_replace('T', ' ', $sale_start);
```

---

## Database Schema

### ticket_type Table
```sql
ticket_id      INT PRIMARY KEY AUTO_INCREMENT
eid            INT FK (references event_details.eid)
ticket_name    VARCHAR(100)
price          DECIMAL(10, 2)
capacity       INT
sold_cout      INT (Current count of sold tickets)
sale_start     DATETIME
sale_end       DATETIME
status         VARCHAR(20) (e.g., 'active', 'closed', 'cancelled')
```

---

## Integration Steps

### Step 1: Replace Files
```
OLD: admin/addEventForm.php → NEW: admin/addEventForm_new.php
OLD: app/actions/controller.php → NEW: app/actions/controller_new.php
```

After testing, rename the new files to overwrite the originals:
- `addEventForm_new.php` → `addEventForm.php`
- `controller_new.php` → `controller.php`

### Step 2: Update Form Action (if needed)
Ensure the form in `addEventForm.php` posts to `../../app/actions/controller.php`:
```html
<form method="post" action="../../app/actions/controller.php" enctype="multipart/form-data">
```

### Step 3: Database Verification
Confirm the `ticket_type` table exists with the correct schema:
```sql
SHOW COLUMNS FROM ticket_type;
```

---

## User Flow

### Adding an Event with Tickets:
1. Admin fills in Event Details section
2. Event form automatically adds one empty Ticket Tier
3. Admin fills in Ticket Tier details (name, price, capacity, sale dates)
4. Admin can click "+ Add Ticket Tier" to add more tiers
5. Admin can click "Remove" on any tier to delete it
6. Admin clicks "Publish Event"
7. System creates the event and all ticket tiers in one transaction

### Example Usage:
```
Event: "Tech Conference 2026"
├─ Ticket Tier 1: "VIP Pass" - Rs. 5000, Capacity: 50
├─ Ticket Tier 2: "Standard" - Rs. 2500, Capacity: 200
└─ Ticket Tier 3: "Student" - Rs. 1500, Capacity: 100
```

---

## Features Breakdown

### 1. Dynamic Tier Management
- Add/remove tiers without page reload
- Each tier gets a unique incrementing ID
- JavaScript generates form fields dynamically

### 2. Form Validation
- All ticket fields are marked `required` (HTML5 validation)
- Price and capacity have min/max constraints
- Empty tiers can be removed

### 3. DateTime Handling
- HTML5 `datetime-local` inputs for user-friendly selection
- Automatic conversion to MySQL datetime format on submit
- Supports NULL values (sale dates are optional)

### 4. Error Handling
- Checks if ticket tiers are provided
- Validates ticket array integrity
- PHP errors on DB insertion failures
- Confirmation dialog on success

---

## Styling Reference

### Ticket Tier Styling:
```css
.ticket-tier {
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 0.6rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.btn-add-ticket {
    background-color: #007BFF;
    color: white;
    padding: 0.8rem 1.5rem;
}

.btn-remove-ticket {
    background-color: #dc3545;
    color: white;
    padding: 0.5rem 1rem;
}
```

---

## Next Steps

### 1. Display Tickets on Event Page
Update `public/event_card.php` to fetch and display tickets from `ticket_type` table:
```php
$query = $connection->prepare(
    "SELECT ticket_name, price, capacity FROM ticket_type WHERE eid = ? AND status = 'active'"
);
```

### 2. Implement Ticket Purchase System
Create booking system to:
- Decrement `sold_cout` when ticket is purchased
- Check capacity before allowing purchase
- Validate sale dates

### 3. Manage Existing Events
Create edit functionality to update ticket tiers for already-created events

---

## Testing Checklist

- [ ] Add event with 1 ticket tier - verify in DB
- [ ] Add event with 3+ ticket tiers - verify all inserted
- [ ] Add and remove ticket tiers - verify form behavior
- [ ] Submit without ticket info - verify error handling
- [ ] Check sale date format in database - should be datetime
- [ ] Verify ticket prices stored correctly as decimals
- [ ] Confirm event_id properly linked as FK

---

## Troubleshooting

### Issue: "Error inserting ticket tier"
**Solution:** Verify `ticket_type` table exists and schema matches

### Issue: Tickets not inserted, event created
**Solution:** Check if ticket arrays are empty or POST data not reaching controller

### Issue: DateTime showing incorrectly
**Solution:** Verify MySQL timezone is set correctly. HTML5 datetime-local doesn't include timezone info.

### Issue: Foreign key constraint error
**Solution:** Ensure event is inserted successfully before ticket insertion. Check event_id value.

