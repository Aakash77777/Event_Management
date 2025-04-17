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
<<header>
    <div class="logo-title">
        <img src="/frontend/photos/Royal Events.png" alt="Royal Events Logo" class="logo">
        <h1>WELCOME TO ROYAL EVENTS</h1>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="venues.php">Venues</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="reviews.php">Review</a></li>
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
            <div class="explore-events">
              <a href="events.php">Explore Events</a>
            </div>

            <section class="event-list"> 
                <div class="event"> 
                    <img src="/frontend/photos/arijit.jpg" alt="Arijit">
                    <h4>Arijit Singh Live in Nepal </h4>
                </div>

                <div class="event"> 
                    <img src="/frontend/photos/avishek.jpg" alt="Avishek">
                    <h4>Toxic Show 2 </h4>
                </div>

                <div class="event"> 
                    <img src="/frontend/photos/goldcup.jpg" alt="goldcup">
                    <h4> 13th Jeetputs GoldCup </h4>
                </div>

                <div class="event"> 
                    <img src="/frontend/photos/grasslands.jpg" alt="grasslands">
                    <h4>Grasslands Carnival 2024 </h4>
                </div>

                <div class="event"> 
                    <img src="/frontend/photos/aakash.jpg" alt="Aakash">
                    <h4>Aakash Mehta Live in Nepal </h4>
                </div>

                <div class="event"> 
                    <img src="/frontend/photos/dashain.jpg" alt="dashain">
                    <h4>Dashain Music Jatra </h4>
                </div>
                </section>

                
                <section class="Our_Venues">
                <h2>Our Venues</h2>
<p>Check out the latest venues near you. Book our venues for any kind of event.</p>

<div class="venue-list">
    <div class="venue">
        <a href="venues.php">
            <img src="/frontend/photos/grandhall.jpg" alt="Grand Hall">
        </a>
        <h3><a href="venues.php">Grand Hall</a></h3>
        <p>A luxurious venue for weddings, concerts, and corporate events.</p>
    </div>

    <div class="venue">
        <a href="venues.php">
            <img src="/frontend/photos/infinitylounge.jpg" alt="Infinity Lounge">
        </a>
        <h3><a href="venues.php">Infinity Lounge</a></h3>
        <p>Perfect for private parties and live music performances.</p>
    </div>

    <div class="venue">
        <a href="venues.php">
            <img src="/frontend/photos/royalhall.jpg" alt="Royal Hall">
        </a>
        <h3><a href="venues.php">Royal Hall</a></h3>
        <p>Traditional hall with modern amenities for special events.</p>
    </div>

    <div class="venue">
        <a href="venues.php">
            <img src="/frontend/photos/sunsetresort.jpg" alt="Sunset Resort">
        </a>
        <h3><a href="venues.php">Sunset Resort</a></h3>
        <p>Luxury resort with beachfront access for grand celebrations.</p>
    </div>
</div>


            </div>
            <div class="Know about us? Click here">
              <a href="aboutus.php"> About Us/Follow Us.</a>
            </div>


        </section>
    </main>
    <footer>
        <p>&copy; 2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com. </p>
    </footer>
</body>
</html>