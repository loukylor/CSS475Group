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
    <h2>Post Directory</h2>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT PostID, Username, TrailID, Title FROM post";
    render_rows(
        $sql,
        $conn,
        function ($post_id, $username, $trail_id, $title) {
            return get_row_title("Post #$post_id — $title") . "<br>" .
                   get_row_sub("By $username on Trail #$trail_id");
        },
        "PostID",         // Primary key column
        "post",           // Table name
        false,
        $post_id, $username, $trail_id, $title
    );
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
