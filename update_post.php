<?php
require_once 'config.inc.php';

// Get PostID from query string
$postID = $_GET['postid'] ?? null;

if (!$postID) {
    header('Location: post_directory.php'); // Redirect if no PostID provided
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Update Post</title>
    <link rel="stylesheet" href="base.css" />
</head>
<body>
<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Update Post</h2>

<?php
$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['Title'] ?? "");
    $description = trim($_POST['Description'] ?? "");

    $errors = [];
    if ($title === "") {
        $errors[] = "Title cannot be empty.";
    }
    if ($description === "") {
        $errors[] = "Description cannot be empty.";
    }

    if (empty($errors)) {
        $sql = "UPDATE post SET Title = ?, Description = ? WHERE PostID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "<p style='color:red;'>Failed to prepare statement.</p>";
        } else {
            $stmt->bind_param('ssi', $title, $description, $postID);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Post updated successfully.</p>";
            } else {
                echo "<p style='color:red;'>Failed to update post.</p>";
            }
            $stmt->close();
        }
    } else {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>$err</p>";
        }
    }
}

// Fetch current post info
$sql = "SELECT Username, TrailID, Title, Description FROM post WHERE PostID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo "<p style='color:red;'>Failed to prepare statement.</p>";
} else {
    $stmt->bind_param('i', $postID);
    $stmt->execute();
    $stmt->bind_result($username, $trailID, $title, $description);
    if ($stmt->fetch()) {
        ?>
        <form method="post">
            <p><strong>User:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Trail ID:</strong> <?= htmlspecialchars($trailID) ?></p>
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
            <button type="submit">Update Post</button>
        </form>
        <?php
    } else {
        echo "<p style='color:red;'>Post not found.</p>";
    }
    $stmt->close();
}

$conn->close();
?>

</div>
</body>
</html>
