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
    <h2>Explored Directory</h2>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT Username, TrailID FROM explored";
    render_rows(
        $sql,
        $conn,
        function ($username, $trail_id) {
            return get_row_title("User: $username") . "<br>" . get_row_sub("Trail ID: $trail_id");
        },
        "TrailID",       // FIX LATER I DONT KNOW HOW THO
        "explored",
        $username, $trail_id
    );
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
