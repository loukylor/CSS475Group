<?php
require_once 'config.inc.php';

$conn = new mysqli($servername, $username, $password, $database, $port, $socket);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

$username = $_GET['username'] ?? null;
if (!$username) {
    die("No username specified.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process update
    $first_name = $conn->real_escape_string($_POST['FirstName']);
    $last_name = $conn->real_escape_string($_POST['LastName']);
    $email = $conn->real_escape_string($_POST['Email']);
    $credibility = (int)$_POST['Credibility'];

    
    if ($credibility < 0) $credibility = 0;

    $sql = "UPDATE user SET FirstName='$first_name', LastName='$last_name', Email='$email', Credibility=$credibility WHERE Username = '" . $conn->real_escape_string($username) . "'";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>User updated successfully.</p>";
    } else {
        echo "<p style='color:red;'>Error updating user: " . $conn->error . "</p>";
    }
}

// Fetch current data to populate form
$sql = "SELECT FirstName, LastName, Email, Credibility FROM user WHERE Username = '" . $conn->real_escape_string($username) . "'";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    die("User not found.");
}

$row = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User: <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Edit User: <?php echo htmlspecialchars($username); ?></h2>
    <form method="POST" action="">
        <label for="FirstName">First Name:</label><br>
        <input type="text" id="FirstName" name="FirstName" value="<?php echo htmlspecialchars($row['FirstName']); ?>" required><br><br>

        <label for="LastName">Last Name:</label><br>
        <input type="text" id="LastName" name="LastName" value="<?php echo htmlspecialchars($row['LastName']); ?>" required><br><br>

        <label for="Email">Email:</label><br>
        <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($row['Email']); ?>" required><br><br>

        <label for="Credibility">Credibility:</label><br>
        <input type="number" id="Credibility" name="Credibility" value="<?php echo htmlspecialchars($row['Credibility']); ?>" min="0" max="100" required><br><br>

        <input type="submit" value="Update User">
    </form>
    <br>
    <a href="user.php">Back to User Directory</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
