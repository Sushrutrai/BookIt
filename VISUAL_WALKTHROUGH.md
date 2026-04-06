# Visual Walkthrough: Using the New Ticket Tier System

## Getting Started

### 1. Accessing the Form
```
Navigate to: http://yourdomain.com/admin/addEventForm_new.php
(After migration, this becomes: /admin/addEventForm.php)
```

### 2. What You'll See
```
╔════════════════════════════════════════════════════════════╗
║                    Add your Event                          ║
║             Fill the fields to create event                ║
╚════════════════════════════════════════════════════════════╝

┌─ EVENT DETAILS ───────────────────────────────────────────┐
│  Event Name        [________________                       │
│  Date              [Select Date ▼]                        │
│  Category          [Select Category ▼]                    │
│  Description       [_____________________                  │
│  Location          [________________                       │
│  Maps Link         [________________                       │
│  Event Image       [Choose File]                           │
└──────────────────────────────────────────────────────────┘

┌─ TICKET TIERS ────────────────────────────────────────────┐
│  Add ticket types for your event. Set custom names,        │
│  prices, and quantities for each tier.                     │
│                                                             │
│ ┌─ TICKET TIER 1 ────────────────────────  [Remove]      │
│ │  Ticket Name          [VIP Pass ________]                │
│ │  Price (Rs.)          [5000.00 __]                       │
│ │  Capacity             [50 ___]                           │
│ │  Sale Start Date      [2026-04-10 09:00]                 │
│ │  Sale End Date        [2026-05-14 23:59]                 │
│ └─────────────────────────────────────────────────────────┘
│                                                             │
│ [+ Add Ticket Tier]                                        │
└──────────────────────────────────────────────────────────┘

┌─ BUTTONS ────────────────────────────────────────────────┐
│  [Publish Event]                            [Reset]       │
└──────────────────────────────────────────────────────────┘
```

---

## Step-by-Step Workflow

### Step 1: Fill Event Details
```
1. Enter Event Name:
   ├─ Text: "Tech Conference 2026"
   
2. Select Date:
   ├─ Click date field → Calendar opens
   ├─ Select: 2026-05-15
   
3. Choose Category:
   ├─ Click dropdown → Shows list
   ├─ Select: "Conferences"
   
4. Add Description:
   ├─ Enter: "Annual tech conference with 3 days of talks and networking"
   
5. Add Location:
   ├─ Enter: "Convention Center, City XYZ"
   
6. Maps Link (Optional):
   ├─ Enter: "https://maps.google.com/?q=Convention+Center"
   
7. Upload Image:
   ├─ Click "Choose File"
   ├─ Select JPG/PNG file
   ├─ Size recommended: 1920x1080px
```

### Step 2: Configure Ticket Tiers

#### Adding Ticket Tier 1 (VIP):
```
┌─ TICKET TIER 1 ──────────────────────────────────────────┐
│                                                [Remove]    │
│                                                            │
│ Ticket Name      [VIP Pass ________________________]       │
│ Price (Rs.)      [5000.00 ________]                       │
│ Capacity         [50 _______]                             │
│ Sale Start Date  [2026-04-10 09:00]                       │
│ Sale End Date    [2026-05-14 23:59]                       │
│                                                            │
│ (Date fields optional - leave blank for unlimited sales)  │
└────────────────────────────────────────────────────────────┘

Fill with:
├─ Ticket Name: VIP Pass
├─ Price: 5000
├─ Capacity: 50
├─ Sale Start: 2026-04-10 09:00 (when sales open)
└─ Sale End: 2026-05-14 23:59 (when sales close)
```

#### Adding Ticket Tier 2 (Standard):
```
1. Click "+ Add Ticket Tier" button
   │
   └─> New tier appears below Tier 1

┌─ TICKET TIER 2 ──────────────────────────────────────────┐
│                                                [Remove]    │
│                                                            │
│ Ticket Name      [Standard Pass ________________]         │
│ Price (Rs.)      [2500.00 ________]                       │
│ Capacity         [200 ________]                            │
│ Sale Start Date  [2026-04-10 09:00]                       │
│ Sale End Date    [2026-05-14 23:59]                       │
└────────────────────────────────────────────────────────────┘
```

