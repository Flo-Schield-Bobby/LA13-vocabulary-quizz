<?php

namespace Fsb\La13\Entity\Base\Activable;

/**
 * DeletableEntityRepositoryInterface
 *
 * Contains mandatory methods for all repositories of entities with a is_deleted property.
 * @author Florent SCHILDKNECHT
 */
interface ActivableEntityRepositoryInterface
{
     /**
     * Find all entities that have is_deleted property set up to false.
     *
     * @return array
     */
     public function findAllNotDeleted();
}
