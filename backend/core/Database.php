<?php
//<M>
//×-
//@-FILENAME   : C:\Users\ax07057\PROGRAMOK\XAMPP\xampp\htdocs\DNTST_REG\backend\core - Database.php -@
//@-AUTHOR     : AX07057+ G.Gemini-@
//@-CREATED ON : 2025-06-10  -@
//@-DEPENDECIES:
//×-
// @-- /config.php -@
//-×
//-@
//@-DESCRIPTION :
// The database conection handler
// -@
//@-CHANGES     :
//×-
// @-- CHANGE_DATE :<br>
// Short description of the applied change...
//-@
//-×
//-×
//</M>
require_once __DIR__ . '/config.php'; // Load database configuration

class Database
{

    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $connection;
    private $error;


    public function __construct()
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Simple conttructor.<br>
        // PARAMETERS:
        //×-
        // @-- @param = no parameter needed (comes from config) -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            //<nn>
            // Set DSN (Data Source Name) - not strictly DSN for mysqli, but conceptual
            // Create a new mysqli connection
            //</nn>
            $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

            //<nn>
            // Optional: Set charset to utf8mb4 for full Unicode support
            //</nn>
            if (!$this->connection->set_charset("utf8mb4")) {
                // This will throw an exception if mysqli_report is active and charset setting fails.
                // For older PHP/mysqli versions or if mysqli_report is not fully effective for set_charset,
                // an explicit throw here ensures the error is handled as an exception.
                throw new Exception("Error loading character set utf8mb4: " . $this->connection->error);
            }
        } catch (Exception $e) {
            // Log the error server-side for debugging purposes.
            error_log("Database Initialization Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

            // Rethrow the exception. This allows it to be caught by the global ErrorHandler 
            // (set in index.php), which will then send a standardized JSON error response to the client.
            throw $e;
        }
    }

    public function getConnection()
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Get the mysqli connection object.<br>
        // Return mysqli|null The mysqli connection object or null on failure.
        // PARAMETERS:
        //×-
        // @-- @param = ... -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>

        if ($this->connection && !$this->connection->connect_error) {
            return $this->connection;
        }
        return null;
    }

    public function getError()
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: USERNAME$<br>
        // Get the last error message.<br>
        // Return string|null The last error message or null if no error.
        // PARAMETERS:
        //×-
        // @-- @param = no parameter needed -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>
        return $this->error;
    }

    public function close()
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Close the database connection.<br>
        // PARAMETERS:
        //×-
        // @-- @param = no parameters needed -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function GNRL_SELECT(array $prmObj): array
    {
        //<SF>
        // CREATED ON: 2024-06-11 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // General selection query generator, based on prm obj, using prepared statements.<br>
        // PARAMETERS:
        //×-
        // @-- @prmObj = an object with several elements: DB_NAME,TBL_NM,FIELD_NAMES,FILTERS,ENDCLOSURES,FUNC_NM -@
        //    - DB_NAME (optional): Database name. Defaults to class dbname.
        //    - TBL_NAME (required): Table name.
        //    - FIELD_NAMES (optional): Array of field names. Defaults to ['*'].
        //    - FILTERS (optional): Array of filter objects. Each filter:
        //        - FLD_NAME (required): Field name for the filter.
        //        - RELATION (required): Comparison operator (=, !=, >, <, >=, <=, LIKE, IS NULL, IS NOT NULL, IN, etc.).
        //        - VALUE (required for most relations): The value to compare against. Should be raw value, NOT quoted. Use array for IN.
        //        - CONNECTOR (optional): Logical connector (AND, OR). Defaults to 'AND' between filters. Last filter should have empty or no connector.
        //    - ENDCLOSURES (optional): Array of strings for GROUP BY, ORDER BY, LIMIT, etc.
        //    - FUNC_NM (optional): Calling function name for logging/messaging.
        //-×
        //CHANGES:
        //×-
        // @-- 2024-06-11 : Implemented using prepared statements for security. -@
        // @-- 2024-06-11 : Integrated with _generateResponse helper. -@
        //-×
        //</SF>

        $dbName = $prmObj['DB_NAME'] ?? $this->dbname; // Use class default if not provided
        $tblName = $prmObj['TBL_NAME'] ?? '';
        $fieldNames = $prmObj['FIELD_NAMES'] ?? ['*'];
        $filters = $prmObj['FILTERS'] ?? [];
        $endClosures = $prmObj['ENDCLOSURES'] ?? [];
        $funcName = $prmObj['FUNC_NM'] ?? 'GNRL_SELECT'; // For message signature

        // Basic validation
        if (empty($tblName)) {
            $msg = '<p class="bg-danger">Database->' . $funcName . ': Table name not provided for SELECT.</p>';
            return $this->_generateResponse('NOK', $msg, [], '', $this->connection, 'SELECT');
        }

        // Build the base query
        $sql = "SELECT " . implode(", ", array_map(function ($f) {
            return "$f";
        }, $fieldNames)) . " FROM " . $dbName . "." . $tblName;

        $params = []; // Array to hold values for binding
        $types = ""; // String to hold types for binding

        // Build WHERE clause with placeholders
        if (!empty($filters)) {
            $sql .= " WHERE ";
            $whereClauses = [];
            $filterIndex = 0; // Use a separate index for filters to handle connectors

            foreach ($filters as $filter) {
                $fieldName = $filter['FLD_NAME'] ?? '';
                $relation = $filter['RELATION'] ?? '=';
                $value = $filter['VALUE'] ?? null; // Can be null for IS NULL/IS NOT NULL
                $connector = $filter['CONNECTOR'] ?? ($filterIndex < count($filters) - 1 ? 'AND' : ''); // Default connector

                if (empty($fieldName)) {
                    // Log this warning server-side
                    error_log("Database->{$funcName}: Filter missing FLD_NAME.");
                    continue; // Skip invalid filter
                }

                $clause = "" . $fieldName . " " . $relation;

                // Handle relations that don't use a value or use a placeholder differently
                if (in_array(strtoupper($relation), ['IS NULL', 'IS NOT NULL'])) {
                    // No placeholder or binding needed for IS NULL/IS NOT NULL
                    $whereClauses[] = $clause . " " . $connector;
                } elseif (strtoupper($relation) === 'IN' && is_array($value)) {
                    if (empty($value)) {
                        // Handle empty IN clause - usually means no results
                        $whereClauses[] = "1=0"; // Force no results if IN list is empty
                    } else {
                        $placeholders = implode(", ", array_fill(0, count($value), '?'));
                        $clause .= " (" . $placeholders . ")";
                        $whereClauses[] = $clause . " " . $connector;
                        // Add each value from the array to params and infer type
                        foreach ($value as $val) {
                            $params[] = $val;
                            if (is_int($val)) $types .= 'i';
                            elseif (is_double($val)) $types .= 'd';
                            else $types .= 's';
                        }
                    }
                } else {
                    // Default case: relation uses a single value placeholder
                    $clause .= " ?";
                    $whereClauses[] = $clause . " " . $connector;
                    $params[] = $value; // Add the single value to params
                    // Infer type (basic inference)
                    if (is_int($value)) $types .= 'i';
                    elseif (is_double($value)) $types .= 'd';
                    else $types .= 's';
                }
                $filterIndex++; // Increment index after processing a valid filter
            }
            // Join the clauses, removing any trailing connector from the last clause
            $sql .= rtrim(implode(" ", $whereClauses));
        }

        // Add end closures (GROUP BY, ORDER BY, LIMIT, etc.)
        if (!empty($endClosures)) {
            $sql .= " " . implode(" ", $endClosures);
        }
        $sql .= ";";

        // Execute the prepared statement
        try {
            $stmt = $this->connection->prepare($sql);

            // Bind parameters if there are any
            if (!empty($types) && !empty($params)) {
                // Use call_user_func_array for bind_param with dynamic parameters
                // Note: ...$params syntax (splat operator) is cleaner in PHP 5.6+
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result(); // Get mysqli_result object
            $rows = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results as associative array
            $stmt->close();

            $msg = '<p class="bg-success">QUERY OK!!!<br>SELECT executed successfully.<br>Signature:Database->' . $funcName . '</p>';
            return $this->_generateResponse('OK', $msg, $rows, $sql, $this->connection, 'SELECT');
        } catch (Exception $e) {
            // Catch exceptions thrown by mysqli_report or manual throws
            $errorMsg = htmlspecialchars($e->getMessage()); // Sanitize message for HTML output
            $msg = '<p class="bg-danger">QUERY ERROR!!!<br>Query: <code>' . htmlspecialchars($sql) . '</code><br>Error: <code>' . $errorMsg . '</code><br>Signature:Database->' . $funcName . '</p>';
            // Log the detailed error server-side
            error_log("Database->{$funcName} Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\nQuery: " . $sql . "\nParams: " . json_encode($params));

            // Return NOK response
            return $this->_generateResponse('NOK', $msg, [], $sql, $this->connection);
        }
    }

    public function GNRL_INSERT(array $prmObj): array
    {
        //<SF>
        // CREATED ON: 2025-06-12 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // General insertion query, based on prm obj, using prepared statements.<br>
        // PARAMETERS:
        //×-
        // @-- @prmObj = an object with several elements: DB_NAME,TBL_NM,FIELD_NAMES,VALUES,FUNC_NM -@
        //    - DB_NAME (optional): Database name. Defaults to class dbname.
        //    - TBL_NAME (required): Table name.
        //    - FIELD_NAMES (required): Array of field names for the insert.
        //    - VALUES (required): Array of values corresponding to FIELD_NAMES for a single row.
        //    - FUNC_NM (optional): Calling function name for logging/messaging.
        //-×
        //CHANGES:
        //×-
        // @-- 2025-06-12 : Implemented using prepared statements for security. -@
        // @-- 2025-06-12 : Integrated with _generateResponse helper. -@
        //-×
        //</SF>

        $dbName = $prmObj['DB_NAME'] ?? $this->dbname;
        $tblName = $prmObj['TBL_NAME'] ?? '';
        $fieldNames = $prmObj['FIELD_NAMES'] ?? [];
        $values = $prmObj['VALUES'] ?? []; // Expecting a single array of values for one row
        $funcName = $prmObj['FUNC_NM'] ?? 'GNRL_INSERT';

        // Basic validation
        if (empty($tblName) || empty($fieldNames) || empty($values)) {
            $msg = '<p class="bg-danger">Database->' . $funcName . ': Table name, field names, or values not provided for INSERT.</p>';
            return $this->_generateResponse('NOK', $msg, [], '', $this->connection, 'INSERT');
        }

        if (count($fieldNames) !== count($values)) {
            $msg = '<p class="bg-danger">Database->' . $funcName . ': The number of field names does not match the number of values for INSERT.</p>';
            return $this->_generateResponse('NOK', $msg, [], '', $this->connection, 'INSERT');
        }

        // Build the SQL query
        $sqlFieldNames = implode(", ", array_map(function ($f) {
            return "`$f`";
        }, $fieldNames));
        $placeholders = implode(", ", array_fill(0, count($values), '?'));
        $sql = "INSERT INTO `" . $dbName . "`.`" . $tblName . "` (" . $sqlFieldNames . ") VALUES (" . $placeholders . ");";

        $types = ""; // String to hold types for binding
        foreach ($values as $value) {
            if (is_int($value)) $types .= 'i';
            elseif (is_double($value)) $types .= 'd';
            // elseif (is_bool($value)) $types .= 'i'; // Booleans can be treated as integers (0 or 1)
            // elseif (is_null($value)) $types .= 's'; // Or handle NULLs specifically if needed, often 's' works with NULL
            else $types .= 's'; // Default to string
        }

        // Execute the prepared statement
        try {
            $stmt = $this->connection->prepare($sql);

            if (!empty($types)) { // Should always be true if $values is not empty
                $stmt->bind_param($types, ...$values);
            }

            $stmt->execute();
            $insertId = $this->connection->insert_id;
            $affectedRows = $stmt->affected_rows;
            $stmt->close();

            $msg = '<p class="bg-success">QUERY OK!!!<br>INSERT executed successfully.<br>Signature:Database->' . $funcName . '</p>';
            return $this->_generateResponse('OK', $msg, ['insert_id' => $insertId, 'affected_rows' => $affectedRows], $sql, $this->connection, 'INSERT');
        } catch (Exception $e) {
            $errorMsg = htmlspecialchars($e->getMessage());
            $msg = '<p class="bg-danger">QUERY ERROR!!!<br>Query: <code>' . htmlspecialchars($sql) . '</code><br>Error: <code>' . $errorMsg . '</code><br>Signature:Database->' . $funcName . '</p>';
            error_log("Database->{$funcName} Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\nQuery: " . $sql . "\nParams: " . json_encode($values));
            return $this->_generateResponse('NOK', $msg, [], $sql, $this->connection);
        }
    }


    //+---------------------------------------------------------------------------------------+
    //|##########################   P R I V A T E   S E C T I O N    #########################|
    //+---------------------------------------------------------------------------------------+

    private function _generateResponse(string $flag, string $msg, array $data = [], string $query = '', ?mysqli $conObj = null, string $operationType = ''): array
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Gneerate a standard DB response object.<br>
        // PARAMETERS:
        //×-
        // @-- $flag = the FLAG element of the answer -@
        // @-- $msg = the MSF element of the answer -@
        // @-- $data = the DATA element of the answer (SELECT result) -@
        // @-- $query = the QRY element of the answer -@
        // @-- $conObj = the MYSQLI CONNECTION object to get further elements -@
        // @-- $operationType = the database operation type. -@
        //-×
        //
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>
        $response = [
            'FLAG' => $flag,
            'MSG' => $msg, // Note: For APIs, plain text messages are generally preferred over HTML.
            'DATA' => $data,
            'QRY' => $query
        ];

        // Add mysqli_info for non-SELECT operations on success, if connection object is provided
        if ($flag === 'OK' && $conObj && $operationType !== 'SELECT' && $operationType !== '') {
            // Check if mysqli_info is available and returns something meaningful
            $info = @mysqli_info($conObj); // Use @ to suppress warnings if info is not available
            if ($info) {
                // Append to existing message or add a new field
                $response['MSG'] .= '<br>MYSQLI_INFO: ' . htmlspecialchars($info);
            }
        }
        return $response;
    }

    // You will add methods here for prepared statements, executing queries, etc.
    // For example:
    // public function query($sql) { ... }
    // public function prepare($sql) { ... }
    // public function execute() { ... }
    // public function single() { ... }
    // public function resultSet() { ... }
}
