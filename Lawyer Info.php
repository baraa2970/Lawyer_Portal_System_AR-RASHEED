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
$charset = 'latin1';

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

// Fetch lawyers from the database
$stmt = $conn->prepare("SELECT f_name, l_name, Type_of_cases, phone_num FROM user WHERE role = 'lawyer'");
$stmt->execute();
$result = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Information</title>
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
            max-width: 800px;
            margin: 0 auto;
        }
        h2 {
            color: #28a745;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                
                <li><a href="Booking Page.php">Book Appointment</a></li>
                <li><a href="Client File Upload.php">Upload Files</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Lawyers Available</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Specialization</th>
                <th>Contact Info</th>
            </tr>
            <?php foreach ($result as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['f_name'] . ' ' . $row['l_name']); ?></td>
                <td><?php echo htmlspecialchars($row['Type_of_cases']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_num']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>