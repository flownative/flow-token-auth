<?php
namespace Flownative\TokenAuthentication\Command;

use Flownative\TokenAuthentication\Security\Model\HashAndRoles;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Utility\Algorithms;

/**
 *
 */
class HashTokenCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var HashAndRolesRepository
     */
    protected $hashAndRolesRepository;

    /**
     * Create a hash token to login with, authenticating the given roles.
     * 
     * @param array $roleNames
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function createHashTokenCommand(array $roleNames)
    {
        $token = Algorithms::generateRandomString(64);
        $hashAndRoles = HashAndRoles::createWithHashAndRoles($token, $roleNames);
        $this->hashAndRolesRepository->add($hashAndRoles);
        $this->persistenceManager->persistAll();
        $this->outputLine('A token with the given roles was generated, the hash is "%s".', [$hashAndRoles->getHash()]);
    }
}
