<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Database {
    private $host = "localhost"; // Host for local WAMP server
    private $dbname = "ecweb";   // Your database name
    private $username = "root";  // Default MySQL username for WAMP
    private $password = "";      // Default MySQL password for WAMP (blank by default)
    private $pdo;

    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4";
        
        try {
            // Establish the PDO connection
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Disable emulated prepares for better security
        } catch (PDOException $e) {
            // Log the error message for debugging purposes
            error_log("Database connection failed: " . $e->getMessage());
            
            // Respond with a user-friendly error message
            http_response_code(500);
            
            // Include error page or handle the error based on your needs
            include 'error.php';  // Ensure 'error.php' exists or provide an alternative error handling mechanism
            
            // Exit the script to prevent further execution
            exit;
        }
    }

    // Method to return the PDO instance
    public function getConnection() {
        return $this->pdo;
    }
}

// Create a new instance of the Database class and get the connection
$database = new Database();
$pdo = $database->getConnection();

// Now you can use $pdo for database queries
?>