#### Adding Ticket Tier 3 (General/Student):
```
1. Click "+ Add Ticket Tier" button again

┌─ TICKET TIER 3 ──────────────────────────────────────────┐
│                                                [Remove]    │
│                                                            │
│ Ticket Name      [Student Ticket ________________]        │
│ Price (Rs.)      [1500.00 ________]                       │
│ Capacity         [100 ________]                            │
│ Sale Start Date  [2026-04-10 09:00]                       │
│ Sale End Date    [2026-05-14 23:59]                       │
└────────────────────────────────────────────────────────────┘
```

---

## Managing Tiers

### Adding a Tier
```
Current state:
├─ Tier 1: VIP Pass
├─ Tier 2: Standard Pass
└─ [+ Add Ticket Tier]

Click "+ Add Ticket Tier":
├─ Tier 1: VIP Pass
├─ Tier 2: Standard Pass
├─ Tier 3: [Empty - ready to fill]
└─ [+ Add Ticket Tier]
```

### Removing a Tier
```
Current state:
├─ Tier 1: VIP Pass ...................... [Remove]
├─ Tier 2: Standard Pass ................ [Remove]
├─ Tier 3: Student Ticket ............... [Remove]
└─ [+ Add Ticket Tier]

Click "Remove" on Tier 2:
├─ Tier 1: VIP Pass ...................... [Remove]
├─ Tier 3: Student Ticket ............... [Remove]  (Note: still Tier 3 ID)
└─ [+ Add Ticket Tier]
```

### Modifying Fields
```
To change price of VIP Pass:
1. Click on price field: [5000.00 ________]
2. Clear current value: [________]
3. Type new value: [6000.00 ________]
4. No page reload needed - field updates in real-time

Same process for:
├─ Ticket Name
├─ Capacity
├─ Sale dates
└─ All other fields
```

---

## Form Validation

### What Happens When You Try to Submit:

#### Scenario 1: Missing Required Field
```
User tries to submit with empty Ticket Name

Browser shows validation error:
┌─────────────────────────────────────┐
│ ⚠  Please fill out this field       │
│    Ticket Name is required          │
└─────────────────────────────────────┘

Form doesn't submit until field is filled
```

#### Scenario 2: Invalid Price (Text instead of number)
```
User enters: "Five Thousand" in Price field

Browser validation error:
┌─────────────────────────────────────────┐
│ ⚠  Please enter a valid number         │
│    Price field accepts numbers only    │
└─────────────────────────────────────────┘
```

#### Scenario 3: Invalid Capacity (0 or negative)
```
User enters: "-50" in Capacity field

Browser validation error:
┌──────────────────────────────────────┐
│ ⚠  Value must be greater than 0      │
│    Capacity must be at least 1        │
└──────────────────────────────────────┘
```

#### Scenario 4: All Fields Valid
```
All required fields filled:
├─ Event Name: ✅
├─ Date: ✅
├─ Category: ✅
├─ Location: ✅
├─ Ticket Tier 1: ✅ (Name, Price, Capacity)
├─ Ticket Tier 2: ✅ (Name, Price, Capacity)
└─ Image: ✅

User clicks [Publish Event]:
├─ Form validates all fields
├─ All validation passes
├─ Form submits to server
└─ Processing starts...
```

---

## Success Flow

### After Clicking "Publish Event"

