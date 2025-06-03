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
    <h2>Location Directory</h2>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT LocationID, ParentLocationID, Name, Description FROM location";
    render_rows(
        $sql,
        $conn,
        function ($location_id, $parent_location_id, $name, $description) {
            return get_row_title("Location: $name (ID: $location_id)") . "<br>" .
                   get_row_sub("Parent ID: " . ($parent_location_id ?? "None") . " — $description");
        },
        "LocationID",       // Primary key column
        "location",         // Table name
        $location_id, $parent_location_id, $name, $description
    );
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
