<?php
require_once 'config.inc.php';
require_once 'render.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users With Reports</title>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php require_once 'header.inc.php'; ?>

<div>
    <h2>Users With Reports</h2>

    <form method="GET" action="account_reports.php">
        <input type="number" name="credibility" placeholder="Credibility Threshold" value="<?= htmlspecialchars($_GET['credibility'] ?? '') ?>">
        |
        <input type="number" name="reports" placeholder="Report Threshold" value="<?= htmlspecialchars($_GET['reports'] ?? '') ?>">
        |
        <input type="number" name="date_cutoff" placeholder="Posted in last (days):" value="<?= htmlspecialchars($_GET['date_cutoff'] ?? '') ?>">
        
        <br>
        
        <label for="do_sort">Sort?:</label>
        <input type="checkbox" name="do_sort" <?= (isset($_GET['do_sort']) ? 'checked' : '') ?> />
        <label for="order_by">Order by:</label>
        <select name="order_by">">
            <option value="user.Username" <?= ($_GET['order_by'] ?? '') === "user.Username" ? 'selected' : '' ?>>Username</option>
            <option value="FirstName" <?= ($_GET['order_by'] ?? '') === "FirstName" ? 'selected' : '' ?>>FirstName</option>
            <option value="LastName" <?= ($_GET['order_by'] ?? '') === "LastName" ? 'selected' : '' ?>>LastName</option>
            <option value="Email" <?= ($_GET['order_by'] ?? '') === "Email" ? 'selected' : '' ?>>Email</option>
            <option value="Credibility" <?= ($_GET['order_by'] ?? '') === "Credibility" ? 'selected' : '' ?>>Credibility</option>
            <option value="PostCount" <?= ($_GET['order_by'] ?? '') === "PostCount" ? 'selected' : '' ?>>PostCount</option>
            <option value="report_count.Count" <?= ($_GET['order_by'] ?? '') === "report_count.Count" ? 'selected' : '' ?>>ReportCount</option>
        </select>
        <label for="order">Ascending?:</label>
        <input type="checkbox" name="order" <?= (isset($_GET['order']) ? 'checked' : '') ?> />
        |
        <input type="number" placeholder="Limit" name="limit" value="<?= htmlspecialchars($_GET['limit'] ?? '') ?>" />
        |
        <button type="submit">Filter</button>
        <button type="reset" onclick="window.location.href='account_reports.php';">Clear</button>
    </form>

    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $credibility = $_GET['credibility'] ?? '';
    $reports = $_GET['reports'] ?? '';
    $date_cutoff = $_GET['date_cutoff'] ?? '';
    
    $order = isset($_GET['order']) ? 'ASC' : 'DESC';
    $sort = isset($_GET['do_sort']) ? " ORDER BY {$_GET['order_by']} $order" : '';
    $limit = (($_GET['limit'] ?? '') !== '') ? " LIMIT {$_GET['limit']}" : '';

    $after_post_join = ' LEFT JOIN ('
            . 'SELECT PostID, COUNT(*) AS Count FROM report GROUP BY PostID'
        . ') report_count ON report_count.PostID = post.PostID WHERE 1=1 ';

    $params = [];
    $types = '';

    if (!empty($credibility)) {
        $after_post_join .= " AND Credibility <= ?";
        $params[] = $credibility;
        $types .= 'i';
    }
    if (!empty($reports)) {
        $after_post_join .= " AND report_count.Count >= ?";
        $params[] = $reports;
        $types .= 'i';
    }
    $post_join = 'JOIN post';
    if (!empty($date_cutoff)) {
        $types = 'i' . $types;
        array_unshift($params, $date_cutoff);
        $post_join = "JOIN (SELECT * FROM post WHERE post.PostDate > DATE_SUB(NOW(), INTERVAL ? DAY)) as post";
    }
    $sql = "SELECT COUNT(*) AS PostCount, SUM(report_count.Count) as ReportCount, user.Username, FirstName, LastName, Email, Credibility FROM user"
        . " RIGHT JOIN profile ON user.Username = profile.Username"
        . " $post_join ON post.Username = profile.Username"
        . $after_post_join . ' GROUP BY post.Username ' . $sort . $limit;

    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    echo $conn->error;
    $stmt->execute();
    $comment_id = $post_id = $username = $description = null;
    render_rows(
        $stmt,
        function ($post_count, $report_count, $username, $first_name, $last_name, $email, $credibility) {
            $report_count ??= 0;
            $content = get_row_title("User: $username") . "<br>" .
                       get_row_sub("$first_name $last_name | $email | Credibility: $credibility | Recent Posts: $post_count | Report Count: $report_count");
            $edit_link = "<a href='update_user.php?username=" . urlencode($username) . "' class='edit-link'>Update</a>";
            return $content . "<br>" . $edit_link;
        },
        "Username",
        "user",
        false,
        $post_count, $report_count, $username, $first_name, $last_name, $email, $credibility
    );
    $conn->close();
    ?>
</div>

</body>
</html>
