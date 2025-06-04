<?php
require_once 'config.inc.php';
require_once 'render.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Directory</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Post Directory</h2>
    <a href="./insert.php?table=post">Insert into table</a>
    
    <h3>Filter Table</h3>
    <form method="GET" action="list_post.php">
    <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
    |
    <input type="text" name="title" placeholder="Title" value="<?= htmlspecialchars($_GET['title'] ?? '') ?>">
    |
    <input type="number" name="trail_id" placeholder="Trail ID" value="<?= htmlspecialchars($_GET['trail_id'] ?? '') ?>">
    |
    <input type="number" name="post_id" placeholder="Post ID" value="<?= htmlspecialchars($_GET['post_id'] ?? '') ?>">
    |
    <input type="text" name="after" pattern = "\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}" placeholder="After date (YYYY-MM-DD HH:MM:SS)" value="<?= htmlspecialchars($_GET['after'] ?? '') ?>">
    
    <br>
    
    <label for="do_sort">Sort?:</label>
    <input type="checkbox" name="do_sort" <?= (isset($_GET['do_sort']) ? 'checked' : '') ?> />
    <label for="order_by">Order by:</label>
    <select name="order_by">">
        <option value="Username" <?= ($_GET['order_by'] ?? '') === "Username" ? 'selected' : '' ?>>Username</option>
        <option value="PostDate" <?= ($_GET['order_by'] ?? '') === "PostDate" ? 'selected' : '' ?>>PostDate</option>
        <option value="Title" <?= ($_GET['order_by'] ?? '') === "Title" ? 'selected' : '' ?>>Title</option>
        <option value="TrailID" <?= ($_GET['order_by'] ?? '') === "TrailID" ? 'selected' : '' ?>>TrailID</option>
        <option value="PostID" <?= ($_GET['order_by'] ?? '') === "PostID" ? 'selected' : '' ?>>PostID</option>
    </select>
    <label for="order">Ascending?:</label>
    <input type="checkbox" name="order" <?= (isset($_GET['order']) ? 'checked' : '') ?> />
    |
    <input type="number" placeholder="Limit" name="limit" value="<?= htmlspecialchars($_GET['limit'] ?? '') ?>" />
    |
    <button type="submit">Filter</button>
    <button type="reset" onclick="window.location.href='list_post.php';">Clear</button>
</form>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }
    $username = $_GET['username'] ?? '';
    $title = $_GET['title'] ?? '';
    $trail_id = $_GET['trail_id'] ?? '';
    $post_id = $_GET['post_id'] ?? '';
    $after = $_GET['after'] ?? '';
    
    $order = isset($_GET['order']) ? 'ASC' : 'DESC';
    $sort = isset($_GET['do_sort']) ? " ORDER BY {$_GET['order_by']} $order" : '';
    $limit = (($_GET['limit'] ?? '') !== '') ? " LIMIT {$_GET['limit']}" : '';

    $sql = "SELECT PostID, UserName, TrailID, Title, Description, PostDate FROM post WHERE 1=1";
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
    if (!empty($after)) {
        $sql .= " AND PostDate >= ?";
        $params[] = $after;
        $types .= 's';
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'post') {
        delete_row_from_db($conn, 'post', 'PostID', $_POST['row_id']);
    }
    
    $sql .= $sort . $limit;

    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $post_id = $username = $trail_id = $title = null;
    render_rows(
        $stmt,
        function ($post_id, $username, $trail_id, $title, $description, $post_date) {
            $content = get_row_title("Post #$post_id — $title at $post_date") . "<br>" .
                       get_row_sub("By $username on Trail #$trail_id - $description");
            $edit_link = "<a href='update_post.php?postid=$post_id' class='edit-link'>Update</a>";
            return $content . "<br>" . $edit_link;
        },
        "PostID",         // Primary key column
        "post",           // Table name
        false,
        $post_id, $username, $trail_id, $title, $description, $post_date
    );

    $conn->close();
    ?>
</div>

</body>
</html>
