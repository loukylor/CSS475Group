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
    <a href="./insert.php?table=user">Insert into table</a>

    <h3>Filter Table</h3>
    <form method="GET" action="list_user.php">
        <input type="text" name="user_name" placeholder="Username" value="<?= htmlspecialchars($_GET['user_name'] ?? '') ?>">
        |
        <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($_GET['first_name'] ?? '') ?>">
        |
        <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($_GET['last_name'] ?? '') ?>">
        |
        <input type="text" name="email" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        |
        <input type="number" name="credibility" placeholder="Min Credibility" value="<?= htmlspecialchars($_GET['credibility'] ?? '') ?>">

        <br>
        
        <label for="do_sort">Sort?:</label>
        <input type="checkbox" name="do_sort" <?= (isset($_GET['do_sort']) ? 'checked' : '') ?> />
        <label for="order_by">Order by:</label>
        <select name="order_by">">
            <option value="Username" <?= $_GET['order_by'] === "Username" ? 'selected' : '' ?>>Username</option>
            <option value="FirstName" <?= $_GET['order_by'] === "FirstName" ? 'selected' : '' ?>>FirstName</option>
            <option value="LastName" <?= $_GET['order_by'] === "LastName" ? 'selected' : '' ?>>LastName</option>
            <option value="Email" <?= $_GET['order_by'] === "Email" ? 'selected' : '' ?>>Email</option>
            <option value="Credibility" <?= $_GET['order_by'] === "Credibility" ? 'selected' : '' ?>>Credibility</option>
        </select>
        <label for="order">Ascending?:</label>
        <input type="checkbox" name="order" <?= (isset($_GET['order']) ? 'checked' : '') ?> />
        |
        <input type="number" placeholder="Limit" name="limit" value="<?= htmlspecialchars($_GET['limit'] ?? '') ?>" />
        |
        <button type="submit">Filter</button>
        <button type="reset" onclick="window.location.href='list_user.php';">Clear</button>
    </form>
    <?php
    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }
    $first_name = $_GET['first_name'] ?? '';
    $last_name = $_GET['last_name'] ?? '';
    $email = $_GET['email'] ?? '';
    $username = $_GET['user_name'] ?? '';
    $credibility = $_GET['credibility'] ?? '';

    $order = isset($_GET['order']) ? 'ASC' : 'DESC';
    $sort = isset($_GET['do_sort']) ? " ORDER BY {$_GET['order_by']} $order" : '';
    $limit = (($_GET['limit'] ?? '') !== '') ? " LIMIT {$_GET['limit']}" : '';

    // Start base query
    $sql = "SELECT Username, FirstName, LastName, Email, Credibility FROM user WHERE 1=1";
    $params = [];
    $types = "";

    // Add filters
    if ($username !== "") {
        $sql .= " AND Username LIKE ?";
        $params[] = "%$username%";
        $types .= "s";
    }
    if ($first_name !== "") {
        $sql .= " AND FirstName LIKE ?";
        $params[] = "%$first_name%";
        $types .= "s";
    }

    if ($last_name !== "") {
        $sql .= " AND LastName LIKE ?";
        $params[] = "%$last_name%";
        $types .= "s";
    }
   
    if ($email !== "") {
        $sql .= " AND Email = ?";
        $params[] = $email;
        $types .= "s";
    }
    if ($credibility !== "") {
        $sql .= " AND Credibility >= ?";
        $params[] = (int)$credibility;
        $types .= "i";
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === 'user') {
        delete_row_from_db($conn, 'user', 'Username', $_POST['row_id']);
    }

    $sql .= $sort . $limit;

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    render_rows(
        $stmt,
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
    
    ?>
</div>

</body>
</html>
