<?php

// backend/controllers/UserAuthApiController.php

class UserAuthApiController
{
    private $db;
    private $auth;

    public function __construct()
    {
        // Ideally, inject dependencies, but for simplicity:
        $this->db = new Database(); // Assumes Database.php defines this class
        $this->auth = new Auth($this->db->getConnection()); // Auth might need DB connection
    }

    public function register($requestData)
    {
        // Validate $requestData (email, password, etc.)
        // ... validation logic ...
        if (empty($requestData['email']) || empty($requestData['password'])) {
            sendJsonResponse(['status' => 'error', 'message' => 'Email and password are required.'], 400);
            return;
        }

        // Call Auth class method
        $result = $this->auth->registerUser($requestData['email'], $requestData['password'], $requestData['first_name'] ?? '', $requestData['last_name'] ?? '');

        if ($result['status'] === 'success') {
            sendJsonResponse($result, 201); // 201 Created
        } else {
            sendJsonResponse($result, 400); // Or 409 Conflict if email exists
        }
    }

    public function login($requestData)
    {
        // ... similar logic for login ...
        // On success, might return a session ID (if using sessions) or a JWT
        // and call sendJsonResponse()
    }

    public function controllertest($requestData)
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057<br>
        // Dummy function for testing connection, and function.<br>
        // PARAMETERS:
        //×-
        // @-- @param = ... -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>

        echo json_encode(['FLAG' => 'OK', 'MSG' => 'CALL OK: UserAuthApiCOntroller=>controllertest()', 'DATA' => []], JSON_UNESCAPED_UNICODE);
    }

    // Other methods like logout, getProfile, etc.
}
