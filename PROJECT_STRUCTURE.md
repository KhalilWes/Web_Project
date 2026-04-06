# Project Overview: Travel Agency Web Application
**Architecture:** Multi-page PHP Application (MPA)
**Database:** MySQL (MariaDB)
**Goal:** A platform to browse travel packages and book trips.

---

## Team Responsibilities & Ownership
*Crucial: Do not generate code that overlaps these boundaries unless specifically asked.*

### 1. Frontend - Homepage & Core Design (Student 1)
- **Files:** `index.php`, `style.css`, `assets/`
- **Scope:** General UI/UX, Navigation, Footer, Responsive Layout.
- **Guideline:** Use global CSS variables defined in `style.css` for all other pages.

### 2. Frontend - Discovery & Listings (Student 2)
- **Files:** `voyages.php`, `details.php`
- **Scope:** Dynamic cards, search filters, and destination galleries.
- **Logic:** Fetches data from the `voyages` table (handled by Student 4/5).

### 3. Frontend - Interactive Booking (Student 3)
- **Files:** `booking_form.js`, `validation.js`
- **Scope:** Client-side form validation and dynamic price calculation.
- **Guideline:** Sends data via POST to `actions/process_booking.php`.

### 4. Backend - Core Logic & DB (Youssef - Student 4)
- **Files:** `includes/db.php`, `models/Voyage.php`, `actions/process_booking.php`
- **Scope:** Database schema, raw SQL queries, and server-side data validation.

### 5. Admin & Integration (Khalil/Me - Student 5)
- **Files:** `admin/login.php`, `admin/dashboard.php`, `includes/auth.php`
- **Scope:** Admin CRUD, Session management, and linking Frontend forms to Backend logic.

---

## Shared Technical Standards
- **CSS:** All custom styles must be appended to `style.css` or scoped to specific classes to avoid breaking the Homepage.
- **Database Access:** Use the shared `$pdo` instance from `includes/db.php`. **Do not create new connection strings in local files.**
- **Naming Convention:** - PHP variables: `$camelCase`
    - Database columns: `snake_case`
    - CSS classes: `kebab-case`

## Directory Tree (Protected)
.
├── admin/              # Khalil's Workspace (Admin panel files)
├── assets/             # Student 1 (Images/Icons)
├── backend/            # Backend logic and shared server-side code
├── pages/              # Frontend pages (listings/details/etc.)
├── PROJECT_STRUCTURE.md
└── Roadmap.md