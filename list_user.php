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
    <form method="GET" action="list_user.php">
        <input type="text" name="user_name" placeholder="Username" value="<?= htmlspecialchars($_GET['user_name'] ?? '') ?>">
        <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($_GET['first_name'] ?? '') ?>">
        <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($_GET['last_name'] ?? '') ?>">
        <input type="text" name="email" placeholder="Email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        <input type="number" name="credibility" placeholder="Min Credibility" value="<?= htmlspecialchars($_GET['credibility'] ?? '') ?>">
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
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    echo($sql);
    render_rows(
        $sql,
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
