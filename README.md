# SESTA School Registration System

This comprehensive PHP-based web application manages student, teacher, and admin information, class assignments, billing, and reports for Sekolah Menengah Sains Tapah. It includes user authentication, role-based access, and detailed record management with a user-friendly interface enhanced by interactive features, responsive design, and PDF report generation.

## Table of Contents

- [Project Overview](#project-overview)
- [Project Requirements](#project-requirements)
- [Dependencies](#dependencies)
- [Getting Started](#getting-started)
- [Running the Application](#running-the-application)
- [Code Examples](#code-examples)
- [Project Structure](#project-structure)
- [Conclusion](#conclusion)


## Project Overview

SESTA is a school registration and management platform designed for admins, teachers, and students to interact securely and efficiently. Core features include:

- User Authentication & Role Management (Admin, Teacher, Student)
- Admin Modules: Manage classes, students, teachers, billing, and generate reports.
- Teacher Modules: View assigned classes, manage students, and view billing.
- Student Modules: Register, view profile, check billing, upload payment receipts, and access student card.
- Interactive Dashboards with summary charts
- PDF report generation and email capabilities for invoices and summaries
- File upload with validation (payment receipts)
- CAPTCHA for security during login

## Project Requirements

To deploy or contribute to SESTA, ensure the following prerequisites:

- PHP 7.0 or higher with MySQLi and GD extensions enabled
- MySQL/MariaDB database server
- Web server capable of running PHP (Apache, Nginx, etc.)
- Composer (optional) if using external dependencies
- TCPDF library for PDF generation (included in `tcpdf/`)
- PHPMailer library for sending emails
- Internet connectivity for loading external CSS/JS (e.g., Bootstrap, SweetAlert, Boxicons CDN)

## Dependencies

The system utilizes several third-party libraries and services:

- **TCPDF** - For PDF creation (generating reports and billing invoices)
- **PHPMailer** - For email sending of invoices and notification
- **SweetAlert2** - For user-friendly alert modals and confirmations
- **Boxicons** - Icon fonts used in headers and navigation
- **Bootstrap** - Responsive UI components and layout (used mainly in billing pages)
- **jQuery** - DOM manipulation and AJAX calls

All needed CSS and JS libraries are linked via CDN or included within `/css` folder.

## Getting Started

The core setup steps after cloning the repository:

1. **Database Setup:**

   - Import the SQL schema (not provided here, but expected as part of `/db` or setup folder) to create tables: `admin`, `teacher`, `student`, `class`, `payment`, `parent`, etc.
   - Update database credentials in `php/config.php`:

   ```php
   $servername = "your-db-host:port";
   $username = "your-db-user";
   $password = "your-db-password";
   $db = "your-db-name";

   $con = mysqli_connect($servername, $username, $password, $db);
   if (!$con) {
       die("Connection failed: " . mysqli_connect_error());
   }
   ```

2. **File Permissions:**
   - Ensure the `uploads/` directory is writable for file uploads (payment receipts).
   - The `pdf/` folder should be writable if server-side PDF generation saves files.

3. **PHPMailer Configuration:**
   - Configure SMTP credentials in PHPMailer initialization within `/student/billing/pdf_maker.php` (and similar scripts) if email sending is needed.

4. **Session & User Authentication:**
   - Users must login via `/php/login-logout/login.php` per role.
   - Verify CAPTCHA on teacher and student logins.

## Running the Application

- Access the system via the webroot, typically:

  - Admin: `php/admin/admin_home.php`
  - Teacher: `php/teacher/teacher_home.php`
  - Student: `php/student/student_home.php`

- Use the login page `php/login-logout/login.php` where users select their role and provide credentials.

- Admin features include managing Classes, Admins, Teachers, Students, and Billing through intuitive interfaces.

- Teachers can view and update information related to their assigned class and students.

- Students can view their profile, billing info, upload payment proof, and print/download payment receipts.

## Code Examples

### Inserting a New Admin (AJAX submission with form validation)

```javascript
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('adminform');
    const saveButton = document.getElementById('save');

    saveButton.addEventListener('click', function (event) {
        event.preventDefault();
        const formData = new FormData(form);

        if (formData.get('pwd') !== formData.get('pwd2')) {
            Swal.fire({
                title: 'Password Mismatch',
                text: 'Passwords do not match, please re-enter.',
                icon: 'error',
                confirmButtonColor: '#FF0004',
            });
            return;
        }

        fetch('insert_admin.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Admin Registered',
                        text: 'New admin added successfully.',
                        icon: 'success',
                        confirmButtonColor: '#4caf50',
                    }).then(() => window.location.href = 'adminList.php');
                } else {
                    Swal.fire({
                        title: 'Admin ID Exist',
                        text: 'Admin already exists.',
                        icon: 'error',
                        confirmButtonColor: '#FF0004',
                    });
                }
            }).catch(console.error);
    });
});
```

### Handling Payment Status Update via AJAX

```javascript
function updateFunction(button) {
    const paymentID = button.getAttribute('data-payment-id');
    const selectedStatus = $(button).closest('tr').find('select[name="selectStatus"]').val();

    $.ajax({
        type: "POST",
        url: "update_payment_status.php",
        data: { paymentID: paymentID, status: selectedStatus },
        success: function (response) {
            if (response.trim() === 'success') {
                Swal.fire({
                    title: "Update!",
                    text: "Payment status updated successfully.",
                    icon: "success"
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    title: "Error!",
                    text: "Error updating payment status.",
                    icon: "error"
                });
            }
        }
    });
}
```

### SQL Update Query Example (Update Admin Data)

```php
$updateQuery = "UPDATE admin SET  
    ADMIN_NAME='$fullname', 
    ADMIN_PHONE='$phoneN', 
    ADMIN_PWD='$pswd' 
    WHERE ADMIN_ID='$oldID'";

if (mysqli_query($con, $updateQuery)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
```

## Project Structure

- **php/admin** - Admin panel: manage students, teachers, classes, billing, reports.
- **php/teacher** - Teacher portal: class info, student lists, billing.
- **php/student** - Student portal: registration, billing, student cards.
- **php/login-logout** - Authentication and user management including password reset, verification, and account creation.
- **php/header** - Common header and footer includes for different user roles.
- **php/student/billing** - Billing related pages and PDF generation.
- **uploads/** - File upload storage for payment receipts.
