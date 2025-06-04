
<?php
require_once 'config.inc.php';
require_once 'render.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Directory</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Review Directory</h2>
    <a href="./insert.php?table=review">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT Username, TrailID, Title, Score, Description FROM review";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'review') {
        delete_composite_row_from_db($conn, 'review', 'Username|TrailID');
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    render_rows(
        $stmt,
        function ($username, $trail_id, $title, $score, $description) {
            // URL encode to avoid issues with special chars
            $edit_url = "update_review.php?username=" . urlencode($username) . "&trailid=" . urlencode($trail_id);

            return get_row_title("Review: $title") . "<br>" .
                   get_row_sub("User: $username | Trail: $trail_id | Score: $score/5 - $description") .
                   "<br><a href='$edit_url' style='color:blue;'>Update</a>";
        },
        "Username|TrailID",   // Composite key
        "review",             // Table name
        True,
        $username, $trail_id, $title, $score, $description
    );

    $conn->close();
    ?>
</div>

</body>
</html>
