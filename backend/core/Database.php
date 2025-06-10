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

        //<nn>
        // Set DSN (Data Source Name) - not strictly DSN for mysqli, but conceptual
        // Create a new mysqli connection
        //</nn>

        $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        //<nn>
        // Check for connection errors
        //</nn>
        if ($this->connection->connect_error) {
            $this->error = "Connection failed: " . $this->connection->connect_error;
            die("Database connection raised exception: " . $this->error);
        }
        //<nn>
        // Optional: Set charset to utf8mb4 for full Unicode support
        //</nn>
        if (!$this->connection->set_charset("utf8mb4")) {
            // Handle error if charset setting fails
            // printf("Error loading character set utf8mb4: %s\n", $this->connection->error);
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

    // You will add methods here for prepared statements, executing queries, etc.
    // For example:
    // public function query($sql) { ... }
    // public function prepare($sql) { ... }
    // public function execute() { ... }
    // public function single() { ... }
    // public function resultSet() { ... }
}
