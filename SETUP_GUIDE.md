# Ticket Tier System - Quick Setup Guide

## ⚡ 5-Minute Quick Start

### Step 1: Verify Database Table Exists
```sql
-- Run this query to check if ticket_type table exists:
SHOW TABLES LIKE 'ticket_type';

-- If not found, create it:
CREATE TABLE ticket_type (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    eid INT NOT NULL,
    ticket_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    capacity INT NOT NULL,
    sold_cout INT DEFAULT 0,
    sale_start DATETIME,
    sale_end DATETIME,
    status VARCHAR(20) DEFAULT 'active',
    FOREIGN KEY (eid) REFERENCES event_details(eid) ON DELETE CASCADE
);
```

### Step 2: Backup Original Files
```bash
cp admin/addEventForm.php admin/addEventForm.php.backup
cp app/actions/controller.php app/actions/controller.php.backup
```

### Step 3: Replace Files (After Testing)
```bash
# Test first! Only deploy after verification:
cp admin/addEventForm_new.php admin/addEventForm.php
cp app/actions/controller_new.php app/actions/controller.php
```

### Step 4: Clear Browser Cache
Users should refresh with Ctrl+Shift+R to load new CSS

### Step 5: Test
Go to `admin/addEventForm.php` and try adding an event with 2 ticket tiers

---

## 🚀 Full Deployment Guide

### Phase 1: Pre-Deployment (15 minutes)

#### 1.1 Review Documentation
- [ ] Read TICKET_TIER_SUMMARY.md (overview)
- [ ] Read TICKET_TIER_IMPLEMENTATION.md (technical)
- [ ] Review list of changes

#### 1.2 Prepare Database
```sql
-- Verify ticket_type table schema
DESC ticket_type;

-- Expected output:
-- Field       | Type             | Null | Key
-- ------------|------------------|------|-------
-- ticket_id   | int             | NO   | PRI
-- eid         | int             | NO   | MUL
-- ticket_name | varchar(100)    | NO   |
-- price       | decimal(10,2)   | NO   |
-- capacity    | int             | NO   |
-- sold_cout   | int             | YES  |
-- sale_start  | datetime        | YES  |
-- sale_end    | datetime        | YES  |
-- status      | varchar(20)     | YES  |

-- If schema differs, create correct table (see Step 1 above)
```

#### 1.3 Backup Critical Files
```bash
# Create backup directory
mkdir -p backups/$(date +%Y%m%d)

# Backup files
cp admin/addEventForm.php backups/$(date +%Y%m%d)/
cp app/actions/controller.php backups/$(date +%Y%m%d)/
cp assets/css/admin_form_style.css backups/$(date +%Y%m%d)/

echo "Backups created in backups/$(date +%Y%m%d)/"
```

#### 1.4 Verify File Locations
```bash
ls -la admin/addEventForm_new.php
ls -la app/actions/controller_new.php
ls -la assets/css/admin_form_style.css

# All should exist and show file size > 0
```

---

### Phase 2: Staging Testing (30 minutes)

#### 2.1 Deploy to Staging
```bash
# Copy new files to staging environment
cp admin/addEventForm_new.php /staging/admin/addEventForm.php
cp app/actions/controller_new.php /staging/app/actions/controller.php
cp assets/css/admin_form_style.css /staging/assets/css/
```

#### 2.2 Test Scenarios
```
Test 1: Single Ticket Tier
├─ Create event with 1 ticket tier
├─ Check: Event created in DB
└─ Check: 1 ticket in ticket_type table

Test 2: Multiple Ticket Tiers
├─ Create event with 3 ticket tiers
├─ Check: Event created in DB
└─ Check: 3 tickets in ticket_type table, all with same eid

Test 3: Form Validation
├─ Try to submit without ticket name
├─ Verify: Browser blocks submission
├─ Fill name, try without price
├─ Verify: Browser blocks submission

Test 4: Remove & Re-add
├─ Add 4 tiers
├─ Remove middle tier (Tier 2)
├─ Try to remove another
├─ Add 1 more tier (should now have 4 again)
├─ Submit
└─ Verify: 4 tickets inserted (not 2)

Test 5: Database Integrity
├─ Query: SELECT * FROM ticket_type WHERE eid = <last_event_id>
├─ Verify: All fields populated correctly
├─ Verify: sold_cout = 0 for all
└─ Verify: status = 'active' for all
```

