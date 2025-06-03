<?php
require_once 'config.inc.php';


$profileUsername = $_GET['username'] ?? null;
if ($profileUsername === "" || $profileUsername === false || $profileUsername === null) {
    header('location: list_users.php'); // or your users listing page
    exit();
}
?>
<html>
<head>
    <title>Update Profile Description</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Update Profile Description</h2>
<?php

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['Description'] ?? "");

    if ($description === "") {
        echo "<div style='color:red;'>Description cannot be empty.</div>";
    } else {
        $sql = "UPDATE profile SET Description = ? WHERE Username = ?";
        $stmt = $conn->stmt_init();
        if (!$stmt->prepare($sql)) {
            echo "<div style='color:red;'>Failed to prepare SQL statement.</div>";
        } else {
            $stmt->bind_param('ss', $description, $profileUsername);
            if ($stmt->execute()) {
                $conn->commit();
                echo "<p style='color:green;'>Profile description updated successfully.</p>";
            } else {
                echo "<div style='color:red;'>Failed to update profile description.</div>";
            }
        }
    }
}


$sql = "SELECT Username, Description FROM profile WHERE Username = ?";
$stmt = $conn->stmt_init();
if (!$stmt->prepare($sql)) {
    echo "<div style='color:red;'>Failed to prepare SQL statement.</div>";
} else {
    $stmt->bind_param('s', $profileUsername);
    $stmt->execute();
    $stmt->bind_result($fetchedUsername, $description);

    if ($stmt->fetch()) {
        ?>
        <form method="post">
            <p><strong>Username:</strong> <?= htmlspecialchars($fetchedUsername) ?></p>
            <p>
                <label>Description:<br>
                    <textarea name="Description" rows="6" cols="60" required><?= htmlspecialchars($description) ?></textarea>
                </label>
            </p>
            <button type="submit">Update Description</button>
        </form>
        <?php
    } else {
        echo "<p style='color:red;'>Profile not found.</p>";
    }
}

$conn->close();
?>
</div>
</body>
</html>
