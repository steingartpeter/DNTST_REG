<?php

// backend/controllers/UserAuthApiController.php

class UserAuthApiController
{
    private $databaseService;
    private $auth;

    public function __construct()
    {
        // Ideally, inject dependencies, but for simplicity:
        $this->databaseService = new Database(); // Assumes Database.php defines this class
        $this->auth = new Auth($this->databaseService); // Auth might need DB connection
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

        $prmObj = [
            'FUNC_NM' => 'UserAuthApiController-controllertest',
            'TYPE' => 'SELECT',
            'DB_NAME' => 'dentist_regs',
            'TBL_NAME' => 'users u 
            LEFT OUTER JOIN dentist_regs.dentistprofiles p ON u.id = p.user_id',
            'FIELD_NAMES' => ["u.*", "COALESCE(p.specialization, '') AS 'SPEC'", "COALESCE(p.license_number, '')AS 'LCNC_NR'"],
            'FILTERS' => [
                ['FLD_NAME' => 'u.id', 'RELATION' => '>', 'VALUE' => "0", 'CONNECTOR' => ''],
            ],
            'ENDCLOSURES' => ["HAVING SPEC != ''"],
        ];
        $db0 = $this->databaseService;
        $dbRsp0 = $db0->GNRL_SELECT($prmObj);
        echo json_encode($dbRsp0, JSON_UNESCAPED_UNICODE);
        //echo json_encode(['FLAG' => 'OK', 'MSG' => 'CALL OK: UserAuthApiCOntroller=>controllertest()', 'DATA' => []], JSON_UNESCAPED_UNICODE);
    }

    // Other methods like logout, getProfile, etc.
}
