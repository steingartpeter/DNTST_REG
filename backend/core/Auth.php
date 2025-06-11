<?php

class Auth
{
    private $db;

    public function __construct(Database $databaseService)
    {
        //<SF>
        // CREATED ON: 2025-06-11 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Basic contructor with one paramter.<br>
        // PARAMETERS:
        //×-
        // @--  $databaseService = Instance of our custom Database class. -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>
        $this->db = $databaseService;
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
        // @-- 2024-06-12 : Refactored to use Database::GNRL_SELECT and Database::GNRL_INSERT. -@
        //-×
        //</SF>

        //<nn>
        // 1. Validate input
        //</nn>
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->_buildResponse('error', 'Invalid or empty email provided.');
        }
        if (empty($password)) {
            return $this->_buildResponse('error', 'Password cannot be empty.');
        }
        // 2. Check if email already exists using Database::GNRL_SELECT
        $prmObjSelect = [
            'FUNC_NM' => 'Auth::registerUser_emailCheck',
            'TBL_NAME' => 'Users', // Assuming your users table is named 'Users'
            'FIELD_NAMES' => ['id'],
            'FILTERS' => [
                ['FLD_NAME' => 'email', 'RELATION' => '=', 'VALUE' => $email, 'CONNECTOR' => '']
            ]
        ];
        $emailCheckResult = $this->db->GNRL_SELECT($prmObjSelect);
        if ($emailCheckResult['FLAG'] === 'NOK') {
            //<nn>
            // Log the actual DB error message server-side for more details
            //</nn>
            error_log("Auth::registerUser - Email check DB error: " . strip_tags($emailCheckResult['MSG']));
            return $this->_buildResponse('error', 'Database error during email check. Please try again later.');
        }
        if (!empty($emailCheckResult['DATA'])) {
            return $this->_buildResponse('error', 'Email address already in use.');
        }

        //<nn>
        // 3. Hash the password
        //</nn>
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            // This is a server-side issue, potentially log it.
            error_log("Auth::registerUser - Failed to hash password for email: " . $email);
            return $this->_buildResponse('error', 'Failed to process password. Please try again.');
        }

        //<nn>
        // 4. Insert the new user using Database::GNRL_INSERT
        //</nn>
        $prmObjInsert = [
            'FUNC_NM' => 'Auth::registerUser_insertUser',
            'TBL_NAME' => 'Users',
            'FIELD_NAMES' => ['first_name', 'last_name', 'email', 'password_hash', 'role'],
            'VALUES' => [$firstName, $lastName, $email, $passwordHash, 'patient'] // Default role 'patient'
        ];
        $insertResult = $this->db->GNRL_INSERT($prmObjInsert);

        //<nn>
        // 5. Return the result
        //</nn>
        if ($insertResult['FLAG'] === 'OK') {
            $userId = $insertResult['DATA']['insert_id'] ?? null;
            return $this->_buildResponse('success', 'User registered successfully.', ['user_id' => $userId]);
        } else {
            // Log the actual DB error message server-side
            error_log("Auth::registerUser - User insert DB error: " . strip_tags($insertResult['MSG']));
            return $this->_buildResponse('error', 'Failed to register user. Please try again later.');
        }

        //return ['FLAG' => 'OK', 'MSG' => 'Call OK at: Auth=>registerUser()', 'DATA' => []];
    }



    //+-------------------------------------------------------------------------+
    //|##########            P R I V A T E   S E C T I O N            ##########|
    //+-------------------------------------------------------------------------+

    private function _buildResponse(string $status, string $message, array $data = []): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }
}
