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
    <form method="GET" action="list_trail.php">
    <label for="name">Trail Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">

    <label for="difficulty">Difficulty:</label>
    <select name="difficulty">
        <option value="">-- Any --</option>
        <option value="Easy" <?= ($_GET['difficulty'] ?? '') === 'Easy' ? 'selected' : '' ?>>Easy</option>
        <option value="Medium" <?= ($_GET['difficulty'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
        <option value="Hard" <?= ($_GET['difficulty'] ?? '') === 'Hard' ? 'selected' : '' ?>>Hard</option>
    </select>

    <label>
        <input type="checkbox" name="bike" value="1" <?= isset($_GET['bike']) ? 'checked' : '' ?>>
        Bike Allowed
    </label>

    <label>
        <input type="checkbox" name="dog" value="1" <?= isset($_GET['dog']) ? 'checked' : '' ?>>
        Dog Friendly
    </label>

    <label>
        <input type="checkbox" name="open" value="1" <?= isset($_GET['open']) ? 'checked' : '' ?>>
        Open
    </label>

    <label for="location_id">Location ID:</label>
    <input type="number" name="location_id" value="<?= htmlspecialchars($_GET['location_id'] ?? '') ?>">

    <button type="submit">Filter</button>
    <button type="reset" onclick="window.location.href='list_trail.php';">Clear</button>
</form>

    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }
    $name = $_GET['name'] ?? '';
    $difficulty = $_GET['difficulty'] ?? '';
    $bike = $_GET['bike'] ?? '';
    $dog = $_GET['dog'] ?? '';
    $open = $_GET['open'] ?? '';
    $location_id = $_GET['location_id'] ?? '';

    $sql = "SELECT TrailID, Name, Description, Difficulty FROM trail where 1=1";
    $params = [];
    $types = '';

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

    if ($location_id !== '') {
        $sql .= " AND LocationID = ?";
        $params[] = (int)$location_id;
        $types .= 'i';
    }

    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    //$sql = "SELECT TrailID, Name, Description, Difficulty FROM trail";
    //$stmt = $conn->prepare($sql);
    //$stmt ->execute();
    render_rows(
        $sql,
        $stmt,
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
