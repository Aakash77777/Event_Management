<?php
session_start();

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../frontend/login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#" onclick="loadPage('dashboard.php')"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#" onclick="loadPage('users.php')"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#" onclick="loadPage('reviews.php')"><i class="fas fa-star"></i> Reviews</a></li>

            <!-- Reports with Submenu -->
            <li class="has-submenu">
                <a class="submenu-toggle"><i class="fas fa-chart-line"></i> Reports <i class="fas fa-caret-down" style="margin-left:auto;"></i></a>
                <ul class="submenu">
                    <li><a href="#" onclick="loadPage('event_reports.php')"><i class="fas fa-calendar-alt"></i> Event Reports</a></li>
                    <li><a href="#" onclick="loadPage('venue_reports.php')"><i class="fas fa-map-marker-alt"></i> Venue Reports</a></li>
                </ul>
            </li>

            <li><a href="../frontend/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
            <div class="user-profile">
                <img src="../frontend/photos/cr7.jpg" alt="Admin">
                <span>Admin <?php echo htmlspecialchars($username); ?></span>
            </div>
        </header>

        <div id="content-area">
            <p>Welcome to the Admin Dashboard. Select an option from the sidebar.</p>
        </div>
    </div>

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

        // Handle dynamic page load
        document.querySelectorAll('.sidebar a').forEach(link => {
            if (link.getAttribute('onclick')) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pageUrl = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    loadPage(pageUrl);

                    // Handle active state
                    document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            }
        });
    });

    function loadPage(pageUrl) {
        fetch(pageUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Page not found.');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('content-area').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('content-area').innerHTML = "<p>Error loading page.</p>";
                console.error(error);
            });
    }
    </script>

</body>
<style>
       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color:rgb(49, 66, 85);
            color: white;
            padding-top: 30px;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar h2 {
           text-align: center;
           font-size: 24px;
            margin-bottom: 30px;
           color: white; /* This line ensures the text is white */
           }


        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px 25px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .sidebar ul li a:hover {
            background-color:  rgb(49, 66, 85);
            border-radius: 5px;
        }

        /* Submenu styles */
        .has-submenu > a {
            justify-content: space-between;
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
            padding: 10px 25px;
            font-size: 14px;
            background:rgb(49, 66, 85);
            border-radius: 5px;
            margin-top: 5px;
        }

        .submenu li a:hover {
            background:rgb(70, 79, 90);
        }

        /* Main content */
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header {
            background:  rgb(49, 66, 85); /* Light blue background */
            padding: 15px 25px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 4px rgba(255, 254, 254, 0.1);
        }

        header h1 {
            color:rgb(255, 255, 255);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solidrgb(255, 255, 255);
        }

        .user-profile span {
            color:rgb(255, 255, 255);
            font-weight: 600;
        }

        #content-area {
            margin-top: 25px;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar ul li a.active {
            background:rgb(95, 111, 128);
            border-radius: 5px;
        }

    </style>
</html>
