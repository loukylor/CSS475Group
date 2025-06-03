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

    $sql = "SELECT ImageURL, Username, PostID, FileSize FROM image";
    render_rows(
        $sql,
        $conn,
        function ($image_url, $username, $post_id, $file_size) {
            return get_row_title("Image: $image_url") . "<br>" .
                   get_row_sub("Uploaded by $username for Post #$post_id — Size: " . round($file_size / 1024, 2) . " KB");
        },
        "ImageURL",       // Primary key column
        "image",          // Table name
        false,
        $image_url, $username, $post_id, $file_size
    );
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
