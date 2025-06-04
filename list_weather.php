
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
    <h2>Weather Directory</h2>
    <a href="./insert.php?table=weather">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT WeatherID, TrailID, TemperatureF, Conditions, ForDate FROM weather";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'weather') {
        delete_row_from_db($conn, 'weather', 'WeatherID', $_POST['row_id']);
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    render_rows(
        $stmt,
        function ($weather_id, $trail_id, $temp_f, $conditions, $for_date) {
            return get_row_title("Weather #$weather_id") . "<br>" .
                   get_row_sub("Trail $trail_id, $conditions, $temp_f°F on $for_date");
        },
        "WeatherID",     // primary key column
        "weather",       // table name
        false,           // not composite
        $weather_id, $trail_id, $temp_f, $conditions, $for_date
    );
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
