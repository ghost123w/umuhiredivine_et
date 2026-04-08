# High-Converting Landing Page

A lightweight, framework-less landing page with scroll-triggered animations and a secure admin panel.

## Features
- **No Frameworks:** Built with pure PHP, CSS, and JS.
- **Scroll Animations:** Intersection Observer API for smooth reveal effects.
- **Secure Admin Panel:** Password hashing, session management, and CSRF protection.
- **Database Support:** Supports both SQLite (default) and MySQL.

## Setup Instructions

### 1. Database Configuration
By default, the project uses SQLite. The database file is located in `data/database.db`.

To switch to **MySQL** (recommended for cPanel deployment):
1. Create a MySQL database and user via cPanel.
2. Import `schema.sql` using phpMyAdmin.
3. Update `config.php`:
   - Change `DB_TYPE` to `'mysql'`.
   - Update `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS`.

### 2. Admin Credentials
The default admin account is:
- **Username:** `admin`
- **Password:** `admin123`

You can log in at `/admin/login.php`.

### 3. Deployment
Upload all files to your `public_html` directory. The `.htaccess` file is included to protect the `data/` directory.

## Directory Structure
- `/admin`: Dashboard, Login, and Logout.
- `/css`: Stylesheets.
- `/js`: JavaScript for animations.
- `/includes`: Database connection and helper functions.
- `/data`: SQLite database file (if used).
- `index.php`: Front-end homepage.
- `config.php`: Project configuration.
