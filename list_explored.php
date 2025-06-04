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
    <a href="./insert.php?table=explored">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }
    

    $sql = "SELECT Username, TrailID FROM explored";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'explored') {
        delete_composite_row_from_db($conn, 'explored', 'Username|TrailID');
        // header("Location: " . $_SERVER['REQUEST_URI']);
        // exit();
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $Username = $TrailID = $Username = $explored = null;
    
    render_rows(
        $stmt,
        function ($username, $trail_id) {
            return get_row_title("User: $username") . "<br>" . get_row_sub("Trail ID: $trail_id");
        },
        "Username|TrailID",      
        "explored",
        true,
        $username, $trail_id
    );
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
