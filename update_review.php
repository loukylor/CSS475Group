<?php
require_once 'config.inc.php';


$editUsername = $_GET['username'] ?? null;
$trailID = $_GET['trailid'] ?? null;

if (!$editUsername || !$trailID) {
    header('Location: review_directory.php'); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Update Review</title>
    <link rel="stylesheet" href="base.css" />
</head>
<body>
<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Update Review</h2>

<?php

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = intval($_POST['Score'] ?? 0);
    $title = trim($_POST['Title'] ?? "");
    $description = trim($_POST['Description'] ?? "");

    $errors = [];
    if ($score < 1 || $score > 5) {
        $errors[] = "Score must be between 1 and 5.";
    }
    if ($title === "") {
        $errors[] = "Title cannot be empty.";
    }
    if ($description === "") {
        $errors[] = "Description cannot be empty.";
    }

    if (empty($errors)) {
        $sql = "UPDATE review SET Score = ?, Title = ?, Description = ? WHERE Username = ? AND TrailID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "<p style='color:red;'>Failed to prepare statement.</p>";
        } else {
            $stmt->bind_param('isssi', $score, $title, $description, $editUsername, $trailID);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Review updated successfully.</p>";
            } else {
                echo "<p style='color:red;'>Failed to update review.</p>";
            }
            $stmt->close();
        }
    } else {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>$err</p>";
        }
    }
}


$sql = "SELECT Score, Title, Description FROM review WHERE Username = ? AND TrailID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo "<p style='color:red;'>Failed to prepare statement.</p>";
} else {
    $stmt->bind_param('si', $editUsername, $trailID);
    $stmt->execute();
    $stmt->bind_result($score, $title, $description);
    if ($stmt->fetch()) {
        ?>
        <form method="post">
            <p><strong>User:</strong> <?= htmlspecialchars($editUsername) ?></p>
            <p><strong>Trail ID:</strong> <?= htmlspecialchars($trailID) ?></p>
            <p>
                <label>Score (1-5):<br>
                    <input type="number" name="Score" min="1" max="5" value="<?= htmlspecialchars($score) ?>" required>
                </label>
            </p>
            <p>
                <label>Title:<br>
                    <input type="text" name="Title" maxlength="32" value="<?= htmlspecialchars($title) ?>" required>
                </label>
            </p>
            <p>
                <label>Description:<br>
                    <textarea name="Description" rows="5" cols="60" maxlength="255" required><?= htmlspecialchars($description) ?></textarea>
                </label>
            </p>
            <button type="submit">Update Review</button>
        </form>
        <?php
    } else {
        echo "<p style='color:red;'>Review not found.</p>";
    }
    $stmt->close();
}

$conn->close();
?>

</div>
</body>
</html>
