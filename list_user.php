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

    $sql = "SELECT FirstName, LastName, UserType FROM user";
    render_rows($sql, $conn, function ($first_name, $last_name, $user_type) {
        return get_row_title("$first_name $last_name") . "<br>" . get_row_sub($userType ?? "Regular User");
    }, $first_name, $last_name, $user_type);
    
    $conn->close();
    ?>
</div>

</body>
</html>
