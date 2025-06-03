<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.inc.php';

// Get Comment ID from query
$id = $_GET['id'] ?? null;
if ($id === null || !is_numeric($id)) {
    header('Location: list_comment.php'); // or your comments listing page
    exit();
}

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST to update description
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['Description'] ?? '');
    if ($description === '') {
        echo "<p style='color:red;'>Description cannot be empty.</p>";
    } else {
        $sql = "UPDATE comment SET Description = ? WHERE CommentID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "<p style='color:red;'>Failed to prepare statement.</p>";
        } else {
            $stmt->bind_param('si', $description, $id);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Comment updated successfully.</p>";
            } else {
                echo "<p style='color:red;'>Failed to update comment.</p>";
            }
        }
    }
}

// Fetch current comment data
$sql = "SELECT CommentID, PostID, Username, Description FROM comment WHERE CommentID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<p style='color:red;'>Failed to prepare select statement.</p>");
}
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($commentID, $postID, $username, $description);

if (!$stmt->fetch()) {
    echo "<p style='color:red;'>Comment not found.</p>";
    $conn->close();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Comment</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>
<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Update Comment</h2>
    <p><strong>Comment ID:</strong> <?= htmlspecialchars($commentID) ?></p>
    <p><strong>Post ID:</strong> <?= htmlspecialchars($postID) ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
    <form method="post">
        <p>
            <label for="desc">Description:</label><br>
            <textarea id="desc" name="Description" rows="5" cols="60" required><?= htmlspecialchars($description) ?></textarea>
        </p>
        <button type="submit">Update Comment</button>
    </form>
</div>

</body>
</html>
