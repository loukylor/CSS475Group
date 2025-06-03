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
    <h2>User Directory</h2>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT CommentID, PostID, Username, Description FROM comment";
    render_rows(
        $sql,
        $conn,
        function ($comment_id, $post_id, $username, $description) {
            return get_row_title("$username — Comment #$comment_id on Post #$post_id") . "<br>" . get_row_sub($description);
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