#### 2.3 Verify HTML/CSS Rendering
```
Test 1: Form Display
├─ Visit /admin/addEventForm.php
├─ Verify: "Ticket Tiers" section visible
├─ Verify: "[+ Add Ticket Tier]" button present
├─ Verify: One tier auto-generated

Test 2: Tier Styling
├─ Verify: Light gray background (#f9f9f9)
├─ Verify: Border around tier container
├─ Verify: Header with "Ticket Tier 1" text
├─ Verify: Red "Remove" button on right

Test 3: Add/Remove Buttons
├─ Click "+ Add Ticket Tier" (JavaScript should work)
├─ Verify: New tier appears below without page reload
├─ Click "Remove" on any tier
├─ Verify: Tier disappears without page reload

Test 4: Form Inputs
├─ Click on each input field
├─ Verify: Proper input type (text, number, datetime-local)
├─ Verify: Placeholder text shows
├─ Verify: Validation constraints active (required, min/step)
```

#### 2.4 Check No Errors in Console
```javascript
// Open browser Developer Tools (F12)
// Go to Console tab
// Should be NO red errors
// Should be NO JavaScript warnings
// Verify: Form submits cleanly
```

---

### Phase 3: Production Deployment (10 minutes)

#### 3.1 Schedule Maintenance Window
```
Best time: Low traffic hours (e.g., 2-4 AM)
Duration: 5-10 minutes
Impact: Admin event creation may be temporarily unavailable
Notify: Team members 24 hours in advance
```

#### 3.2 Final Verification
```sql
-- Before deploying, double-check production database is ready
SELECT COUNT(*) FROM ticket_type;
-- Should return 0 or small number (existing records)

-- Verify schema is correct
DESC ticket_type;
```

#### 3.3 Deploy Files
```bash
# In production directory:

# 1. Replace form file
cp admin/addEventForm_new.php admin/addEventForm.php

# 2. Replace controller file
cp app/actions/controller_new.php app/actions/controller.php

# 3. Verify CSS was updated (should already have new classes)
grep "\.ticket-tier {" assets/css/admin_form_style.css
# Should find the class

echo "Deployment complete at $(date)"
```

#### 3.4 Verify Production
```
☑ Admin can access /admin/addEventForm.php
☑ Form loads without errors (check console)
☑ Can add events with ticket tiers
☑ Database records created correctly
☑ No error logs generated
```

#### 3.5 Rollback Plan
```bash
# If issues occur, rollback immediately:

cp admin/addEventForm.php.backup admin/addEventForm.php
cp app/actions/controller.php.backup app/actions/controller.php

# Then investigate issue
```

---

### Phase 4: Post-Deployment (15 minutes)

#### 4.1 Monitor Logs
```bash
# Check error logs for first hour
tail -f /var/log/apache2/error.log
tail -f /var/log/mysql/error.log
tail -f /var/log/php-errors.log

# Look for:
# ❌ SQL errors
# ❌ Foreign key constraint violations
# ❌ File not found errors
# ✅ Should be silent if working correctly
```

#### 4.2 Test Live System
```
Test 1: Create Event
├─ Log in as admin
├─ Go to Add Event form
├─ Fill event details
├─ Add 2 ticket tiers (VIP + Standard)
├─ Click "Publish Event"
└─ Verify success message

Test 2: Check Database
├─ Query event_details for new event
├─ Query ticket_type for new event's tickets
├─ Verify both records exist
└─ Verify foreign key relationship correct

Test 3: Event Appears
├─ Check if event shows on main page
├─ Verify event details display
└─ Note: Tickets display logic still using hardcoded data
        (This is expected - ticket display is next phase)
```

#### 4.3 User Training
```
1. Email admins link to VISUAL_WALKTHROUGH.md
2. Schedule optional 15-minute demo (if needed)
3. Provide quick reference card (from VISUAL_WALKTHROUGH.md)
4. Create support channel for questions
```

#### 4.4 Documentation Updates
```bash
# Update your internal wiki/docs:

- Feature: Dynamic Ticket Tier Creation ✅ Deployed
- Date: 2026-04-06
- Status: LIVE

- Admin Guide: [Link to VISUAL_WALKTHROUGH.md]
- Technical Docs: [Link to TICKET_TIER_IMPLEMENTATION.md]
- Known Issues: [None initially]
```

---

## 📊 Deployment Checklist

