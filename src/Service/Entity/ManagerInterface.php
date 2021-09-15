<?php

namespace App\Service\Entity;

interface ManagerInterface {

    /**
     * @param array $data
     * @param bool $andFlush
     * @return object
     */
    public function create(array $data, bool $andFlush = false): object;

    /**
     * @param object $object
     * @param array $data
     * @return object
     */
    public function update(object $object, array $data, bool $andFlush = false): object;

    /**
     * @param string $id
     * @return object|null
     */
    public function getById(string $id): ?object;

    /**
     * @param object $object
     * @return bool
     */
    public function save(object $object): bool;

    /**
     * @param object $object
     * @return bool
     */
    public function remove(object $object): bool;

    /**
     * @return string
     */
    public function getEntityClass(): string;
}
