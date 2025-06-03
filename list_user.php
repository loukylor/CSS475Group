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

    $sql = "SELECT Username, FirstName, LastName, Email, Credibility FROM user";
    render_rows(
        $sql,
        $conn,
        function ($username, $first_name, $last_name, $email, $credibility) {
            $content = get_row_title("User: $username") . "<br>" .
                       get_row_sub("$first_name $last_name | $email | Credibility: $credibility");
            $edit_link = "<a href='update_user.php?username=" . urlencode($username) . "' class='edit-link'>Update</a>";
            return $content . "<br>" . $edit_link;
        },
        "Username",
        "user",
        false,
        $username, $first_name, $last_name, $email, $credibility
    );

    $conn->close();
    ?>
</div>

</body>
</html>
