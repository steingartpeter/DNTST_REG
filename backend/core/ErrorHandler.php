<?php
//<M>
//×-
//@-FILENAME   : C:\Users\ax07057\PROGRAMOK\XAMPP\xampp\htdocs\DNTST_REG\backend\core - ErrorHandler.php -@
//@-AUTHOR     : AX07057+G.Gemini-@
//@-CREATED ON : 2025-06-10  -@
//@-DEPENDECIES:
//×-
// @-- NONE -@
//-×
//-@
//@-DESCRIPTION :
// A general error handler/formatter to send the to frontend.
// -@
//@-CHANGES     :
//×-
// @-- CHANGE_DATE :<br>
// Short description of the applied change...
//-@
//-×
//-×
//</M>

class ErrorHandler
{

    public static function handleException(Throwable $exception): void
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        //  Handles uncaught exceptions.Converts the exception into a JSON response.<br>
        // Returns void.
        // PARAMETERS:
        //×-
        // @-- @param = Throwable $exception The exception that was thrown. -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>

        //<nn>
        // Ensure headers are not already sent
        //</nn>
        if (!headers_sent()) {
            http_response_code(500); // Internal Server Error
            header('Content-Type: application/json');
        }

        $response = [
            'status' => 'error',
            'message' => 'An unexpected error occurred.',
            'error_details' => [ // Keep detailed info for development/logging
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                // 'trace' => $exception->getTraceAsString() // Optional: for more detailed debugging
            ]
        ];
        echo json_encode($response);
        exit;
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        //<SF>
        // CREATED ON: 2025-06-10 <br>
        // CREATED BY: AX07057+G.Gemini<br>
        // Handles PHP errors (warnings, notices, etc.).Converts them into ErrorException instances, 
        // which are then caught by the exception handler.<br>
        // Return bool.
        // PARAMETERS:
        //×-
        // @-- $errno =  int The error level. -@
        // @-- $errstr =  string The error message. -@
        // @-- $errfile =  string The filename where the error occurred. -@
        // @-- $errline =  string The line number where the error occurred. -@
        //-×
        //CHANGES:
        //×-
        // @-- ... -@
        //-×
        //</SF>

        //<nn>
        // Don't throw exception for errors suppressed with @
        //</nn>
        if (!(error_reporting() & $errno)) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
