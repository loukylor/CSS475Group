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
    <h2>Trail Directory</h2>
    <a href="./insert.php?table=trail">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT TrailID, Name, Description, Difficulty FROM trail";
    render_rows(
        $sql,
        $conn,
        function ($trail_id, $name, $description, $difficulty) {
            $edit_link = "<a href='update_trail.php?id=$trail_id'>Update</a>";
            return get_row_title("Trail #$trail_id: $name") . " [$edit_link]<br>" . get_row_sub("$difficulty — $description");
        },
        "TrailID",     // Primary key column
        "trail",       // Table name
        false,         // is_composite = false
        $trail_id, $name, $description, $difficulty
    );
    
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