```
┌─ PROCESSING ──────────────────────────┐
│  Creating event and tickets...         │
│  [████████████████████░░░░░░]  75%    │
└───────────────────────────────────────┘

Server processes:
1. Insert Event (1 query)
   ├─ Event Name: Tech Conference 2026 ✓
   ├─ Date: 2026-05-15 ✓
   ├─ Category: Conferences ✓
   └─ (other fields...) ✓
   
2. Get Event ID: 5 ✓

3. Insert Ticket Tier 1 (VIP Pass) ✓
   ├─ event_id: 5
   ├─ name: VIP Pass
   ├─ price: 5000.00
   ├─ capacity: 50
   └─ status: active

4. Insert Ticket Tier 2 (Standard Pass) ✓
   ├─ event_id: 5
   ├─ name: Standard Pass
   ├─ price: 2500.00
   ├─ capacity: 200
   └─ status: active

5. Insert Ticket Tier 3 (Student Ticket) ✓
   ├─ event_id: 5
   ├─ name: Student Ticket
   ├─ price: 1500.00
   ├─ capacity: 100
   └─ status: active

All 4 inserts successful!
```

### Success Message

```
┌──────────────────────────────────────────┐
│  ✓ Event Added Successfully              │
│                                           │
│  Event: Tech Conference 2026             │
│  Location: Convention Center, City XYZ   │
│  Date: 2026-05-15                        │
│                                           │
│  Ticket Tiers Created: 3                 │
│  ├─ VIP Pass (Rs. 5000) - 50 tickets    │
│  ├─ Standard Pass (Rs. 2500) - 200      │
│  └─ Student Ticket (Rs. 1500) - 100     │
│                                           │
│  [OK to Continue]                        │
└──────────────────────────────────────────┘

Click [OK] → Redirects to form for next event
```

---

## Database View (After Success)

### What Gets Created in Database:

#### event_details Table
```
eid │ event_name          │ event_date │ category_id │ ... │ status
─────┼─────────────────────┼────────────┼─────────────┼─────┼────────
  5 │ Tech Conference 2026│ 2026-05-15 │      3      │ ... │ active
```

#### ticket_type Table
```
ticket_id │ eid │ ticket_name    │ price  │ capacity │ sold_cout │ status
──────────┼─────┼────────────────┼────────┼──────────┼───────────┼────────
    1     │  5  │ VIP Pass       │ 5000.0 │    50    │     0     │ active
    2     │  5  │ Standard Pass  │ 2500.0 │   200    │     0     │ active
    3     │  5  │ Student Ticket │ 1500.0 │   100    │     0     │ active
```

---

## Editing Events

### Modifying an Existing Event

**Note:** Edit functionality for existing events is for future development.

Current behavior:
```
After publishing, you cannot edit from the form.

To change ticket details:
1. Delete the event (removes all tickets via FK CASCADE)
2. Create new event with updated tiers
```

---

## Common Tasks

### Task 1: Create Free Event (No Tickets)
```
❌ Cannot do this in current version
Reason: Tickets are required when creating event

Workaround: Add 1 free ticket tier with price = 0
├─ Ticket Name: Free Admission
├─ Price: 0
├─ Capacity: 500
```

### Task 2: Limited Time Sales
```
Ticket Tier 1: Early Bird (Limited Time)
├─ Ticket Name: Early Bird
├─ Price: 1000
├─ Capacity: 50
├─ Sale Start: 2026-04-01 00:00
├─ Sale End: 2026-04-10 23:59  ← Stops selling after this date

Ticket Tier 2: Regular (Full Duration)
├─ Ticket Name: Regular
├─ Price: 2000
├─ Capacity: 200
├─ Sale Start: 2026-04-01 00:00
├─ Sale End: 2026-05-14 23:59  ← Longer sale window
```

### Task 3: Tiered Pricing
```
Most expensive → Least expensive

Tier 1: Premium (5000)
Tier 2: Standard (3000)
Tier 3: Budget (1000)

Presentation: Users see price options from high to low
```

### Task 4: Different Capacities
```
Limited High-Price vs Many Low-Price

Tier 1: VIP (5000/50 people)
├─ Limited seating, premium experience

Tier 2: Standard (2500/200 people)
├─ General admission, good experience

Tier 3: General (1000/500 people)
├─ Unrestricted seating
```

---

## Keyboard Shortcuts

