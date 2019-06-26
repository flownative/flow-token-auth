<?php
namespace Flownative\TokenAuthentication\Security;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Context;
use Flownative\TokenAuthentication\Security\Model\HashAndRoles;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;

/**
 * @Flow\Scope("singleton")
 */
class TokenInformer
{
    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @Flow\InjectConfiguration(package="Flownative.TokenAuthentication", path="authenticationProviderName")
     * @var string
     */
    protected $authenticationProviderName;

    /**
     * @Flow\Inject
     * @var HashAndRolesRepository
     */
    protected $hashAndRolesRepository;

    /**
     * @return HashAndRoles|null
     */
    public function getToken()
    {
        if ($this->securityContext->canBeInitialized() !== true) {
            return null;
        }

        $account = $this->securityContext->getAccountByAuthenticationProviderName($this->authenticationProviderName);

        if ($account === null) {
            return null;
        }

        $hashAndRoles = $this->hashAndRolesRepository->findByIdentifier($account->getAccountIdentifier());
        return $hashAndRoles;
    }
}
