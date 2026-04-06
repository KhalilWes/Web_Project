# Student 5 Handoff Pack (Admin + Integration)

Date: 2026-04-06
Owner: Khalil (Student 5)

## Goal
This document explains exactly what is completed and what each teammate must connect next so work can continue in parallel.

## Completed Files
- admin/login.php
- admin/dashboard.php
- admin/logout.php
- backend/includes/db.php
- backend/includes/auth.php
- backend/actions/process_booking.php
- backend/actions/update_voyage.php
- backend/actions/delete_voyage.php

## Security Status (Already Implemented)
- Session-based admin auth
- Password verification with password_verify
- Admin route guard (require admin_id in session)
- CSRF token generation and validation for admin forms
- Prepared statements for all write queries
- Prepared statements for dashboard read queries
- XSS-safe output escaping in admin dashboard/login messages

## Routes and Responsibilities

### Admin Login
- Page: /admin/login.php
- Method: POST (for login submit)
- Required POST fields:
  - csrf_token
  - email
  - password
- Success:
  - session admin_id is set
  - redirects to /admin/dashboard.php

### Admin Dashboard
- Page: /admin/dashboard.php
- Access: admin only
- Shows:
  - reservations joined with voyages
  - voyage management section (update/delete)

### Admin Logout
- Page: /admin/logout.php
- Action:
  - clears session and cookie
  - redirects to login page

### Booking Integration Endpoint (Student 3 uses this)
- Endpoint: /backend/actions/process_booking.php
- Method: POST
- Required POST fields:
  - name
  - email
  - voyage_id
  - travelers
- Response: JSON
  - 201 success
  - 422 invalid input
  - 404 voyage not found
  - 405 wrong method
  - 500 server error

### Voyage Update Endpoint (Admin)
- Endpoint: /backend/actions/update_voyage.php
- Method: POST
- Required POST fields:
  - csrf_token
  - voyage_id
  - destination
  - departure_date (YYYY-MM-DD)

### Voyage Delete Endpoint (Admin)
- Endpoint: /backend/actions/delete_voyage.php
- Method: POST
- Required POST fields:
  - csrf_token
  - voyage_id
  - confirm_delete (must be yes)

## Database Contract Required By This Work

### admins table
Required columns:
- id (int, primary key)
- email (varchar, unique)
- password_hash (varchar)

### voyages table
Required columns:
- id (int, primary key)
- destination (varchar)
- departure_date (date)

### reservations table
Required columns:
- id (int, primary key)
- name (varchar)
- email (varchar)
- voyage_id (int, foreign key to voyages.id)
- travelers (int)
- status (varchar)

## What Teammates Need To Do Next

### Student 1 (UI owner)
- Attach global stylesheet to admin pages when style files are available.
- Reuse existing classes only (no change needed in backend logic).

### Student 2 (listing/details)
- Continue voyages/details pages using voyages table.
- Can rely on voyage updates/deletes done from admin panel.

### Student 3 (booking frontend)
- Set booking form action to /backend/actions/process_booking.php
- Send fields exactly: name, email, voyage_id, travelers
- Handle JSON status codes for user feedback

### Student 4 (core backend)
- Ensure table schema matches the database contract above.
- Ensure admins.password_hash stores password_hash() output.

## Quick Smoke Test Checklist
1. Open /admin/login.php and login with a valid admin user.
2. Confirm redirect to /admin/dashboard.php.
3. Update one voyage and verify database row changed.
4. Delete one voyage and verify row removed.
5. Submit booking POST to /backend/actions/process_booking.php and verify reservation inserted.
6. Attempt admin action with missing/invalid csrf_token and confirm it is blocked.

## Notes
- No global stylesheet was edited.
- If style.css is added later, only class assignment in admin HTML is needed.