| Action | Keyboard | Mouse |
|--------|----------|-------|
| Focus Next Field | Tab | Click field |
| Focus Previous | Shift+Tab | Click field |
| Open Date Picker | Enter (on date field) | Click calendar icon |
| Submit Form | Ctrl+Enter | Click [Publish Event] |
| Reset Form | N/A | Click [Reset] |

---

## Tips & Best Practices

### 1. Organize Tiers by Price
```
✅ Good Order:
└─ VIP (highest price) → Standard → General (lowest)

❌ Confusing Order:
└─ General → VIP → Standard (random)
```

### 2. Clear Naming
```
✅ Good Names:
├─ "VIP Pass - Premium Seating"
├─ "Standard Pass - General Admission"
└─ "Student Ticket - With ID Required"

❌ Vague Names:
├─ "Tier 1"
├─ "Option A"
└─ "Premium Package"
```

### 3. Realistic Pricing
```
✅ Reasonable:
├─ VIP: 5000 Rs
├─ Standard: 2500 Rs
└─ General: 1000 Rs

❌ Unrealistic:
├─ VIP: 500000 Rs
├─ Standard: 2 Rs
└─ General: 0.01 Rs
```

### 4. Capacity Planning
```
✅ Good Capacity:
├─ VIP: 50 (exclusive)
├─ Standard: 200 (majority)
└─ General: 500+ (as many as needed)

❌ Bad Capacity:
├─ VIP: 10000 (not exclusive)
├─ Standard: 1 (too limited)
└─ General: 0 (impossible to sell)
```

### 5. Date Management
```
✅ Good Dates:
├─ Sale Start: 1-2 weeks before event
├─ Sale End: 1-2 days before event
└─ Allows time for promotion and planning

❌ Bad Dates:
├─ Sale Start: After event date
├─ Sale End: Before event date
└─ Sales already closed or not open yet
```

---

## Troubleshooting in Form

### Issue: Price Won't Accept Decimal
```
Trying: 1999.99
Result: Won't let me type decimals

Solution: Input type accepts decimals by default
├─ Try: Clear field and retype
├─ Try: Use period (.) not comma (,)
└─ Try: Refresh page if issue persists
```

### Issue: Date Picker Not Opening
```
Clicking on date field does nothing

Solution:
├─ Make sure browser supports HTML5
├─ Try: Click the calendar icon next to field
├─ Try: Type date manually (YYYY-MM-DD)
└─ Try: Different browser if issue persists
```

### Issue: Can't Click "Add Ticket" Button
```
Button appears disabled or won't respond

Solution:
├─ Check if browser JavaScript is enabled
├─ Try: Page reload
├─ Try: Close browser and reopen
└─ Contact admin if still not working
```

---

## Quick Reference Card

```
╔════════════════════════════════════════════════════════════╗
║         TICKET TIER FORM - QUICK REFERENCE                ║
╠════════════════════════════════════════════════════════════╣
║                                                             ║
║  Field           Required  Type      Example               ║
║  ────────────────────────────────────────────────────────  ║
║  Ticket Name     ✅        Text      "VIP Pass"            ║
║  Price           ✅        Number    "5000.00"             ║
║  Capacity        ✅        Number    "50"                  ║
║  Sale Start      ❌        DateTime  "2026-04-10 09:00"   ║
║  Sale End        ❌        DateTime  "2026-05-14 23:59"   ║
║                                                             ║
║  Actions:                                                   ║
║  ├─ Add Tier: "+ Add Ticket Tier" button                  ║
║  ├─ Remove Tier: "Remove" button per tier                 ║
║  └─ Submit: "Publish Event" button                         ║
║                                                             ║
║  Limits:                                                    ║
║  ├─ Min Tiers: 1 (auto-generated)                         ║
║  ├─ Max Tiers: Unlimited (browser dependent)              ║
║  ├─ Price Precision: 2 decimals (e.g., 999.99)           ║
║  └─ Capacity: Positive integers only                      ║
║                                                             ║
╚════════════════════════════════════════════════════════════╝
```

