<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Orm\Exceptions;

use Rf\Core\Base\Exceptions\DebugException;
use Rf\Core\Log\LogService;
use Rf\Core\Orm\Entity;

/**
 * Class EntityAlreadyExistException
 *
 * @package Rf\Core\Orm\Exceptions
 */
class EntityAlreadyExistException extends DebugException {

    /** @var Entity $entity */
    protected $entity;

    /**
     * EntityAlreadyExistException constructor.
     *
     * @param Entity $entity
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(Entity $entity, $message = '', $code = 0, \Exception $previous = null) {

        $this->entity = $entity;
        $finalMessage = $message !== '' ? $message : 'Entity ' . get_class($this->entity) . ' with id ' . $this->entity->getId() . ' already exists';

        parent::__construct(LogService::TYPE_ERROR, $finalMessage, $code, $previous);

    }

    /**
     * Get the existing entity
     *
     * @return Entity
     */
    public function getEntity() {

        return $this->entity;

    }

}