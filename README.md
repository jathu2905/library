# Online Book Store

Welcome to the Online Book Store project. This application serves as a comprehensive portal for managing an online library, categorizing books, and serving PDF readings. It features a complete role-based authentication system with a private admin dashboard and public-facing browsing capabilities.

## Architecture

The project has been refactored into a scalable, robust, and professional file structure outlining clear separation of concerns:

- **`admin/`**: Houses all authenticated backend processes restricted to administrators.
- **`assets/`**: Contains static frontend files separated natively into `css/`, `img/`, and `js/`.
- **`config/`**: Home to configuration scripts, MySQL connection (`db.php`), and initialization tools (`setup.php`, `schema.sql`).
- **`includes/`**: Shared view components (`header.php` and `footer.php`) that maintain navigational cohesion across internal files using dynamic `BASE_URL` routing.
- **`uploads/pdfs/`**: Secure static directory where server-handled book PDFs are stored by administrators.

## Installation

1. Copy this project to your `htdocs/` folder (or equivalent web server directory).
2. Start up Apache and MySQL globally (e.g., via XAMPP).
3. Ensure you have a MySQL database created (default name expected is `book_store`).
4. Execute `config/schema.sql` within your database, or visit `http://localhost/library/config/setup.php` which automatically initializes seed data.
5. In `config/db.php`, modify the `$user` and `$pass` variables to match your local MYSQL setup if you deviate from defaults.

## Default Credentials
- **Admin Details**: `admin` / `admin123` *(Note: Setup script creates dummy hashes by default using PHP's standard algorithms).*

## Features
- **Public Views**: Browse books under structured categories, view dynamically loaded lists, interact seamlessly.
- **PDF Uploading**: Direct support for hosting and browsing interactive PDFs online.
- **Admin Dashboard**: Modify entries with ease; perform Create, Read, Update, and Delete operations on entries remotely without raw DB interaction.
