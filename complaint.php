<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jms";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Fetch complaints dynamically
$query = "SELECT c.id AS case_id, c.case_title, c.type_of_case, 
                 l.f_name AS lawyer_name, u.f_name AS complainant_name 
          FROM complaints cmp
          JOIN cases c ON cmp.case_id = c.id
          JOIN user l ON cmp.lawyer_id = l.id
          JOIN user u ON cmp.complainant_id = u.id
          WHERE cmp.complainant_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle complaint submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lawyer_id = $_POST['lawyer_id'];
    $case_id = $_POST['case_id'];
    $complaint_type = $_POST['complaint_type'];

    $insert_query = "INSERT INTO complaints (complainant_id, lawyer_id, case_id, complaint_type) 
                     VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("iiis", $client_id, $lawyer_id, $case_id, $complaint_type);

    if ($stmt_insert->execute()) {
        echo "<script>alert('Complaint submitted successfully!'); window.location.href='complaints.php';</script>";
    } else {
        echo "<script>alert('Failed to submit complaint.');</script>";
    }

    $stmt_insert->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Complaints</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; }
        .container { width: 80%; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        .form-container { margin-top: 30px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, select, button { padding: 10px; margin: 10px; width: 80%; border: 1px solid #ccc; border-radius: 5px; }
        button { background-color: #28a745; color: white; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Complaints Against Lawyers</h2>
        <table>
            <tr>
                <th>Lawyer Name</th>
                <th>Case Type</th>
                <th>Case Number</th>
                <th>Case Title</th>
                <th>Complainant Name</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['lawyer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['type_of_case']); ?></td>
                <td><?php echo htmlspecialchars($row['case_id']); ?></td>
                <td><?php echo htmlspecialchars($row['case_title']); ?></td>
                <td><?php echo htmlspecialchars($row['complainant_name']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="form-container">
        <h2>Submit a Complaint</h2>
        <form method="POST">
            <select name="lawyer_id" required>
                <option value="">Select Lawyer</option>
                <?php
                // Fetch all lawyers for dropdown
                $conn = new mysqli($servername, $username, $password, $dbname);
                $lawyer_query = "SELECT id, f_name FROM user WHERE role = 1";
                $lawyer_result = $conn->query($lawyer_query);
                while ($lawyer = $lawyer_result->fetch_assoc()) {
                    echo "<option value='{$lawyer['id']}'>{$lawyer['f_name']}</option>";
                }
                $conn->close();
                ?>
            </select>

            <select name="case_id" required>
                <option value="">Select Case</option>
                <?php
                // Fetch all cases for dropdown
                $conn = new mysqli($servername, $username, $password, $dbname);
                $case_query = "SELECT id, case_title FROM cases WHERE client = ?";
                $stmt_case = $conn->prepare($case_query);
                $stmt_case->bind_param("i", $client_id);
                $stmt_case->execute();
                $case_result = $stmt_case->get_result();
                while ($case = $case_result->fetch_assoc()) {
                    echo "<option value='{$case['id']}'>{$case['case_title']}</option>";
                }
                $stmt_case->close();
                $conn->close();
                ?>
            </select>

            <input type="text" name="complaint_type" placeholder="Enter Complaint Type" required>
            <button type="submit">Submit Complaint</button>
        </form>
    </div>
</body>
</html>
