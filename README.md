## BSIT Student Profiling System

BSIT Student Profiling System is a PHP/MySQL web application for managing student profiles, dynamic information forms, and announcements for a BSIT department. It provides separate experiences for **students** and **admins**, including enrollment flows, profile editing, and admin-side student management and reporting.

### Features

- **Authentication**
  - Shared login screen for students and admin (`index.php`).
  - Admin login via username `admin`.
  - Student login via email address and password.

- **Student portal**
  - Registration and login.
  - Enrollment flow that guides first‑time students to fill out the required form (`enroll.php`).
  - Dynamic profile form defined by the admin (`createForm.php`) and stored in `form_fields` and `student_details`.
  - Modern profile page (`myProfile.php`) with:
    - Avatar upload and inline edit.
    - Bio and personal information.
    - Additional details built from dynamic fields.
    - Basic activity statistics (e.g. profile completeness).
    - Optional social links from dynamic fields.
  - Update profile page (`updateProfile.php`) with refreshed UI and support for editing both basic info and dynamic fields.
  - Student announcements page (`studentAnnouncement.php`).

- **Admin portal**
  - Admin dashboard (`adminDashboard.php`) with navigation provided by `adminSideBar.php`.
  - Manage students (`studentManagement.php`, `viewStudent.php`, `editStudent.php`, `deleteStudent.php`).
  - Define and lock/unlock dynamic form fields (`createForm.php`, `unlockForms.php`, `resetForm.php`).
  - Post and manage announcements (`adminAnnouncement.php`, `editAnnouncement.php`, `deleteAnnouncement.php`).
  - Generate reports (`generateReport.php`, `generateAllStudentsReport.php`).

- **Database**
  - MySQL database schema and sample data provided in `db/student_profiling.sql`.
  - Tables for `students`, `admin`, `form_fields`, `student_details`, `announcements`, and related data.

### Tech Stack

- PHP 8+ (plain PHP, no framework).
- MySQL / MariaDB.
- HTML5, CSS3, vanilla JavaScript.
- Font Awesome for icons.
- Designed to run on XAMPP / local LAMP stack (e.g. `C:\xampp\htdocs\BsitSP`).

### Getting Started

#### 1. Prerequisites

- PHP 8+ and MySQL/MariaDB (XAMPP, WAMP, MAMP, or similar).
- A web server (Apache recommended).
- A web browser (latest Chrome, Firefox, Edge, or Safari).

#### 2. Clone or copy the project

Place the project folder inside your web server directory. For XAMPP on Windows:

- Path: `C:\xampp\htdocs\BsitSP`

If you are using Git:

```bash
cd C:\xampp\htdocs
git clone <your-repo-url> BsitSP
```

#### 3. Create the database

1. Start Apache and MySQL from XAMPP (or your stack).
2. Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
3. Create a new database named:

   ```text
   student_profiling
   ```

4. Import the SQL dump:
   - Go to the **Import** tab.
   - Choose the file: `db/student_profiling.sql`.
   - Click **Go** to import tables and sample data.

#### 4. Configure database connection

The database connection settings are defined in [`db_connection.php`](file:///c:/xampp/htdocs/BsitSP/db_connection.php):

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_profiling";
```

If your local database uses different credentials or a different database name, update these values to match your environment.

#### 5. Run the application

1. Ensure Apache and MySQL are running.
2. Open your browser and go to:

   ```text
   http://localhost/BsitSP/index.php
   ```

3. You should see the login page.

### Default Accounts

After importing `db/student_profiling.sql`, you will have:

- **Admin account**
  - Username: `admin`
  - Password: `admin123`

- **Student accounts**
  - Sample student records are included in the `students` table (see the SQL file).
  - You can also create a new student using the registration flow (`register.php`).

> For real deployments, change the admin password and avoid using `root` with an empty password in production.

### Project Structure (Key Files)

- `index.php` – Combined login for admin and students.
- `home.php` – Student home/dashboard after login.
- `register.php` – Student registration.
- `enroll.php` – First‑time student enrollment / information filling.
- `myProfile.php` – Student profile view page with avatar, bio, and dynamic details.
- `updateProfile.php` – Student profile edit page.
- `studentAnnouncement.php` – Student announcement listing.
- `adminDashboard.php` – Admin landing page.
- `adminSideBar.php` – Admin navigation/sidebar UI.
- `studentManagement.php` – Student list and management actions.
- `viewStudent.php`, `editStudent.php`, `deleteStudent.php` – View and manage an individual student.
- `createForm.php` – Admin page to create and configure dynamic form fields.
- `adminAnnouncement.php`, `editAnnouncement.php`, `deleteAnnouncement.php` – Admin announcement management.
- `generateReport.php`, `generateAllStudentsReport.php` – Reporting pages.
- `db_connection.php` – Shared database connection.
- `db/student_profiling.sql` – Database schema and sample data.
- `css/` – Stylesheets for each major page (e.g. `home.css`, `myProfile.css`, `updateProfile.css`).

### Customizing Dynamic Fields

Admins can customize the information students must fill in using the **Create Form** feature:

- Field definitions are stored in the `form_fields` table.
- Student answers are stored in the `student_details` table.
- The profile pages read from these tables to render:
  - Additional details on `myProfile.php`.
  - Editable fields on `updateProfile.php`.

Use `createForm.php` in the admin portal to add, update, or lock fields without changing PHP code.

### Development Notes

- The application assumes a local development setup; if you deploy to a server:
  - Use strong database credentials.
  - Change the default admin password.
  - Configure HTTPS and appropriate session/security settings.
- A high‑level flow diagram for the system is included in `flowchart.drawio`.
- Bugs or to‑dos can be tracked in the text files such as `bugs.txt` and `notes.txt`.

### License

This project does not currently specify a license. Add a license section here if you decide to open‑source or share it.

