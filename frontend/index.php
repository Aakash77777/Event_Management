<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>WELCOME TO ROYAL EVENTS</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="venues.php">Venues</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="upcoming-events">
            <h2>Upcoming Events</h2>
            <p>Check out the latest events happening near you.</p>
            <div class="event-list">
                <div class="event">
                    <img src="/frontend/photos/holii.jpg" alt="Kuma Sagars concert">
                    <h3>Holi</h3>
                    <p>Join us for an amazing Holi Events and concerts.</p>
                </div>
                <div class="event">
                    <img src="/frontend/photos/bipul.jpg" alt="Bipul Chhetri and Pharaoh">
                    <h3>Bipul Chhetri and Pharaoh</h3>
                    <p>Join us for an amazing music concert.</p>
                </div>
                <div class="event">
                    <img src="/frontend/photos/ucl.jpg" alt="Susant Kc">
                    <h3>Ucl Final </h3>
                    <p>Join us for an amazing Uefa Champions League Final.</p>
                </div>
                <div class="event">
                    <img src="/frontend/photos/rockheads22.jpg" alt="Rockheads">
                    <h3>Rockheads</h3>
                    <p>Join us for an amazing music concert.</p>
                </div>
                <div class="event">
                    <img src="/frontend/photos/oscar.jpg" alt="Oscar">
                    <h3>Oscar</h3>
                    <p>Join us for an amazing Oscar Event, where every actors are choosen by their movies perfomance.</p>
                </div>
                <div class="event">
                    <img src="/frontend/photos/dance.jpg" alt="Leinaz Dance Performances">
                    <h3>Leinaz Dance Performances</h3>
                    <p>Leinaz Dance Performance is a contemporary troupe known for innovative choreography and emotional storytelling, blending tradition with modern styles to captivate audiences worldwide.</p>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Royal Event. All rights reserved.</p>
    </footer>
</body>
</html>