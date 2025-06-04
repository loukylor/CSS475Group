<?php
require_once 'config.inc.php';
require_once 'render.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Directory</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Report Directory</h2>
    <a href="./insert.php?table=report">Insert into table</a>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $sql = "SELECT ReportID, ReporterUsername, Username, ReviewUsername, ReviewTrailID, CommentID, PostID FROM report";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'report') {
        delete_row_from_db($conn, 'report', 'ReportID', $_POST['row_id']);
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $report_id = $reporter_username = $username = $review_username = $review_trail_id = $comment_id = $post_id = null;

    render_rows(
        $stmt,
        function ($report_id, $reporter_username, $username, $review_username, $review_trail_id, $comment_id, $post_id) {
            $sub = "No associated content"; // Default fallback
            if (!empty($username)) {
                $sub = "Profile: $username";
            } elseif (!empty($review_trail_id)) {
                $sub = "Review: $review_username, #$review_trail_id";
            } elseif (!empty($comment_id)) {
                $sub = "Comment: #$comment_id";
            } elseif (!empty($post_id)) {
                $sub = "Post: #$post_id";
            }
            $content = get_row_title("Report #$report_id by $reporter_username") . "<br>" .
                       get_row_sub($sub);
            return $content;
        },
        "ReportID",         // Primary key column
        "report",           // Table name
        false,
        $report_id, $reporter_username, $username, $review_username, $review_trail_id, $comment_id, $post_id
    );

    $conn->close();
    ?>
</div>

</body>
</html>