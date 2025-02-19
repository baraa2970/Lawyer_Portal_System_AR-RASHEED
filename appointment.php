<?php
session_start();

// Add HTTP headers for security
header("X-Frame-Options: DENY");             // Prevent clickjacking
header("X-Content-Type-Options: nosniff");   // Prevent MIME type sniffing
header("X-XSS-Protection: 1; mode=block");   // Enable XSS protection

// Check if the lawyer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lawyer') {
    header("Location: login.php");
    exit();
}

// Database connection details
$host = '127.0.0.1';
$db   = 'jms';
$user = 'root'; // Replace with your database username
$pass = '';     // Replace with your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create a PDO instance (connect to the database)
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' https://code.jquery.com https://stackpath.bootstrapcdn.com; style-src 'self' https://stackpath.bootstrapcdn.com;">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Lawyer Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <style>
        .table-container { margin-top: 20px; }
        .message { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <!-- Header (Navbar) matching the Dashboard -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <a class="navbar-brand" href="dashboard.php">Lawyer Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="appointment.php">Appointments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container">
        <h1 class="mt-4">Appointments</h1>

        <!-- Section to display appointments -->
        <div class="table-container">
            <h3>Upcoming Appointments</h3>
            <?php if ($conn !== null) : ?>
                <?php
                // Prepare the query to fetch appointments using a prepared statement
                $query = "SELECT a_id, client_name, date 
                          FROM appointments 
                          WHERE lawyer_id = ? 
                          ORDER BY date DESC";

                $stmt = $conn->prepare($query);
                if ($stmt) {
                    // Bind the lawyer ID from the session
                    $stmt->bindParam(1, $_SESSION['user_id']);
                    if (!$stmt->execute()) {
                        error_log("Execute failed: " . $stmt->errorInfo()[2]);
                        die("Internal Server Error.");
                    }
                    $result = $stmt->fetchAll();
                } else {
                    error_log("Prepare failed: " . $conn->errorInfo()[2]);
                    die("Internal Server Error.");
                }
                ?>

                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Appointment ID</th>
                            <th>Client Name</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result)) : ?>
                            <?php foreach ($result as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['a_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['client_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <!-- Buttons with data attributes for appointment ID -->
                                    <button class="btn btn-warning btn-sm reschedule-btn" data-id="<?php echo $row['a_id']; ?>">
                                        Reschedule
                                    </button>
                                    <button class="btn btn-danger btn-sm cancel-btn" data-id="<?php echo $row['a_id']; ?>">
                                        Cancel
                                    </button>
                                    <button class="btn btn-success btn-sm agree-btn" data-id="<?php echo $row['a_id']; ?>">
                                        Agree
                                    </button>
                                    <!-- Message placeholder -->
                                    <div class="message" id="message-<?php echo $row['a_id']; ?>"></div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center">No upcoming appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="alert alert-warning">Database connection not available. Please check your database settings.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle Reschedule button click
            $(document).on('click', '.reschedule-btn', function() {
                var appointmentId = $(this).data('id');
                $('#message-' + appointmentId).text('Reschedule request sent for appointment ' + appointmentId).css('color', 'orange');
                alert('Reschedule request sent!');
            });

            // Handle Cancel button click
            $(document).on('click', '.cancel-btn', function() {
                var appointmentId = $(this).data('id');
                $('#message-' + appointmentId).text('Appointment ' + appointmentId + ' has been canceled.').css('color', 'red');
                alert('Appointment canceled!');
            });

            // Handle Agree button click
            $(document).on('click', '.agree-btn', function() {
                var appointmentId = $(this).data('id');
                $('#message-' + appointmentId).text('You have agreed to appointment ' + appointmentId).css('color', 'green');
                alert('You agreed to the appointment!');
            });
        });
    </script>
</body>
</html>
