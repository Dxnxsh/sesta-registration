<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize admin session ID
$adminId = SecuritySanitizer::sanitize($_SESSION['adminID'], 'id', 'ADMIN_ID');
if (!$adminId) {
    SecuritySanitizer::logSecurityEvent('Invalid admin session ID in adminList.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}

?>
<?php include "../header/adminHeader.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Admin List</title>
    <style>
        body {
            background-image: url("../../image/admin.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .button {
            background-color: #04AA6D;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            margin: 10px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        #form2 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 10px;
        }

        #searchBox {
            margin-left: auto;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            align-content: center;
        }

        th {
            background-color: #04AA6D;
            color: white;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        button {
            padding: 8px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .manage-buttons a {
            display: inline-block;
            background-color: #04AA6D;
            color: white;
            padding: 8px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            margin: 4px;
            transition: background-color 0.3s;
            width: 60%;
        }

        .manage-buttons a.view-button {
            background-color: #007BFF;
            width: fit-content;
        }

        .manage-buttons a.update-button {
            background-color: #28A745;
            width: fit-content;
        }

        .manage-buttons a.delete-button {
            background-color: #DC3545;
            width: fit-content;
        }

        .manage-buttons a.back-button {
            background-color: #007BFF;
            width: fit-content;
            margin-top: 30px;
        }

        @keyframes buttonHover {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0);
            }
        }

        a.reset-button {
            background-color: #0072ffc2;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }

        a.reset-button:hover  {
            background-color: #DC3545;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }
        .manage-buttons a:hover {
            animation: buttonHover 0.3s ease;
            opacity: 0.9;
        }

        h1 {
            font-size: 40px;
            color: black;
            margin-bottom: 10px;
            text-align: center;
        }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-container img {
            margin-right: 10px;
            cursor: pointer;
        }

        #searchBox {
            width: fit-content;
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: aliceblue;
        }

        #submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }

        #submit:hover {
            background-color: #45a049;
        }
    </style>
</head>
<?php
include("../config.php");

// Handle admin deletion with proper sanitization
if (isset($_GET['adminID'])) {
    $deleteAdminID = SecuritySanitizer::sanitize($_GET['adminID'], 'id', 'ADMIN_ID');
    
    if (!$deleteAdminID) {
        SecuritySanitizer::logSecurityEvent("Invalid admin ID for deletion by admin $adminId", 'MEDIUM');
    } else {
        // Check if the admin being deleted is the same as the currently logged-in admin
        if ($adminId == $deleteAdminID) {
            SecuritySanitizer::logSecurityEvent("Admin $adminId attempted to delete themselves", 'MEDIUM');
            echo '<script>
                    Swal.fire({
                        title: "Cannot Delete Yourself",
                        text: "You cannot delete your own admin account.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                  </script>';
        } else {
            // Use prepared statements for deletion
            $deleteStmt = $con->prepare("DELETE FROM admin WHERE ADMIN_ID = ?");
            $deleteStmt->bind_param("s", $deleteAdminID);
            
            if ($deleteStmt->execute()) {
                // Update related records
                $updateStmt1 = $con->prepare("UPDATE class SET ADMIN_ID = NULL WHERE ADMIN_ID = ?");
                $updateStmt1->bind_param("s", $deleteAdminID);
                $updateStmt1->execute();
                $updateStmt1->close();
                
                $updateStmt2 = $con->prepare("UPDATE teacher SET ADMIN_ID = NULL WHERE ADMIN_ID = ?");
                $updateStmt2->bind_param("s", $deleteAdminID);
                $updateStmt2->execute();
                $updateStmt2->close();
                
                SecuritySanitizer::logSecurityEvent("Admin $deleteAdminID deleted by admin $adminId", 'INFO');
            } else {
                SecuritySanitizer::logSecurityEvent("Failed to delete admin $deleteAdminID by admin $adminId", 'HIGH');
            }
            $deleteStmt->close();
        }
    }
}

// Handle search with proper sanitization
$searchTerm = '';
if (isset($_GET['searchBox'])) {
    $searchTerm = trim($_GET['searchBox']);
    $searchTerm = SecuritySanitizer::sanitize($searchTerm, 'id', 'ADMIN_ID');
    
    if ($searchTerm) {
        SecuritySanitizer::logSecurityEvent("Admin $adminId searched for admin: $searchTerm", 'INFO');
        $stmt = $con->prepare("SELECT * FROM admin WHERE ADMIN_ID LIKE ?");
        $searchPattern = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchPattern);
        $stmt->execute();
        $query = $stmt->get_result();
    } else {
        SecuritySanitizer::logSecurityEvent("Invalid admin search term by admin $adminId", 'MEDIUM');
        $query = $con->query("SELECT * FROM admin WHERE 1=0");
    }
} else {
    // Default: show all admins
    $query = $con->query("SELECT * FROM admin");
}
?>
<body>
    <div class="container">
    <form id="form2" name="form2" method="get">
            <h1>Registered Admin</h1>
            <div class="search-container">
                <input name="searchBox" type="text" id="searchBox" placeholder="Search admin id">
                <input name="submit" type="submit" id="submit" value="Search">
                <a class="reset-button" href="adminList.php">Show All</a>
            </div>
        </form>
        <form id="form1" name="form1" method="post">
            <p>
                <input class="button" type="submit" name="submit2" id="submit2" formaction="AddNewAdmin.php"
                    value="Add New admin">
            </p>
            <table width="163%">
                <tr>
                    <th>ID</th>
                    <th>USERNAME</th>
                    <th>NAME</th>
                    <th>CONTACT NO</th>
                    <th colspan="4">MANAGE</th>
                </tr>
                <?php
                $num = $query->num_rows;
                if ($num > 0) {
                    while ($result = $query->fetch_assoc()) {
                        // Sanitize all outputs for XSS protection
                        $adminIdOutput = SecuritySanitizer::sanitize($result["ADMIN_ID"], 'id');
                        $adminUsername = SecuritySanitizer::sanitize($result["ADMIN_USERNAME"], 'username');
                        $adminName = SecuritySanitizer::sanitize($result["ADMIN_NAME"], 'name');
                        $adminPhone = SecuritySanitizer::sanitize($result["ADMIN_PHONE"], 'phone');
                        
                        echo "
                    <tr>
                        <td>" . htmlspecialchars($adminIdOutput, ENT_QUOTES, 'UTF-8') . "</td>
                        <td>" . htmlspecialchars($adminUsername, ENT_QUOTES, 'UTF-8') . "</td>
                        <td>" . htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') . "</td>
                        <td colspan='3'>" . htmlspecialchars($adminPhone, ENT_QUOTES, 'UTF-8') . "</td>
                        <td class='manage-buttons'><a class='update-button' href='AdminUpdateAdmin.php?id=" . htmlspecialchars($adminIdOutput, ENT_QUOTES, 'UTF-8') . "'>UPDATE</a></td>
                        <td class='manage-buttons'><a class='delete-button' onclick='confirmDelete(\"" . htmlspecialchars($adminIdOutput, ENT_QUOTES, 'UTF-8') . "\")'>DELETE</a></td>
       
                        </tr>";
                    }
                }
                ?>    
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button'  href='Admin_home.php'>Go Back</a></div>
      
    </div>
      
    <script>
        function confirmDelete(adminID) {
    Swal.fire({
        title: 'Are you sure',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'adminList.php?adminID=' + adminID; // Change 'id' to 'adminID'
        }
    });
}

    </script>

</body>

</html>
<?php include "../header/footer.php" ?>