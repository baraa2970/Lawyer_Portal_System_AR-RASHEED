<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Check if the lawyer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lawyer') {
    header("Location: login.php");
    exit();
}

$lawyer_id = $_SESSION['user_id'];
$lawyerName = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jms";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lawyer Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar {
            background-color: #2c3e50;
        }
        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: 600;
        }
        .nav-link:hover {
            color: #18bc9c !important;
        }
        .dashboard-container {
            margin-top: 50px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
            background: white;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-body {
            text-align: center;
            padding: 25px;
        }
        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
        }
        .card-text {
            font-size: 1.4rem;
            font-weight: bold;
            color: #18bc9c;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="dashboard.php">Lawyer Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="cases.php">Cases</a></li>
                <li class="nav-item"><a class="nav-link" href="appointment.php">Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container dashboard-container text-center">
        <h2 class="mb-4">Welcome, <?php echo $lawyerName; ?>!</h2>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Cases</h5>
                        <p class="card-text" id="total_cases">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Appointments</h5>
                        <p class="card-text" id="upcoming_appointments">0</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Tasks</h5>
                        <p class="card-text" id="pending_tasks">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>