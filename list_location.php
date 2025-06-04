<?php
require_once 'config.inc.php';
require_once 'render.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Location Directory</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Location Directory</h2>
    <a href="./insert.php?table=location">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT LocationID, ParentLocationID, Name, Description FROM location";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'location') {
        delete_row_from_db($conn, 'location', 'LocationID', $_POST['row_id']);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $location_id = $parent_location_id = $name = $description = null;
    render_rows(
        $stmt,
        function ($location_id, $parent_location_id, $name, $description) {
            $title = get_row_title("Location: $name (ID: $location_id)");
            $sub = get_row_sub("Parent ID: " . ($parent_location_id ?? "None") . " — $description");
            $edit_link = "<a href='update_location.php?location_id=$location_id' class='edit-link'>Update</a>";
            return "$title<br>$sub<br>$edit_link";
        },
        "LocationID",       // Primary key column
        "location",         // Table name
        false,
        $location_id, $parent_location_id, $name, $description
    );

    $conn->close();
    ?>
</div>

</body>
</html>
