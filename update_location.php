<?php
require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port, $socket);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

$location_id = $_GET['location_id'] ?? null;
if (!$location_id) {
    die("No location ID specified.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form input
    $name = $conn->real_escape_string($_POST['Name']);
    $description = $conn->real_escape_string($_POST['Description']);

    $sql = "UPDATE location SET Name='$name', Description='$description' WHERE LocationID = " . intval($location_id);
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Location updated successfully.</p>";
    } else {
        echo "<p style='color:red;'>Error updating location: " . $conn->error . "</p>";
    }
}

// Fetch current data to populate form
$sql = "SELECT Name, Description FROM location WHERE LocationID = " . intval($location_id);
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    die("Location not found.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Location #<?php echo htmlspecialchars($location_id); ?></title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Edit Location #<?php echo htmlspecialchars($location_id); ?></h2>
    <form method="POST" action="">
        <label for="Name">Name:</label><br>
        <input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($row['Name']); ?>" required><br><br>

        <label for="Description">Description:</label><br>
        <textarea id="Description" name="Description" rows="4" cols="50" required><?php echo htmlspecialchars($row['Description']); ?></textarea><br><br>

        <input type="submit" value="Update Location">
    </form>
    <br>
    <a href="location.php">Back to Location Directory</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
