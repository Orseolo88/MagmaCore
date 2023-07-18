<?php

declare(strict_types=1);

namespace Magma\LiquidOrm\DataMapper;

interface DataMapperInterface
{

    /**
     * Prepare the query string
     * @param string $sqlQuery
     * @return $this
     */
    public function prepare(string $sqlQuery): self;

    /**
     *
     * @param $value
     * @return mixed
     */
    public function bind($value);

    /**
     * @param array $fields
     * @param bool $isSearch
     * @return mixed
     */
    public function bindParameters(array $fields, bool $isSearch = false): self;

    /**
     * @return int
     */
    public function numRows(): int;

    /**
     * @return void
     */
    public function execute(): void;

    /**
     * @return object
     */
    public function result(): object;

    /**
     * @return array
     */
    public function results(): array;

    /**
     * Return the last inserted row ID from database table
     * @return int
     */
    public function getLastId(): int;
}