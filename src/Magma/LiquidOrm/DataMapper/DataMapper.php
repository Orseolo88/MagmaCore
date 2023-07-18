<?php

declare(strict_types=1);

namespace Magma\LiquidOrm\DataMapper;

use Magma\DatabaseConnection\DatabaseConnectionInterface;
use Magma\LiquidOrm\DataMapper\Exception\DataMapperException;
use PDO;
use PDOStatement;
use Throwable;

class DataMapper implements DataMapperInterface
{

    /**
     * @var DatabaseConnectionInterface
     */
    private DatabaseConnectionInterface $dbh;

    /**
     * @var PDOStatement
     */
    private PDOStatement $statement;

    /**
     * Main constructor class
     * @param DatabaseConnectionInterface $dbh
     */
    public function __construct(DatabaseConnectionInterface $dbh)
    {
        $this->dbh = $dbh;
    }

    private function isEmpty($value, string $errorMessage = null)
    {
        if (empty($value)) {
            throw new DataMapperException($errorMessage);
        }
    }

    private function isArray($value)
    {
        if (!is_array($value)) {
            throw new DataMapperException('Your argument needs to be an array');
        }
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $sqlQuery): self
    {
        $this->statement = $this->dbh->open()->prepare($sqlQuery);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bind($value)
    {
        try {
            switch ($value) {
                case is_bool($value):
                case intval($value):
                    $dataType = PDO::PARAM_INT;
                    break;
                case is_null($value):
                    $dataType = PDO::PARAM_NULL;
                    break;
                default:
                    $dataType = PDO::PARAM_STR;
                    break;
            }
            return $dataType;
        } catch (DataMapperException $exception) {
            throw $exception;
        }
    }

    /**
     * Bind a value to a corresponding name or question mark placeholder in the SQL
     * statement that was used to prepare the statement
     * @param array $fields
     * @return PDOStatement
     * @throws DataMapperException
     */
    protected function bindValue(array $fields)
    {
        $this->isArray($fields);

        foreach ($fields as $key => $value) {
            $this->statement->bindValue(':' . $key, $value, $this->bind($value));
        }
        return $this->statement;
    }

    /**
     * @inheritDoc
     */
    public function bindParameters(array $fields, bool $isSearch = false): self
    {
        if (is_array($fields)) {
            $type = ($isSearch == false) ? $this->bindValue($fields) : $this->bindSearchValues($fields);
            if ($type) {
                return $this;
            }
        }
        return false;
    }

    protected function bindSearchValues(array $fields)
    {
        $this->isArray($fields);

        foreach ($fields as $key => $value) {
            $this->statement->bindValue(':' . $key, '%' . $value . '%', $this->bind($value));
        }
        return $this->statement;
    }

    /**
     * @inheritDoc
     */
    public function numRows(): int
    {
        if ($this->statement)
            return $this->statement->rowCount();
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->statement)
            return $this->statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function result(): object
    {
        if ($this->statement) {
            return $this->statement->fetch(PDO::FETCH_OBJ);
        }
    }

    /**
     * @inheritDoc
     */
    public function results(): array
    {
        if ($this->statement)
            return $this->statement->fetchAll();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function getLastId(): int
    {
        try {
            if ($this->dbh->open()) {
                $lastID = $this->dbh->open()->lastInsertId();
                if (!empty($lastID)) {
                    return intval($lastID);
                }
            }
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}