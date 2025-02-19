<?php
session_start();

// Ensure only clients can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
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

// Fetch available lawyers
$stmt = $conn->prepare("SELECT id, f_name, l_name FROM user WHERE role = 'lawyer'");
$stmt->execute();
$lawyers = $stmt->fetchAll();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_SESSION['username']; // Store client's name instead of ID
    $lawyer_id = $_POST['lawyer_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Merge date and time into a single datetime format
    $datetime = $appointment_date . " " . $appointment_time;

    // Insert the appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (lawyer_id, client_name, date) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $lawyer_id);
    $stmt->bindParam(2, $client_name);
    $stmt->bindParam(3, $datetime);

    if ($stmt->execute()) {
        $success_message = "Appointment booked successfully! You will receive an email confirmation.";
    } else {
        $error_message = "Failed to book the appointment. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #28a745;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #28a745;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        select, input[type="date"], input[type="time"], button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="Lawyer Info.php">Lawyers</a></li>
                <li><a href="booking Page.php">Book Appointment</a></li>
                <li><a href="Client File Upload.php">Upload Files</a></li>
                
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <h2>Book an Appointment</h2>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label>Select Lawyer:</label>
            <select name="lawyer_id" required>
                <?php foreach ($lawyers as $lawyer): ?>
                    <option value="<?php echo $lawyer['id']; ?>">
                        <?php echo htmlspecialchars($lawyer['f_name'] . ' ' . $lawyer['l_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label>Appointment Date:</label>
            <input type="date" name="appointment_date" required>
            
            <label>Appointment Time:</label>
            <input type="time" name="appointment_time" required>
            
            <button type="submit">Book Appointment</button>
        </form>
    </div>
</body>
</html>
