<?php
class Database {
    private $host = "localhost";
    private $database = "sari_sari_store";
    private $username = "root";
    private $password = "";
    private $conn;
    //Server Connection
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->database,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch(PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                throw new Exception("Database connection failed");
            }
        }
        return $this->conn;
    }

    /**
     * Execute a stored procedure with parameters
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters for the stored procedure
     * @param bool $fetchAll Whether to fetch all results or just one row
     * @return array|false The result of the stored procedure
     * @throws Exception If the procedure fails
     */
    public function callProcedure($procedureName, $params = [], $fetchAll = true) {
        try {
            $conn = $this->getConnection();
            
            // Create parameter placeholders
            $placeholders = str_repeat('?,', count($params));
            $placeholders = rtrim($placeholders, ',');
            
            // Prepare the CALL statement
            $sql = "CALL $procedureName(" . $placeholders . ")";
            error_log("[Database] Calling procedure: $sql with params: " . json_encode($params));
            
            $stmt = $conn->prepare($sql);
            
            // Execute with parameters
            $stmt->execute($params);
            
            // Return results
            $result = $fetchAll ? $stmt->fetchAll() : $stmt->fetch();
            error_log("[Database] Procedure $procedureName returned: " . json_encode($result));
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error calling procedure $procedureName: " . $e->getMessage());
            error_log("Procedure parameters: " . json_encode($params));
            throw new Exception("Database procedure error: " . $e->getMessage());
        }
    }
}
?>
