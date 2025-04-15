<?php
session_start();
include 'db_connect.php';

// Fetch venues from the database
$sql = "SELECT * FROM venues";
$result = $conn->query($sql);

// Fetch food items for booking form
$foods_sql = "SELECT * FROM foods";
$foods_result = $conn->query($foods_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black with transparency */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Royal Events</h1>
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
        <section class="venues">
            <h2>Our Venues</h2>
            <p>Explore our top event venues for your next occasion.</p>
            <div class="venue-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="venue">
                            <img src="/frontend/photos/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['venue_name']); ?>">
                            <h3><?php echo htmlspecialchars($row['venue_name']); ?></h3>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                            <button onclick="openBookingForm(<?php echo $row['id']; ?>)">Book Now</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No venues available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Booking Form Modal -->
        <div id="booking-form-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeBookingForm()">&times;</span>
                <h3>Book Venue</h3>
                <form id="booking-form" method="POST" action="book_venue.php">
                    <input type="hidden" name="venue_id" id="venue-id">
                    <label for="booking-date">Booking Date:</label>
                    <input type="date" name="booking_date" id="booking-date" required>

                    <label for="food">Select Food:</label>
                    <div id="food-checkboxes">
                        <?php if ($foods_result->num_rows > 0): ?>
                            <?php while ($food = $foods_result->fetch_assoc()): ?>
                                <div class="food-item">
                                    <input type="checkbox" name="food_ids[]" value="<?php echo $food['id']; ?>" id="food_<?php echo $food['id']; ?>">
                                    <label for="food_<?php echo $food['id']; ?>"><?php echo htmlspecialchars($food['name']); ?> Rs. <?php echo $food['price']; ?></label>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No food available</p>
                        <?php endif; ?>
                    </div>

                    <button type="submit">Confirm Booking</button>
                </form>
            </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com.</p>
    </footer>

    <script>
        // Function to open the booking form modal
        function openBookingForm(venueId) {
            document.getElementById('venue-id').value = venueId;
            document.getElementById('booking-form-modal').style.display = 'block';
        }

        // Function to close the booking form modal
        function closeBookingForm() {
            document.getElementById('booking-form-modal').style.display = 'none';
        }

        // Close the modal if the user clicks outside of the modal content
        window.onclick = function(event) {
            var modal = document.getElementById('booking-form-modal');
            if (event.target == modal) {
                closeBookingForm();
            }
        }
    </script>
</body>
</html>
<style> 
    /* Modal Overlay */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(17, 24, 39, 0.7); /* dark semi-transparent bg */
    backdrop-filter: blur(3px);
}

/* Modal Content */
.modal-content {
    background-color: #ffffff;
    margin: 6% auto;
    padding: 2rem;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: relative;
}

/* Close Button */
.modal-content .close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    color: #6b7280;
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.2s;
}
.modal-content .close:hover {
    color: #111827;
}

/* Heading */
.modal-content h3 {
    text-align: center;
    color: #1f2937;
    margin-bottom: 1.5rem;
    font-size: 1.6rem;
}

/* Form Styling */
#booking-form label {
    display: block;
    margin-top: 1rem;
    margin-bottom: 0.4rem;
    font-weight: 600;
    color: #374151;
}

#booking-form input[type="date"] {
    width: 100%;
    padding: 0.7rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background-color: #f9fafb;
    font-size: 1rem;
}

/* Food Checkbox List */
#food-checkboxes {
    margin-top: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.food-item {
    background-color: #f3f4f6;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.food-item label {
    font-weight: 500;
    color: #1f2937;
}

/* Submit Button */
#booking-form button {
    margin-top: 1.5rem;
    width: 100%;
    padding: 0.75rem;
    background-color: #1e3a8a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
#booking-form button:hover {
    background-color: #2563eb;
}
   /* reviews */ 

.give-reviews {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100px;
  margin: 0 auto;
  text-align: center;
}

.give-reviews a {
  text-decoration: none;
  color: #fff;
  background-color:rgb(0, 66, 198);
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.give-reviews a:hover {
  background-color:rgb(131, 53, 196);
}


</style>
