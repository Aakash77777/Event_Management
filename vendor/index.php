<?php
session_start();
include '../frontend/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Fetch user role
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $role);
$stmt->fetch();
$stmt->close();

// Restrict access if not vendor
if ($role !== 'Vendor') {
    echo "<script>alert('Access denied. Vendors only!'); window.location.href='../frontend/index.php';</script>";
    exit();
}

// Store username in session for display
$_SESSION['username'] = $username;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="admin_styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background: #1d2b53;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px;
            text-align: left;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
            cursor: pointer;
        }

        .sidebar ul li a:hover {
            background: #263159;
            border-radius: 5px;
        }

        .has-submenu > a {
            cursor: pointer;
        }

        .submenu {
            display: none;
            list-style: none;
            padding-left: 20px;
        }

        .has-submenu.open .submenu {
            display: block;
        }

        .submenu li a {
            background: #263159;
            margin: 5px 0;
            padding: 8px;
            border-radius: 5px;
            font-size: 14px;
            display: block;
            color: white;
        }

        .submenu li a:hover {
            background: #1d2b53;
        }

        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            padding: 20px;
            background: #f5f5f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        iframe {
            flex-grow: 1;
            border: none;
            width: 100%;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .sidebar ul li a.active {
    background: #4c5c9a;
    border-radius: 5px;
}

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Vendor Panel</h2>
    <ul>
    <li><a href="#" onclick="loadPage('dashboard_home.php')"><i class="fas fa-chart-line"></i> Dashboard</a></li>
<li><a href="#" onclick="loadPage('events.php')"><i class="fas fa-calendar"></i> Events</a></li>
<li><a href="#" onclick="loadPage('venues.php')"><i class="fas fa-map-marker-alt"></i> Venues</a></li>
<li><a href="#" onclick="loadPage('bookings.php')"><i class="fas fa-ticket-alt"></i> Event Bookings</a></li>
<li><a href="#" onclick="loadPage('venuebooking.php')"><i class="fas fa-building"></i> Venue Bookings</a></li>
<li><a href="#" onclick="loadPage('foods.php')"><i class="fas fa-utensils"></i> Foods</a></li>

<!-- Reports with submenu -->
<li class="has-submenu">
    <a class="submenu-toggle"><i class="fas fa-file-alt"></i> Reports<i class="fas fa-caret-down" style="float: right;"></i></a>
    <ul class="submenu">
        <li><a href="#" onclick="loadPage('event_reports.php')">Event Reports</a></li>
        <li><a href="#" onclick="loadPage('venue_reports.php')">Venue Reports</a></li>
    </ul>
</li>
        <li><a href="../frontend/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Put header + placeholder inside a wrapper div -->
    <div id="default-dashboard">
        <header>
            <h1>Dashboard</h1>
            <div class="user-profile">
                <img src="../frontend/photos/bipul.jpg" alt="Vendor">
                <span>Vendor <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </header>

        <div class="dashboard-placeholder">
            <p>Welcome to the Vendor Dashboard. Select an option from the sidebar.</p>
        </div>
    </div>

    <!-- This div will load new pages dynamically -->
    <div id="dynamic-content" style="display: none;"></div>

</div>

<!-- Submenu Toggle Script and Dynamic Load -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Handle submenu dropdown toggle
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.classList.toggle('open');
        });
    });
});
// Load pages dynamically into content area
function loadPage(pageUrl) {
    // Hide default dashboard
    document.getElementById('default-dashboard').style.display = 'none';

    // Show dynamic content area
    const dynamicContent = document.getElementById('dynamic-content');
    dynamicContent.style.display = 'block';

    // Load the page
    fetch(pageUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Page not found.');
            }
            return response.text();
        })
        .then(html => {
            dynamicContent.innerHTML = html;
        })
        .catch(error => {
            dynamicContent.innerHTML = "<p>Error loading page.</p>";
            console.error(error);
        });

    // ➡️ Handle active class on sidebar
    const links = document.querySelectorAll('.sidebar ul li a');
    links.forEach(link => {
        link.classList.remove('active'); // remove previous active
    });

    // Find the clicked link and add active class
    const clickedLink = Array.from(links).find(link => link.getAttribute('onclick')?.includes(pageUrl));
    if (clickedLink) {
        clickedLink.classList.add('active');
    }
}

</script>

</body>
</html>
