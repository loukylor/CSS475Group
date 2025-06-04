<?php
require_once 'config.inc.php';
require_once 'render.php';
$conn = new mysqli($servername, $username, $password, $database, $port, $socket);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'profile') {
    delete_row_from_db($conn, 'profile', 'Username', $_POST['row_id']);
}
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
    <h2>Profile Directory</h2>
    <a href="./insert.php?table=profile">Insert into table</a>
    <?php



    $sql = "SELECT Username, Description FROM profile";

    
    $stmt = $conn->prepare($sql);
    $username = $description = null;
    render_rows(
        $stmt,
        function ($username, $description) {
            $editButton = '<a href="update_profile.php?username=' . urlencode($username) . '" style="margin-left:10px;">Update</a>';
            return get_row_title("Profile: " . htmlspecialchars($username) . " $editButton") . "<br>" . get_row_sub(htmlspecialchars($description));
        },
        "Username",         // primary key column
        "profile",          // table name
        false,              // is_composite = false
        $username, $description
    );

    $conn->close();
    ?>
</div>

</body>
</html>
