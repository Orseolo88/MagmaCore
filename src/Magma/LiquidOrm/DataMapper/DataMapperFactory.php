<?php

declare(strict_types=1);

namespace Magma\LiquidOrm\DataMapper;

use Magma\DatabaseConnection\DatabaseConnectionInterface;
use Magma\LiquidOrm\DataMapper\Exception\DataMapperException;

class DataMapperFactory
{

    /**
     * Main contructor
     * @return void
     */
    public function __construct()
    {

    }

    public function create(string $databaseConnectionString, string $dataMapperEnvironmentCofiguration): DataMapperInterface
    {
        $credentials = (new $dataMapperEnvironmentCofiguration([]))->getDatabaseCredentials('mysql');
        $databaseConnectionObject = $databaseConnectionString($credentials);

        if (!$databaseConnectionObject instanceof DatabaseConnectionInterface) {
            throw new DataMapperException($databaseConnectionString . ' is not a valid database connection object');
        }
        return new DataMapper($databaseConnectionObject);
    }
}