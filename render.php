<?php

function render_rows(
    string $sql_query,
    mysqli $conn,
    callable $get_row_func,
    string $primary_key_column,
    string $table_name,
    &$bound_var,
    &...$bound_vars
): void {
    // Handle delete request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row_id']) && $_POST['table'] === $table_name) {
        $id_to_delete = $_POST['row_id'];
        delete_row_from_db($conn, $table_name, $primary_key_column, $id_to_delete);
    
        // Redirect to avoid resubmission on refresh
        // header("Location: " . $_SERVER['REQUEST_URI']);
        // exit();
    }

    $stmt = $conn->stmt_init();

    if (!$stmt->prepare($sql_query)) {
        render_statement_fail();
    } else {
        $stmt->execute();
        $stmt->bind_result($bound_var, ...$bound_vars);

        echo "<ul class='row-list'>";
        while ($stmt->fetch()) {
            $clean_var = htmlspecialchars($bound_var);
            $clean_vars = array_map('htmlspecialchars', $bound_vars);

            echo "<li style='display: flex; justify-content: space-between; align-items: center; flex-direction: row;'>
                <div>" . $get_row_func($clean_var, ...$clean_vars) . "</div>
                <form method='POST' style='margin-left: 1em;' onsubmit=\"return confirm('Delete this row?');\">
                    <input type='hidden' name='row_id' value='" . $clean_var . "'>
                    <input type='hidden' name='table' value='" . $table_name . "'>
                    <button type='submit' class='delete-btn'>Delete</button>
                </form>
              </li>";
        }
        echo "</ul>";
    }
}


function delete_row_from_db(mysqli $conn, string $table, string $column, string $value): void {
    // Sanitize table/column identifiers (never user input!)
    $allowed_tables = ['user', 'trail', 'profile', 'comment', 'post', 'location', 'explored', 'weather', 'review', 'report', 'image'];
    $allowed_columns = ['Username', 'TrailID', 'PostID', 'CommentID', 'LocationID', 'WeatherID', 'ReportID', 'ImageURL'];
    

    if (!in_array($table, $allowed_tables) || !in_array($column, $allowed_columns)) {
        echo "<p style='color:red;'>Invalid delete target.</p>";
        return;
    }

    $query = "DELETE FROM `$table` WHERE `$column` = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<p style='color:red;'>Failed to prepare delete for $table: " . htmlspecialchars($conn->error) . "</p>";
        return;
    }

    $stmt->bind_param("s", $value);
    if (!$stmt->execute()) {
        echo "<p style='color:red;'>Failed to delete row: " . htmlspecialchars($stmt->error) . "</p>";
    } else {
        echo "<p style='color:green;'>Deleted from $table where $column = '$value'</p>";
    }
    $stmt->close();
}




function get_row_title(string $column_data): string {
    return "<strong>" . $column_data . "</strong>";
}

function get_row_sub(string $column_data): string {
    return  $column_data;
}

function render_statement_fail(): void {
    echo "<p style='color:red;'>Failed to prepare SQL statement.</p>";
}

?>