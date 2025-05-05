<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_reviews = $conn->query("SELECT COUNT(*) AS total FROM reviews")->fetch_assoc()['total'];
$total_event_reports = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$total_venue_reports = $conn->query("SELECT COUNT(*) AS total FROM venue_booking")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #121212; /* Dark background */
            padding: 30px;
            margin: 0;
            color: #fff;
        }

        h2 {
            text-align: center;
            font-size: 28px;
            color: #ffffff;
            margin-bottom: 30px;
        }

        .dashboard-cards {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            width: 250px;
            background-color: #ffffff; /* Light card */
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            text-align: center;
            transition: transform 0.3s ease;
            color: #333;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card .icon {
            font-size: 36px;
            margin-bottom: 15px;
        }

        .card .label {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card .value {
            font-size: 24px;
            font-weight: bold;
        }

        .card.blue {
            border-top: 6px solid #1e88e5;
        }

        .card.yellow {
            border-top: 6px solid #fbc02d;
        }

        .card.purple {
            border-top: 6px solid #ab47bc;
        }

        .card.green {
            border-top: 6px solid #43a047;
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 90%;
            }
        }

        .table-container {
            max-width: 700px;
            margin: 40px auto;
            background: #ffffff; /* Light table background */
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            color: #333;
            text-align: center;
        }

        .table-container h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        th, td {
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left; /* Ensuring text alignment to the left */
        }

        th {
            background-color: #f5f5f5;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        /* Ensuring equal column width */
        th, td {
            width: 50%;
        }
    </style>
</head>
<body>

<h2>Admin Dashboard</h2>

<div class="dashboard-cards">
    <div class="card blue">
        <div class="icon">👤</div>
        <div class="label">Total Users</div>
        <div class="value"><?= $total_users ?></div>
    </div>
    <div class="card yellow">
        <div class="icon">📝</div>
        <div class="label">Reviews</div>
        <div class="value"><?= $total_reviews ?></div>
    </div>
    <div class="card purple">
        <div class="icon">📊</div>
        <div class="label">Event Reports</div>
        <div class="value"><?= $total_event_reports ?></div>
    </div>
    <div class="card green">
        <div class="icon">🏢</div>
        <div class="label">Venue Bookings</div>
        <div class="value"><?= $total_venue_reports ?></div>
    </div>
</div>

<div class="table-container">
    <h3>Data Overview Table</h3>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Users</td>
                <td><?= $total_users ?></td>
            </tr>
            <tr>
                <td>Total Reviews</td>
                <td><?= $total_reviews ?></td>
            </tr>
            <tr>
                <td>Event Reports</td>
                <td><?= $total_event_reports ?></td>
            </tr>
            <tr>
                <td>Venue Bookings</td>
                <td><?= $total_venue_reports ?></td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
