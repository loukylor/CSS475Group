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

    $sql = "SELECT Username, TrailID, Title, Score FROM review";
    render_rows(
        $sql,
        $conn,
        function ($username, $trail_id, $title, $score) {
            return get_row_title("Review: $title") . "<br>" .
                   get_row_sub("User: $username | Trail: $trail_id | Score: $score/5");
        },
        "Username|TrailID",   // Composite key
        "review",             // Table name
        True,
        $username, $trail_id, $title, $score
    );
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
