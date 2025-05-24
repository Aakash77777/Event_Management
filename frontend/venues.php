<?php
session_start();
include 'db_connect.php';

// Fetch venues
$venues_result = $conn->query("SELECT * FROM venues");

// Fetch food items
$foods_result = $conn->query("SELECT * FROM foods");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Venues | Royal Events</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Global and layout styles */
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

  <!-- Chatbot Button and Frame -->
  <button id="chatbot-btn">
    <img src="/frontend/photos/chatbot.jpg" alt="Chatbot">
  </button>
  <div id="chatbot-frame">
    <iframe src="chat.php" width="100%" height="100%" style="border: none; border-radius: 12px; background: white;"></iframe>
  </div>

  <script>
    document.getElementById('chatbot-btn').addEventListener('click', () => {
      const frame = document.getElementById('chatbot-frame');
      frame.style.display = frame.style.display === 'block' ? 'none' : 'block';
    });
  </script>

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
        <?php if ($venues_result->num_rows > 0): ?>
          <?php while ($venue = $venues_result->fetch_assoc()): ?>
            <div class="venue">
              <img src="/frontend/photos/<?php echo htmlspecialchars($venue['image']); ?>" alt="<?php echo htmlspecialchars($venue['venue_name']); ?>">
              <h3><?php echo htmlspecialchars($venue['venue_name']); ?></h3>
              <p><strong>Location:</strong> <?php echo htmlspecialchars($venue['location']); ?></p>
              <p><?php echo htmlspecialchars($venue['description']); ?></p>
              <p><strong>Price Per Guest:</strong>
                <?php echo $venue['price_per_person'] ? 'Rs. ' . htmlspecialchars($venue['price_per_person']) : 'Not Set'; ?>
              </p>
              <button onclick="openBookingForm(<?php echo $venue['id']; ?>)">Book Now</button>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No venues available at the moment.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Booking Modal -->
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
                  <input type="radio" name="food_id" value="<?php echo $food['id']; ?>" id="food_<?php echo $food['id']; ?>" required>
                  <label for="food_<?php echo $food['id']; ?>">
                    <img src="uploads/foods/<?php echo htmlspecialchars($food['picture']); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>">
                    <?php echo htmlspecialchars($food['name']); ?>
                  </label>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No food available</p>
            <?php endif; ?>
          </div>

          <label for="guests">Number of Guests</label>
          <input type="number" name="guests" id="guests" min="1" required>

          <button type="submit">Confirm Booking</button>
        </form>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Royal Event. All rights reserved. For support: 📱 +977 9864791919 📧 RoyalEvents@gmail.com</p>
  </footer>

  <script>
    function openBookingForm(venueId) {
      const dateInput = document.getElementById('booking-date');
      document.getElementById('venue-id').value = venueId;
      dateInput.value = '';
      document.getElementById('booking-form-modal').style.display = 'block';

      fetch(`get_booked_dates.php?venue_id=${venueId}`)
        .then(res => res.json())
        .then(data => {
          const bookedDates = data.bookedDates;
          const newInput = dateInput.cloneNode(true);
          dateInput.parentNode.replaceChild(newInput, dateInput);
          newInput.id = 'booking-date';
          newInput.name = 'booking_date';
          newInput.required = true;

          newInput.addEventListener('input', function () {
            if (bookedDates.includes(this.value)) {
              alert("This date is already booked for the selected venue.");
              this.value = '';
            }
          });
        });
    }

    function closeBookingForm() {
      document.getElementById('booking-form-modal').style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target === document.getElementById('booking-form-modal')) {
        closeBookingForm();
      }
    };
  </script>

</body>
</html>


<style>
    /* Red Book Now Button */
.venue button {
    background-color: #dc2626; /* Tailwind red-600 */
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.venue button:hover {
    background-color: #b91c1c; /* Tailwind red-700 */
}


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

.food-item {
  display: flex;
  align-items: center;      /* Vertically center text */
  gap: 1rem;
  background-color: #f3f4f6;
  padding: 0.75rem 1rem;
  border-radius: 8px;
}

.food-item img {
  width: 100px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
}

.food-item label {
  display: flex;
  align-items: center;      /* Align text and radio button vertically */
  gap: 0.5rem;
  font-weight: 500;
  color: #1f2937;
  margin: 0;
}


</style> 
