<?php

include_once 'render.php';
require_once 'config.inc.php'; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queries);
$table = $queries['table'];


// Perform insertion on post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    insert_row($conn, $table);
}


function render_insert_form(mysqli $conn, string $table) {
    $cols = get_table_cols($conn, $table);

    echo "<h2>Insert into the \"{$table}\" table</h2>";
    echo '<form action="" method="post">';
    foreach ($cols as $col) {
        // Skip if the column is auto_increment
        if (strpos($col['Extra'], 'auto_increment') !== false) {
            continue;
        }

        // Get input attributes based on column type
        $length = null;
        $is_required = is_null($col['Default']) && $col['Null'] === 'NO' ? 'required' : '';
        $type = null;
        $pattern = null;
        $step = null;
        $suffix = null;
        $default = $col['Default'];
        switch ($col['Type']) {
            case 'int':
            case 'int(11)':
            case 'smallint(6)':
                $type = 'number';
                break;
            case 'float':
                $type = 'number';
                $step = '0.0001';
                break;
            case 'text':
                $type = 'text';
                break;
            case 'blob':
                $type = 'text';
                $pattern = "([0-9a-fA-F]{2})*";
                $suffix = ' (in hex bytes)';
                break;
            case 'bit(1)':
                $type = 'checkbox';
                break;
            case 'date':
                $type = 'text';
                $pattern = '\d{4}-\d{2}-\d{2}';
                $suffix = ' (YYYY-MM-DD)';
                break;
            case 'datetime':
            case 'timestamp':
                $type = 'text';
                $pattern = '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}';
                $suffix = ' (YYYY-MM-DD HH:MM:SS)';
                if ($default === 'CURRENT_TIMESTAMP') {
                    $default = date('Y-m-d H:i:s', strtotime('-7 hours'));
                }
                break;
            case 'time':
                $type = 'text';
                $pattern = '\d{2}:\d{2}:\d{2}';
                $suffix = ' (HH:MM:SS)';
                break;
            default:
                if (substr($col['Type'], 0, 7) === 'varchar') {
                    $length = (int)rtrim(explode('(', $col['Type'])[1], ')');
                    $type = "text";
                } else {
                    echo "{$col['Type']} is unknown";
                }
                break;
        }
        $length = is_null($length) ? '' : "maxLength=\"{$length}\"";
        $pattern = is_null($pattern) ? '' : "pattern=\"{$pattern}\"";
        $step = is_null($step) ? '' : "step=\"{$step}\"";
        $default = is_null($default) ? '' : "value=\"{$default}\"";

        echo "<div class=\"insert-input-div\">";
        if ($is_required === '') {
            echo "<label class=\"empty-label\" for=\"{$col['Field']}-null\">Leave null?</label>";
            echo "<input class=\"empty-input\" name=\"{$col['Field']}-null\" type=\"checkbox\" />";
        }
        
        echo "<label class=\"insert-label\" fild=\"{$col['Field']}\">{$col['Field']}{$suffix}:</label>";
        echo "<input class=\"insert-input\" name=\"{$col['Field']}\""
            . " {$is_required}"
            . " {$length}"
            . " {$pattern}"
            . " {$step}"
            . " {$default}"
            . " type=\"{$type}\""
        . "/>";
        echo "</div>";
    }
    echo '<button type="submit">submit</button>';
    echo '</form>';
}

function insert_row(mysqli $conn, string $table) {
    // Get field names for the prepared query, question marks, types and values
    $cols = get_table_cols($conn, $table);
    
    $field_names = array();
    $values = array();
    $types = '';
    $q_marks = array();

    foreach ($cols as $col) {
        // use default is not set
        if (!isset($_POST[$col['Field']]) && $col['Type'] !== 'bit(1)') {
            continue;
        }
        $field_names[] = $col['Field'];
        $q_marks[] = '?';
        $value = $_POST[$col['Field']];
        $use_null = isset($_POST[$col['Field'] . '-null']);

        switch ($col['Type']) {
            case 'int':
            case 'int(11)':
            case 'smallint(6)':
                $types .= 'i';
                break;
            case 'bit(1)':
                $types .= 'i';
                $value = isset($value) ? 1 : 0;
                break;
            case 'float':
                $types .= 'd';
                break;
            case 'text':
            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'time':
                $types .= 's';
                break;
            case 'blob':
                $types .= 'b';
                $value = hex2bin($value);
                break;
            default:
                if (substr($col['Type'], 0, 7) === 'varchar') {
                    $types .= 's';
                } else {
                    echo "{$col['Type']} is unknown";
                }
                break;
        }
        if (!$use_null) {
            $values[] = $value;
        } else {
            $values[] = null;
        }
    }
    $field_names = implode(',', $field_names);
    $q_marks = implode(',', $q_marks);

    // Prepare query    
    if (!$stmt = $conn->prepare("INSERT INTO $table ($field_names) VALUES ($q_marks);")) {
        render_statement_fail();
        return;
    }

    // Execute using user vals
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    if ($stmt->errno !== 0) {
        echo "<p style='color:red;'>Error occurred: {$stmt->error}</p>";
    } else {
        header("Location: " . "./list_$table.php");
        $conn->close();
        exit();
    }
}

function get_table_cols(mysqli $conn, string $table): array {
    // Fetch table schema
    if (!$result = $conn->query("DESCRIBE {$table};")) {
        render_statement_fail();
        return null;
    }

    // Store table schema
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php echo "<title>Insert into \"{$table}\"</title>"; ?>
    <link rel="stylesheet" href="base.css">
</head>
<body>

<?php 

require_once 'header.inc.php'; 

// Render form
render_insert_form($conn, $table);

$conn->close();

?>

</body>
</html>
