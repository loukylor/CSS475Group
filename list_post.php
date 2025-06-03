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
    <h2>Post Directory</h2>
    <a href="./insert.php?table=post">Insert into table</a>
    <form method="GET" action="list_post.php">
    <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
    <input type="text" name="title" placeholder="Title" value="<?= htmlspecialchars($_GET['title'] ?? '') ?>">
    <input type="number" name="trail_id" placeholder="Trail ID" value="<?= htmlspecialchars($_GET['trail_id'] ?? '') ?>">
    <input type="number" name="post_id" placeholder="Post ID" value="<?= htmlspecialchars($_GET['post_id'] ?? '') ?>">
    <button type="submit">Filter</button>
    <button type="reset" onclick="window.location.href='list_post.php';">Clear</button>
</form>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port, $socket);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }
    $username = $_GET['username'] ?? '';
    $title = $_GET['title'] ?? '';
    $trail_id = $_GET['trail_id'] ?? '';
    $post_id = $_GET['post_id'] ?? '';

    $sql = "SELECT PostID, UserName, TrailID, Title FROM post WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($username)) {
        $sql .= " AND Username LIKE ?";
        $params[] = '%' . $username . '%';
        $types .= 's';
    }
    if (!empty($title)) {
        $sql .= " AND Title LIKE ?";
        $params[] = '%' . $title . '%';
        $types .= 's';
    }
    if (!empty($trail_id)) {
        $sql .= " AND TrailID = ?";
        $params[] = $trail_id;
        $types .= 'i';
    }
    if (!empty($post_id)) {
        $sql .= " AND PostID = ?";
        $params[] = $post_id;
        $types .= 'i';
    }

    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    //$sql2 = "SELECT PostID, Username, TrailID, Title FROM post";

    $stmt->execute();
    render_rows(
        $sql,
        $stmt,
        function ($post_id, $username, $trail_id, $title) {
            $content = get_row_title("Post #$post_id — $title") . "<br>" .
                       get_row_sub("By $username on Trail #$trail_id");
            $edit_link = "<a href='update_post.php?postid=$post_id' class='edit-link'>Update</a>";
            return $content . "<br>" . $edit_link;
        },
        "PostID",         // Primary key column
        "post",           // Table name
        false,
        $post_id, $username, $trail_id, $title
    );

    $conn->close();
    ?>
</div>

</body>
</html>
