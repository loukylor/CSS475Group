<?php

function render_rows(
    mysqli_stmt $stmt,
    callable $get_row_func,
    string $primary_key_column,
    string $table_name,
    bool $is_composite = false,
    &$bound_var,
    &...$bound_vars
): void {
    $stmt->execute();
    $stmt->bind_result($bound_var, ...$bound_vars);

    $rows = [];
    while ($stmt->fetch()) {
        $row = [$bound_var];
        foreach ($bound_vars as $var) {
            $row[] = $var;
        }
        $rows[] = $row;
    }

    echo "<ul class='row-list'>";
    foreach ($rows as $row_data) {
        $clean_var = htmlspecialchars($row_data[0]);
        $clean_vars = array_map('htmlspecialchars', array_slice($row_data, 1));

        $form_fields = "";
        $row_id = $clean_var;

        if ($is_composite && strpos($primary_key_column, '|') !== false) {
            $key_parts = explode('|', $primary_key_column);
            $row_id_parts = [];

            foreach ($key_parts as $index => $key_name) {
                $field_value = htmlspecialchars($row_data[$index]);
                $row_id_parts[] = $field_value;
                $form_fields .= "<input type='hidden' name='" . strtolower($key_name) . "' value='$field_value'>\n";
            }

            $row_id = implode('|', $row_id_parts);
        }

        echo "<li style='display: flex; justify-content: space-between; align-items: center; flex-direction: row;'>
            <div>" . $get_row_func(...$row_data) . "</div>";
        if (!empty($primary_key_column)) {
            echo "<form method='POST' style='margin-left: 1em;' onsubmit=\"return confirm('Delete this row?');\">
                <input type='hidden' name='row_id' value='" . $row_id . "'>
                <input type='hidden' name='table' value='" . $table_name . "'>
                $form_fields
                <button type='submit' class='delete-btn'>Delete</button>
            </form>";
        }
        echo "</li>";
    }
    echo "</ul>";
}



function delete_composite_row_from_db(mysqli $conn, string $table, string $composite_key): void {
    $key_parts = explode('|', $composite_key);
    $where_clauses = [];
    $param_types = "";
    $param_values = [];

    foreach ($key_parts as $key) {
        $form_key = strtolower($key);
        $value = $_POST[$form_key] ?? null;

        if ($value === null) {
            echo "<p style='color:red;'>Missing form value for key '$form_key'</p>";
            return;
        }

        // Bind type inference: assume int if it's numeric and not a string of letters
        if (is_numeric($value) && (string)(int)$value === (string)$value) {
            $param_types .= "i";
            $param_values[] = (int)$value;
        } else {
            $param_types .= "s";
            $param_values[] = $value;
        }

        $where_clauses[] = "$key = ?";
    }

    $where_sql = implode(" AND ", $where_clauses);
    $query = "DELETE FROM `$table` WHERE $where_sql";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<p style='color:red;'>Failed to prepare composite delete: " . htmlspecialchars($conn->error) . "</p>";
        return;
    }

    // Bind dynamically
    $stmt->bind_param($param_types, ...$param_values);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo "<p style='color:orange;'>No row matched composite key for deletion.</p>";
    } else {
        echo "<p style='color:green;'>Deleted row from $table with composite key: $composite_key</p>";
    }

    $stmt->close();
}



function delete_row_from_db(mysqli $conn, string $table, string $column, string $value): void {
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