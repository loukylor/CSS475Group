<?php
require_once 'config.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Directory</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Profile Directory</h2>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT Username, Description FROM profile";
    $stmt = $conn->stmt_init();

    if (!$stmt->prepare($sql)) {
        echo "<p style='color:red;'>Failed to prepare SQL statement.</p>";
    } else {
        $stmt->execute();
        $stmt->bind_result($Username, $Description);

        echo "<ul class='user-list'>";
        while ($stmt->fetch()) {
            echo "<li><strong>" . htmlspecialchars($Username) . "</strong><br>";
            echo "<span class='role'>" . htmlspecialchars($Description) . "</span></li>";
        }
        echo "</ul>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
