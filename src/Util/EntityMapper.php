<?php

namespace App\Util;

class EntityMapper {

    /**
     * Maps any data to an entity
     * @param string $class
     * @param array $data
     * @return object
     */
    public static function arrayToEntity(string $class, array $data): object
    {
        $object = new $class();

        foreach ($data as $key => $value) {
            $methodName = 'set' . strtoupper(substr($key, 0,1)) . substr($key, 1);
            if (method_exists($object, $methodName)) {
                switch ($methodName) {
                    case 'setTimestamp':
                    case 'setDate':
                        $object->{$methodName}(EntityMapper::stringToDate($value));
                        break;
                    default:
                        $object->{$methodName}($value);
                        break;
                }
            }
        }

        return $object;
    }

    /**
     * Maps any data to an entity
     * @param object $object
     * @param array $data
     * @return object
     */
    public static function updateEntityByArray(object $object, array $data): object
    {
        foreach ($data as $key => $value) {
            $methodName = 'set' . strtoupper(substr($key, 0,1)) . substr($key, 1);
            if (method_exists($object, $methodName)) {
                switch ($methodName) {
                    case 'setTimestamp':
                    case 'setDate':
                        $object->{$methodName}(EntityMapper::stringToDate($value));
                        break;
                    default:
                        $object->{$methodName}($value);
                        break;
                }
            }
        }

        return $object;
    }

    /**
     * @param string $date
     * @return \DateTime|false
     * @throws \Exception
     */
    public static function stringToDate(string $date)
    {
        return new \DateTime($date);
    }
}
