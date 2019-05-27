<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Entity;

/**
 * Class EntityHelpers
 *
 * @package Rf\Core\Entity
 *
 */
abstract class EntityHelpers {

    /**
     * Return an array of the entity using the id as key
     *
     * @param Entity[] $entitiesArray
     *
     * @return array
     */
    public static function refById($entitiesArray) {

        $entitiesById = [];
        foreach ($entitiesArray as $entity) {
            $entitiesById[$entity->getId()] = $entity;
        }

        return $entitiesById;

    }

}