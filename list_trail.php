<?php
require_once 'config.inc.php';
require_once 'render.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trail Directory</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Trail Directory</h2>
    <a href="./insert.php?table=trail">Insert into table</a>
    
    <h3>Filter Table</h3>
    <form method="GET" action="list_trail.php">
    <label for="name">Trail Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
    |
    <label for="difficulty">Difficulty:</label>
    <select name="difficulty">
        <option value="">-- Any --</option>
        <option value="Easy" <?= ($_GET['difficulty'] ?? '') === 'Easy' ? 'selected' : '' ?>>Easy</option>
        <option value="Medium" <?= ($_GET['difficulty'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
        <option value="Hard" <?= ($_GET['difficulty'] ?? '') === 'Hard' ? 'selected' : '' ?>>Hard</option>
    </select>
    |
    <label>
        <input type="checkbox" name="bike" value="1" <?= isset($_GET['bike']) ? 'checked' : '' ?>>
        Bike Allowed
    </label>
    |
    <label>
        <input type="checkbox" name="dog" value="1" <?= isset($_GET['dog']) ? 'checked' : '' ?>>
        Dog Friendly
    </label>
    |
    <label>
        <input type="checkbox" name="open" value="1" <?= isset($_GET['open']) ? 'checked' : '' ?>>
        Open
    </label>
    |
    <!-- <label for="location_id">Location ID:</label>
    <input type="number" name="location_id" value="<?php //htmlspecialchars($_GET['location_id'] ?? '') ?>"> -->

    <label for="location_name">Location Name</label>
    <input type="text" name="location_name" value="<?= htmlspecialchars($_GET['location_name'] ?? '') ?>" />

    <br>

    <label for="do_sort">Sort?:</label>
    <input type="checkbox" name="do_sort" <?= (isset($_GET['do_sort']) ? 'checked' : '') ?> />
    <label for="order_by">Order by:</label>
    <select name="order_by">">
        <option value="location.Name" <?= $_GET['order_by'] === "location.Name" ? 'selected' : '' ?>>Location Name</option>
        <option value="TrailID" <?= $_GET['order_by'] === "TrailID" ? 'selected' : '' ?>>TrailID</option>
        <option value="Name" <?= $_GET['order_by'] === "Name" ? 'selected' : '' ?>>Name</option>
        <option value="Description" <?= $_GET['order_by'] === "Description" ? 'selected' : '' ?>>Description</option>
        <option value="Difficulty" <?= $_GET['order_by'] === "Difficulty" ? 'selected' : '' ?>>Difficulty</option>
        <option value="BikeAllowed" <?= $_GET['order_by'] === "BikeAllowed" ? 'selected' : '' ?>>BikeAllowed</option>
        <option value="DogFriendly" <?= $_GET['order_by'] === "DogFriendly" ? 'selected' : '' ?>>DogFriendly</option>
        <option value="Open" <?= $_GET['order_by'] === "Open" ? 'selected' : '' ?>>Open</option>
    </select>
    <label for="order">Ascending?:</label>
    <input type="checkbox" name="order" value="<?= htmlspecialchars($_GET['order'] ?? '') ?>" />
    |
    <label for="limit">Limit</label>
    <input type="number" name="limit" value="<?= htmlspecialchars($_GET['limit'] ?? '') ?>" />
    |
    <button type="submit">Filter</button>
    <button type="reset" onclick="window.location.href='list_trail.php';">Clear</button>
</form>

    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }
    $params = [];
    $types = '';

    $name = $_GET['name'] ?? '';
    $difficulty = $_GET['difficulty'] ?? '';
    $bike = $_GET['bike'] ?? '';
    $dog = $_GET['dog'] ?? '';
    $open = $_GET['open'] ?? '';
    $join = ' JOIN location ON location.LocationID = trail.LocationID ';
    if ($_GET['location_name'] !== '') {
        $join = ' JOIN location ON location.name=? ';
        $types .= 's';
        $params[] = $_GET['location_name'];
    }
    $order = isset($_GET['order']) ? 'ASC' : 'DESC';
    $sort = isset($_GET['do_sort']) ? " ORDER BY {$_GET['order_by']} $order" : '';
    $limit = (($_GET['limit'] ?? '') !== '') ? " LIMIT {$_GET['limit']}" : '';

    $sql = "SELECT location.Name, TrailID, trail.Name, trail.Description, Difficulty, BikeAllowed, DogFriendly, Open FROM trail "
            . "{$join}WHERE 1=1";

    if ($name) {
        $sql .= " AND Name LIKE ?";
        $params[] = '%' . $name . '%';
        $types .= 's';
    }

    if ($difficulty) {
        $sql .= " AND Difficulty = ?";
        $params[] = $difficulty;
        $types .= 's';
    }

    if ($bike !== '') {
        $sql .= " AND BikeAllowed = ?";
        $params[] = 1;
        $types .= 'i';
    }

    if ($dog !== '') {
        $sql .= " AND DogFriendly = ?";
        $params[] = 1;
        $types .= 'i';
    }

    if ($open !== '') {
        $sql .= " AND Open = ?";
        $params[] = 1;
        $types .= 'i';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'trail') {
        delete_row_from_db($conn, 'trail', 'TrailID', $_POST['row_id']);
    }
    
    $sql .= $sort . $limit;
    $stmt = $conn->prepare($sql);
    echo $sql;
    echo $conn->error;
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    render_rows(
        $stmt,
        function ($location_name, $trail_id, $name, $description, $difficulty, $bike_allowed, $dog_friendly, $open) {
            $edit_link = "<a href='update_trail.php?id=$trail_id'>Update</a>";
            $bike_allowed = is_null($bike_allowed) ? 'Unknown' : ($bike_allowed ? 'Yes' : 'No');
            $dog_friendly = is_null($dog_friendly) ? 'Unknown' : ($dog_friendly ? 'Yes' : 'No');
            $open = is_null($open) ? 'Unknown' : ($open ? 'Yes' : 'No');
            
            return get_row_title("Trail #$trail_id: $name in $location_name") . " [$edit_link]<br>" 
                 . get_row_sub("Open: $open | Dogs: $dog_friendly | Bike: $bike_allowed | $difficulty — $description");
            
        },
        "TrailID",     // Primary key column
        "trail",       // Table name
        false,         // is_composite = false
        $location_name, $trail_id, $name, $description, $difficulty, $bike_allowed, $dog_friendly, $open
    );
    
    
    
    
    $conn->close();
    ?>
</div>

</body>
</html>
