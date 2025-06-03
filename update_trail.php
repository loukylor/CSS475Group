<?php
require_once 'config.inc.php';

// Get Trail ID from query
$id = $_GET['id'] ?? null;
if ($id === "" || $id === false || $id === null) {
    header('location: list_trail.php');
    exit();
}
?>
<html>
<head>
    <title>Update Trail</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Update Trail</h2>
<?php

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['Name'] ?? "");
    $description = trim($_POST['Description'] ?? "");
    $difficulty = $_POST['Difficulty'] ?? "Medium";
    $duration = $_POST['Duration'] ?? "";
    $length = $_POST['Length'] ?? "";
   
    $open = isset($_POST['Open']) ? 1 : 0;

    
    $errors = [];
    if ($name === "") {
        $errors[] = "Trail Name cannot be empty.";
    }
    if (!in_array($difficulty, ['Easy', 'Medium', 'Hard'])) {
        $errors[] = "Difficulty must be Easy, Medium, or Hard.";
    }
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $duration)) {
        $errors[] = "Duration must be in HH:MM:SS format.";
    }
    if (!is_numeric($length) || $length <= 0) {
        $errors[] = "Length must be a positive number.";
    }

    if (count($errors) > 0) {
        foreach ($errors as $err) {
            echo "<div style='color:red;'>$err</div>";
        }
    } else {
        $sql = "UPDATE trail SET Name = ?, Description = ?, Difficulty = ?, Duration = ?, Length = ?, Open = ? WHERE TrailID = ?";
        $stmt = $conn->stmt_init();
        if (!$stmt->prepare($sql)) {
            echo "<div style='color:red;'>Failed to prepare SQL statement.</div>";
        } else {
          
            $stmt->bind_param('ssssdii', $name, $description, $difficulty, $duration, $length, $open, $id);
            if ($stmt->execute()) {
                $conn->commit();
                echo "<p style='color:green;'>Trail updated successfully.</p>";
            } else {
                echo "<div style='color:red;'>Failed to update trail.</div>";
            }
        }
    }
}


$sql = "SELECT TrailID, Name, Description, Difficulty, Duration, Length, Open FROM trail WHERE TrailID = ?";
$stmt = $conn->stmt_init();
if (!$stmt->prepare($sql)) {
    echo "<div style='color:red;'>Failed to prepare SQL.</div>";
} else {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($trailID, $name, $description, $difficulty, $duration, $length, $open);

    if ($stmt->fetch()) {
        ?>
        <form method="post">
            <p><strong>Trail ID:</strong> <?= htmlspecialchars($trailID) ?></p>

            <p>
                <label>Trail Name:<br>
                    <input type="text" name="Name" value="<?= htmlspecialchars($name) ?>" required>
                </label>
            </p>

            <p>
                <label>Description:<br>
                    <textarea name="Description" rows="4" cols="50" required><?= htmlspecialchars($description) ?></textarea>
                </label>
            </p>

            <p>
                <label>Difficulty:<br>
                    <select name="Difficulty" required>
                        <option value="Easy" <?= $difficulty === 'Easy' ? 'selected' : '' ?>>Easy</option>
                        <option value="Medium" <?= $difficulty === 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="Hard" <?= $difficulty === 'Hard' ? 'selected' : '' ?>>Hard</option>
                    </select>
                </label>
            </p>

            <p>
                <label>Duration (HH:MM:SS):<br>
                    <input type="text" name="Duration" pattern="\d{2}:\d{2}:\d{2}" title="Format HH:MM:SS" value="<?= htmlspecialchars($duration) ?>" required>
                </label>
            </p>

            <p>
                <label>Length (miles):<br>
                    <input type="number" step="0.01" min="0" name="Length" value="<?= htmlspecialchars($length) ?>" required>
                </label>
            </p>

            <p>
                <label>
                    <input type="checkbox" name="Open" <?= $open ? 'checked' : '' ?>>
                    Open for visitors
                </label>
            </p>

            <button type="submit">Update Trail</button>
        </form>
        <?php
    } else {
        echo "<p style='color:red;'>Trail not found.</p>";
    }
}

$conn->close();
?>
</div>
</body>
</html>
