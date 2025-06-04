<?php
require_once 'config.inc.php';
require_once 'render.php';
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
    <h2>Comment Directory</h2>
    <a href="./insert.php?table=comment">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT CommentID, PostID, Username, Description FROM comment";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'comment') {
        delete_row_from_db($conn, 'comment', 'CommentID', $_POST['row_id']);
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $comment_id = $post_id = $username = $description = null;
    render_rows(
        $stmt,
        function ($comment_id, $post_id, $username, $description) {
            $editLink = "<a href='update_comment.php?id=" . urlencode($comment_id) . "'>Update</a>";
            $title = "Comment by $username on Post #$post_id $editLink";
            return get_row_title($title) . "<br>" . get_row_sub($description);
        },
        "CommentID",       // Primary key column
        "comment",         // Table name
        false,
        $comment_id, $post_id, $username, $description
    );

    $conn->close();
    ?>
</div>

</body>
</html>
