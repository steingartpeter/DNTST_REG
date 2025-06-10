<?php

class Auth
{
    private $db;

    public function __construct(mysqli $dbConn)
    {
        $this->db = $dbConn;
    }

    public function registerUser(string $email, string $password, string $firstName = '', string $lastName = '')
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Registers a new user.<br>
        // PARAMETERS:
        //×-
        // @-- $email = string, the email addres of the user to be rgistered -@
        // @-- $password = string, the password of the user to be rgistered -@
        // @-- $firstName = string, the first name of the user to be rgistered -@
        // @-- $lastName = string, the last name of the user to be rgistered -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>

        //<nn>
        // Check if requested data is provided
        //</nn>
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid or empty email provided.'];
        }
        if (empty($password)) {
            return ['status' => 'error', 'message' => 'Password cannot be empty.'];
        }

        //<nn>
        // Check if email is unique
        //</nn>
        $stmt  = $this->db->prepare("SELECT id FROM Users WHERE email = ?");
        if (!$stmt) {
            return ['status' => 'error', 'message' => 'Database error (prepare failed for email check).'];
        }
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            // Log $stmt->error
            $stmt->close();
            return ['status' => 'error', 'message' => 'Database error (execute failed for email check).'];
        }
        $stmt->store_result(); // Important for num_rows
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return ['status' => 'error', 'message' => 'Email address already in use.'];
        }
        $stmt->close();

        //<nn>
        // 3. Hash the password
        //</nn>
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            // This is a server-side issue, potentially log it.
            return ['status' => 'error', 'message' => 'Failed to hash password.'];
        }

        //<nn>
        // 4. Insert the new user
        //</nn>
        $insertStmt = $this->db->prepare("INSERT INTO Users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, 'patient')");
        if (!$insertStmt) {
            // Log $this->db->error
            return ['status' => 'error', 'message' => 'Database error (insert prepare failed).'];
        }
        // 'ssss' for four string parameters
        $insertStmt->bind_param("ssss", $firstName, $lastName, $email, $passwordHash);
        if ($insertStmt->execute()) {
            $userId = $insertStmt->insert_id; // Get the ID of the newly inserted user
            $insertStmt->close();
            return ['status' => 'success', 'message' => 'User registered successfully.', 'user_id' => $userId];
        } else {
            // Log $insertStmt->error
            $insertStmt->close();
            return ['status' => 'error', 'message' => 'Failed to register user. Database execution error.'];
        }


        //return ['FLAG' => 'OK', 'MSG' => 'Call OK at: Auth=>registerUser()', 'DATA' => []];
    }
}
