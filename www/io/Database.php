<?php
namespace Mediaio;


class Database
{
    //public static mixed $credentials;

    private static function getCredentials(){
        $file_path = $_SERVER["DOCUMENT_ROOT"] . '/server/dbCredentials.json';
        $json_data = file_get_contents($file_path);
        return json_decode($json_data, true);
    }

    public static function createObject() {
        return new self(); // Instantiating the current class
    }

    static function runQuery($query, $schema = null)
    {
        $credentials=self::getCredentials();
        if ($schema === null) {
            $schema = $credentials['schema'];
        }

        // Extract the username, password, and schema fields
        $username = $credentials['username'];
        $password = $credentials['password'];

        /* Runs an SQL query on the databse, and returns it's result. 
            - Doesn't check the query, it blindly runs it !
            - Doesn't close the databse connection either.
        */
        $connection = mysqli_connect('mysql', $username, $password, $schema);
        mysqli_set_charset($connection, "utf8mb4");
        if (!$connection) {
            echo "Errored";
            die ("Connection failed: " . mysqli_connect_error());
        }
        //$connection->close();
        return $connection->query($query);
    }

    //Runs query, and returns the mysqli object as a result.

    //Caller object SHOULD close the connection!
    static function runQuery_mysqli($schema = null)
    {
        $credentials=self::getCredentials();
        if ($schema === null) {
            $schema = $credentials['schema'];
        }

        // Extract the username, password, and schema fields
        $username = $credentials['username'];
        $password = $credentials['password'];

        $connection = mysqli_connect('mysql', $username, $password, $schema);
        mysqli_set_charset($connection, "utf8mb4");
        if (!$connection) {
            die ("Connection failed: " . mysqli_connect_error());
        }
        return $connection;
    }
}
