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
            margin: 0;
            padding: 30px;
            font-family: 'Segoe UI', sans-serif;
            color: #f0f0f0;
        }

        .dashboard-cards {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            width: 260px;
            border-radius: 18px;
            padding: 30px 20px;
            background: linear-gradient(145deg, #f0f0f0, #e0e0e0);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: #2c2c2c;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.3);
        }

        .card .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .card .label {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card .value {
            font-size: 28px;
            font-weight: bold;
        }

        .card.blue { border-top: 6px solid #2196f3; }
        .card.yellow { border-top: 6px solid #ffeb3b; }
        .card.purple { border-top: 6px solid #9c27b0; }
        .card.green { border-top: 6px solid #4caf50; }

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
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.3);
            color: #333;
        }

        .table-container h3 {
            font-size: 24px;
            margin-bottom: 25px;
            color: #2c3e50;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        th, td {
            padding: 14px 20px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
            color: #333;
        }

        tr:hover {
            background-color: #f3f3f3;
        }

        th, td {
            width: 50%;
        }
    </style>
</head>
<body>

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
