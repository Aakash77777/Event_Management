<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Royal Events</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <h1>WELCOME TO ROYAL EVENTS</h1>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="venues.php">Venues</a></li>
            <li><a href="aboutus.php">About Us</a></li>
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
    <section class="about-container">
        <div class="about-content">
            <h2>About Royal Events</h2>
            <p>Royal Events is dedicated to creating unforgettable experiences. From concerts and festivals to private gatherings, we make every moment special.</p>
        </div>

        <div class="about-sections">
            <div class="section">
                <h3>Our Mission</h3>
                <p>To provide high-quality event management services that bring people together and create lasting memories.</p>
            </div>
            <div class="section">
                <h3>Our Vision</h3>
                <p>To be the leading event management company, known for innovation and excellence.</p>
            </div>
            <div class="section">
                <h3>Our Goals</h3>
                <p>We aim to organize seamless, world-class events that exceed client expectations.</p>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="footer-container">
        <div class="footer-left">
            <h3>Plan, Book, Celebrate</h3>
            <div class="social-icons">
      <a href="https://facebook.com" target="_blank">
        <img src="/frontend/photos/Facebook_Logo_2023.png" alt="Facebook">
      </a>
      <a href="https://twitter.com" target="_blank">
        <img src="/frontend/photos/twitter.png" alt="Twitter">
      </a>
      <a href="https://instagram.com" target="_blank">
        <img src="/frontend/photos/insta.png" alt="Instagram">
      </a>
    </div>

            <p>&copy; 2025 Royal Events. All rights reserved.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-middle">
            <h3>Company</h3>
            <ul>
                <li><a href="aboutus.php">About Us</a></li>
                <li><a href="#">Mission</a></li>
                <li><a href="#">Vision & Goals</a></li>
            </ul>
        </div>

        <div class="footer-right">
            <h3>Contact Us</h3>
            <p><i class="fas fa-map-marker-alt"></i> Pasikot, Budhanilkantha, Kathmandu, Nepal</p>
            <p><i class="fas fa-phone"></i> +977-9864791919, +977-9865791919</p>
            <p><i class="fas fa-envelope"></i> RoyalEvents@gmail.com</p>
        </div>
    </div>

    <div class="developer">
        <p>Designed & Developed by <span class="developer-name">Royalist&Co</span></p>
    </div>
</footer>

</body>
</html>
<style> 
    /* About Us Page */
.about-container {
    text-align: center;
    padding: 50px 20px;
}

.about-content {
    max-width: 800px;
    margin: auto;
    font-size: 18px;
}

.about-sections {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 30px;
}

.section {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    width: 30%;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
/* Social Media Icons */
.social-icons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.social-icons a {
    display: inline-block;
    transition: transform 0.3s ease;
}

.social-icons img {
    width: 40px; /* Adjust size as needed */
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.social-icons a:hover {
    transform: scale(1.1);
}


/* Footer */
.footer-container {
    display: flex;
    justify-content: space-between;
    padding: 40px;
    background-color: #f8f9fa;
    color: #333;
}

.footer-left, .footer-middle, .footer-right {
    width: 30%;
}

.footer-middle ul {
    list-style: none;
    padding: 0;
}

.footer-middle ul li {
    margin-bottom: 10px;
}

.footer-middle ul li a {
    text-decoration: none;
    color: #333;
}

.footer-right p {
    margin-bottom: 10px;
}

/* Developer Section */
.developer {
    text-align: center;
    padding: 10px;
    background:rgb(64, 67, 70);
}

.developer-name {
    font-weight: bold;
    color:rgb(255, 255, 255);
}

</style>