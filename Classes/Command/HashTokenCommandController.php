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
     * @param ?string $label An optional label for the token
     * @param ?string $expiresAt An optional expiry date / time for the token (anything DateTime($expiresAt) can parse)
     * @param ?string $token An optional value to use as token, if not specified a random string is used. !!! MUST be 64 characters long !!!
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function createHashTokenCommand(
        array $roleNames,
        ?string $label = null,
        ?string $expiresAt = null,
        ?string $token = null
    ): void
    {
        if (!empty($expiresAt)) {
            $expiresAt = new \DateTime($expiresAt);

            if ($expiresAt < new \DateTime()) {
                $this->outputLine('The expiration date must be in the future.');
                return;
            }
        }

        if ($token === null) {
            $token = Algorithms::generateRandomString(64);
        } else {
            if (strlen($token) !== 64) {
                throw new \InvalidArgumentException('The token must be 64 characters long');
            }
        }

        $hashAndRoles = HashAndRoles::create($token, $roleNames, [], $label, $expiresAt);
        $this->hashAndRolesRepository->add($hashAndRoles);
        $this->persistenceManager->persistAll();
        $this->outputLine('A token with the given roles was generated, the hash is "%s".', [$hashAndRoles->getHash()]);
    }

    /**
     * Remove all hash tokens
     *
     * @param bool $force Do not ask for confirmation
     */
    public function removeAllCommand(bool $force = false): void
    {
        if ($force === false) {
            $confirmed = $this->output->askConfirmation('are you sure', false, );
            if (!$confirmed) {
                return;
            }
        }

        $this->hashAndRolesRepository->removeAll();
        $this->persistenceManager->persistAll();
        $this->outputLine('all tokens have been removed.');
    }
}
