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
    <h2>Images Directory</h2>
    <a href="./insert.php?table=image">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT ImageURL, Username, PostID, FileSize FROM image";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'image') {
        delete_row_from_db($conn, 'image', 'ImageURL', $_POST['row_id']);
    }
    
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    render_rows(
        $stmt,
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
