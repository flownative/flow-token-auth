<?php
namespace Flownative\TokenAuthentication\Security\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Doctrine\Repository;
use Neos\Flow\Persistence\QueryResultInterface;
use Flownative\TokenAuthentication\Security\Model\HashAndRoles;

/**
 * @Flow\Scope("singleton")
 */
class HashAndRolesRepository extends Repository
{
    /**
     * @param array $roles
     * @return HashAndRoles|null
     */
    public function findOneByRoles(array $roles)
    {
        return $this->findOneByRolesHash(HashAndRoles::calculateHashForRoles($roles));
    }

    /**
     * @param array $roles
     * @return QueryResultInterface
     */
    public function findByRoles(array $roles)
    {
        return $this->findByRolesHash(HashAndRoles::calculateHashForRoles($roles));
    }
}
