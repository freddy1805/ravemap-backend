<?php

namespace App\Service\Entity;

use App\Exception\ValidationException;
use App\Util\EntityMapper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class BaseManager implements ManagerInterface {

    protected string $repoName = '';

    protected ObjectRepository $repository;

    protected EntityManagerInterface $entityManager;

    /**
     * @var array
     */
    protected array $validation = [];

    /**
     * BaseManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($this->repoName);
    }

    /**
     * @param array $data
     * @param bool $andFlush
     * @return object
     * @throws ValidationException
     */
    public function create(array $data, bool $andFlush = false): object
    {
        $object = $this->mapArrayToEntity($data);

        if ($andFlush) {
            $this->save($object);
        }

        return $object;
    }

    /**
     * @param object $object
     * @param array $data
     * @param bool $andFlush
     * @return object
     */
    public function update(object $object, array $data, bool $andFlush = false): object
    {
        $object = EntityMapper::updateEntityByArray($object, $data);

        if ($andFlush) {
            $this->save($object);
        }

        return $object;
    }

    /**
     * @param string $id
     * @return object|null
     */
    public function getById(string $id): ?object
    {
        return $this->repository->find($id);
    }

    /**
     * @param object $object
     * @return bool
     */
    public function save(object $object): bool
    {
        try {
            $this->entityManager->persist($object);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param object $object
     * @return bool
     */
    public function remove(object $object): bool
    {
        try {
            $this->entityManager->remove($object);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param array $data
     * @return object
     * @throws ValidationException
     */
    public function mapArrayToEntity(array $data): object
    {
        $validationResult = $this->validateData($data);

        if (!empty($validationResult)) {
            throw new ValidationException($validationResult);
        }

        $className = $this->getEntityClass();

        return EntityMapper::arrayToEntity($className, $data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function validateData(array $data): array
    {
        $errors = [];

        foreach ($this->validation as $validationKey) {
            if (!isset($data[$validationKey])) {
                $errors[$validationKey] = 'not defined';
            }
        }

        return $errors;
    }

    public function getEntityClass(): string
    {
    }
}
