<?php

/**
 * ODBC Helper Class
 * Provides safe ODBC operations with proper error handling
 */
class OdbcHelper {
    
    /**
     * Safe ODBC prepare operation
     * @param resource $connection Database connection
     * @param string $query SQL query
     * @return resource|false Statement resource or false on failure
     */
    public static function safePrepare($connection, $query) {
        if (!$connection || !is_resource($connection)) {
            error_log("OdbcHelper::safePrepare - Invalid database connection");
            return false;
        }
        
        if (empty($query)) {
            error_log("OdbcHelper::safePrepare - Empty query provided");
            return false;
        }
        
        $statement = odbc_prepare($connection, $query);
        
        if (!$statement) {
            $error = odbc_errormsg($connection);
            error_log("OdbcHelper::safePrepare - Failed to prepare query: $query. Error: $error");
            
            if (defined('ENV') && ENV === 'development') {
                echo "<div style='color: red; font-weight: bold;'>Failed to prepare query: $error</div>";
            }
        }
        
        return $statement;
    }
    
    /**
     * Safe ODBC execute operation
     * @param resource $statement Statement resource
     * @param array $parameters Query parameters
     * @return bool True on success, false on failure
     */
    public static function safeExecute($statement, $parameters = []) {
        if (!$statement || !is_resource($statement)) {
            error_log("OdbcHelper::safeExecute - Invalid statement resource");
            return false;
        }
        
        $result = odbc_execute($statement, $parameters);
        
        if (!$result) {
            $error = odbc_errormsg();
            error_log("OdbcHelper::safeExecute - Failed to execute statement. Error: $error");
            
            if (defined('ENV') && ENV === 'development') {
                echo "<div style='color: red; font-weight: bold;'>Failed to execute statement: $error</div>";
            }
        }
        
        return $result;
    }
    
    /**
     * Safe ODBC prepare and execute in one operation
     * @param resource $connection Database connection
     * @param string $query SQL query
     * @param array $parameters Query parameters
     * @return resource|false Statement resource or false on failure
     */
    public static function safePrepareAndExecute($connection, $query, $parameters = []) {
        $statement = self::safePrepare($connection, $query);
        
        if (!$statement) {
            return false;
        }
        
        if (!self::safeExecute($statement, $parameters)) {
            return false;
        }
        
        return $statement;
    }
    
    /**
     * Safe ODBC fetch array
     * @param resource $statement Statement resource
     * @return array|false Array of results or false on failure
     */
    public static function safeFetchArray($statement) {
        if (!$statement || !is_resource($statement)) {
            error_log("OdbcHelper::safeFetchArray - Invalid statement resource");
            return false;
        }
        
        return odbc_fetch_array($statement);
    }
    
    /**
     * Safe ODBC fetch row
     * @param resource $statement Statement resource
     * @return bool True if row fetched, false otherwise
     */
    public static function safeFetchRow($statement) {
        if (!$statement || !is_resource($statement)) {
            error_log("OdbcHelper::safeFetchRow - Invalid statement resource");
            return false;
        }
        
        return odbc_fetch_row($statement);
    }
    
    /**
     * Safe ODBC result retrieval
     * @param resource $statement Statement resource
     * @param mixed $field Field number or name
     * @return mixed Result value or false on failure
     */
    public static function safeResult($statement, $field) {
        if (!$statement || !is_resource($statement)) {
            error_log("OdbcHelper::safeResult - Invalid statement resource");
            return false;
        }
        
        return odbc_result($statement, $field);
    }
    
    /**
     * Check if connection is valid
     * @param resource $connection Database connection
     * @return bool True if valid, false otherwise
     */
    public static function isValidConnection($connection) {
        return $connection && is_resource($connection);
    }
    
    /**
     * Get the last ODBC error message
     * @param resource $connection Optional connection resource
     * @return string Error message
     */
    public static function getLastError($connection = null) {
        if ($connection && is_resource($connection)) {
            return odbc_errormsg($connection);
        }
        
        return odbc_errormsg();
    }
}

/**
 * Global helper functions for backward compatibility
 */

/**
 * Safe ODBC prepare - global function
 */
function safe_odbc_prepare($connection, $query) {
    return OdbcHelper::safePrepare($connection, $query);
}

/**
 * Safe ODBC execute - global function
 */
function safe_odbc_execute($statement, $parameters = []) {
    return OdbcHelper::safeExecute($statement, $parameters);
}

/**
 * Safe ODBC prepare and execute - global function
 */
function safe_odbc_prepare_execute($connection, $query, $parameters = []) {
    return OdbcHelper::safePrepareAndExecute($connection, $query, $parameters);
}

?>