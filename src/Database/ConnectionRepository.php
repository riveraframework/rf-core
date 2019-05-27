<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Database;

/**
 * Class ConnectionRepository
 *
 * @package Rf\Core\Database
 */
class ConnectionRepository {

    const OPTION_INDEX_BY_ID = 'index-by-id';

    /**
     * @var PDO[]
     */
    public static $connections = [];

    /**
     * Get a connection
     *
     * @param string $connectionName
     *
     * @return PDO
     * @throws \Exception
     */
    public static function getConnection($connectionName) {

        if(empty(self::$connections[$connectionName])) {

            // Get available connection configurations
            $availableConnections = rf_config('databases')->toArray();

            // Check if the requested connection configuration exists
            if(empty($availableConnections[$connectionName])) {
                throw new \Exception('The connection has not been configured');
            }

            // Get the current configuration
            $connConfig = $availableConnections[$connectionName];

            // Check if the connection is properly configured
            if(
                empty($connConfig['host'])
                || empty($connConfig['user'])
                || empty($connConfig['password'])
                || empty($connConfig['name'])
                || empty($connConfig['charset'])
            ) {
                throw new \Exception('The connection is not properly configured');
            }

            // Create the connection
            try {

                self::$connections[$connectionName] = new PDO($connectionName, $connConfig);

            } catch(\PDOException $e) {

                throw new \Exception('Unable to connect to the database: ' . $e->getMessage());

            }

        }

        return self::$connections[$connectionName];

    }

	/**
	 * Get the default connection
	 *
	 * @return PDO
     * @throws \Exception
	 */
    public static function getDefaultConnection() {

        $availableConnections = rf_config('databases')->toArray();

        if(!empty($availableConnections['default'])) {

            return self::getConnection('default');

        } else {
            
            $keys = array_keys($availableConnections);
            $name = array_shift($keys);

            return self::getConnection($name);

        }

    }

}