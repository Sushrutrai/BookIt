# Project Guidelines

## Code Style
PHP: Procedural with minimal structure. Use prepared statements for DB queries. Escape output with htmlspecialchars(). Include bootstrap.php for DB connection.

JavaScript: Vanilla JS, event delegation preferred over inline handlers.

CSS: BEM-inspired classes, mobile-first responsive design.

## Architecture
Event booking platform: Public UI (/public), Admin panel (/admin), Business logic (/app). Database: MySQL with 9 tables (see schema.sql).

Key flows: Event browsing → ticket selection → payment processing (incomplete).

Authentication: Session-based with user/admin roles.

## Build and Test
No automated build/test. Manual setup: PHP 7.4+, MySQL/MariaDB. Import schema.sql to 'bookit' database.

Run locally: php -S localhost:8000, visit /public/index.php

Maven tasks exist but irrelevant (project is PHP, not Java).

## Conventions
- Bootstrap all pages with require __DIR__ . '/../app/bootstrap.php'
- Use AJAX for bookmarks (POST to app/actions/bookmarks.php)
- MyEvents toggles: ?view=bookmarked|purchased
- Admin: Role-based access, event CRUD in /admin

See schema.sql for DB structure, report.md for TODOs, MYEVENTS_IMPLEMENTATION_SUMMARY.md for styling details.

Avoid SQL injection: Always use prepared statements (bug in login_register.php).

Users table has 'username' field, not 'name'.