```
PRE-DEPLOYMENT
□ Read documentation
□ Database table exists and verified
□ Files backed up safely
□ Files renamed from _new to production versions
□ No typos in file paths

STAGING TESTING
□ Form displays correctly
□ Single tier test passes
□ Multiple tier test passes
□ Validation test passes
□ Add/remove tier test passes
□ Database records created
□ No JavaScript errors in console
□ CSS styling applied correctly

PRODUCTION DEPLOYMENT
□ Maintenance window scheduled
□ Stakeholders notified
□ Files deployed to production
□ URLs verify (no 404 errors)
□ Database responsive
□ No error logs generated
□ Admins can create events
□ Database records created
□ Tickets linked to events correctly

POST-DEPLOYMENT
□ Monitor logs for 1 hour
□ Test with sample event
□ Admin training complete
□ Support channel established
□ Documentation links shared
□ Known issues documented (if any)
□ Success notification sent to team
```

---

## ⚠️ Common Pitfalls to Avoid

### Pitfall 1: Wrong File Versions
```
❌ WRONG: Copy controller_new.php without renaming
         Form tries to POST to controller_new.php which doesn't exist

✅ RIGHT: Rename controller_new.php to controller.php BEFORE deploying
         -or- Update form action to point to controller_new.php
```

### Pitfall 2: Missing Database Table
```
❌ WRONG: Deploy code without creating ticket_type table
         Admin creates event but SQL error occurs

✅ RIGHT: Create table BEFORE deploying code
         Verify with: SHOW TABLES LIKE 'ticket_type';
```

### Pitfall 3: Cached CSS/JS
```
❌ WRONG: Users see old form without ticket tier section
         Browser has cached admin_form_style.css

✅ RIGHT: Tell users to clear cache or:
         - Rename CSS file: admin_form_style_v2.css
         - Update reference in HTML
```

### Pitfall 4: File Path Issues
```
❌ WRONG: Using forward slashes on Windows server
         /admin/addEventForm.php → File not found

✅ RIGHT: Use backslashes or let OS handle it
         \admin\addEventForm.php or use relative paths
```

### Pitfall 5: Permissions
```
❌ WRONG: New files don't have read permissions
         Server can't open files

✅ RIGHT: Verify file permissions after copying
         chmod 644 admin/addEventForm.php
```

---

## 🆘 Quick Troubleshooting

### Issue: Form shows old version without ticket section
**Solution:**
- Check if correct file is deployed (should be addEventForm_new.php or renamed)
- Clear browser cache (Ctrl+Shift+R)
- Verify file path is correct
- Check file permissions

### Issue: "Error inserting ticket tier" message
**Solution:**
- Check ticket_type table exists: `SHOW TABLES LIKE 'ticket_type';`
- Check table schema: `DESC ticket_type;`
- Verify foreign key: `SHOW CREATE TABLE ticket_type;`
- Check MySQL error logs for detailed error

### Issue: Tickets created but not linked to event
**Solution:**
- Verify eid (event_id) is being passed to ticket insert
- Check that event insert succeeded before ticket inserts
- Verify event_id matches in both tables: 
  ```sql
  SELECT * FROM event_details WHERE eid = X;
  SELECT * FROM ticket_type WHERE eid = X;
  ```

### Issue: Admin can't access form at all
**Solution:**
- Verify form file exists in correct location
- Check server error logs: `tail -f /var/log/apache2/error.log`
- Verify file permissions: `ls -la admin/addEventForm.php`
- Try accessing form directly: `/admin/addEventForm.php`

---

## 📞 Support Contacts

For issues during deployment:
1. Check TICKET_TIER_IMPLEMENTATION.md - Troubleshooting section
2. Check VISUAL_WALKTHROUGH.md - Troubleshooting section
3. Review this guide's Troubleshooting section
4. Check server logs for error details
5. Contact development team with error message

---

## ✅ Success Indicators

After deployment, you should see:

```
☑ Admin form loads with Ticket Tier section
☑ Can add/remove ticket tiers without page reload
☑ Form validation prevents empty submissions
☑ Events created successfully in database
☑ Ticket records linked to events (same eid)
☑ Database fields: name, price, capacity, dates all saved
☑ No error logs generated
☑ Admins report easier event creation
☑ No complaints about form not working
```

---

## 📈 Next Steps After Deployment

1. **Monitor Usage**
   - Track how many events created with tickets
   - Monitor database growth
   - Check for any error patterns

2. **Gather Feedback**
   - Ask admins about user experience
   - Note any feature requests
   - Document pain points

3. **Phase 2: Display Tickets**
   - Update event_card.php to show dynamic tickets
   - Replace hardcoded ticket display with DB queries
   - Test ticket purchase flow

4. **Phase 3: Booking System**
   - Implement ticket sales tracking
   - Decrement sold_cout on purchase
   - Validate capacity constraints

5. **Future Enhancements**
   - Ticket tier templates
   - Bulk pricing
   - Discounts and promotions
   - Sales analytics

---

**Ready to deploy? Start with Phase 1! 🚀**

