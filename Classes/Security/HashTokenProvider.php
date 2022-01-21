<?php
namespace Flownative\TokenAuthentication\Security;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\Authentication\Provider\AbstractProvider;
use Neos\Flow\Security\Authentication\TokenInterface;
use Neos\Flow\Security\Exception\UnsupportedAuthenticationTokenException;
use Neos\Flow\Security\Policy\PolicyService;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;
use Flownative\TokenAuthentication\Security\Model\HashAndRoles;

/**
 * A Flow security provider that can authenticate HashToken.
 */
class HashTokenProvider extends AbstractProvider
{
    /**
     * @Flow\Inject
     * @var HashAndRolesRepository
     */
    protected $hashAndRolesRepository;

    /**
     * @Flow\Inject
     * @var PolicyService
     */
    protected $policyService;

    /**
     * @return array
     */
    public function getTokenClassNames(): array
    {
        return [HashToken::class, SessionStartingHashToken::class];
    }

    /**
     * @param TokenInterface $authenticationToken
     */
    public function authenticate(TokenInterface $authenticationToken)
    {
        if (!($authenticationToken instanceof SessionStartingHashToken) && !($authenticationToken instanceof HashToken)) {
            throw new UnsupportedAuthenticationTokenException('This provider cannot authenticate the given token.', 1547118072);
        }

        $hashAndRoles = $this->getHashAndRoles($authenticationToken);
        if ($hashAndRoles === null) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
            return;
        }

        $authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
        $account = new Account();
        $account->setAccountIdentifier($hashAndRoles->getHash());
        $roles = [];
        foreach ($hashAndRoles->getRoles() as $roleIdentifier) {
            $roles[] = $this->policyService->getRole($roleIdentifier);
        }
        $account->setRoles($roles);
        $authenticationToken->setAccount($account);
    }

    /**
     * @param TokenInterface $authenticationToken
     * @return HashAndRoles|null
     */
    protected function getHashAndRoles(TokenInterface $authenticationToken)
    {
        $credentials = $authenticationToken->getCredentials();
        if (!is_array($credentials) || !isset($credentials['password'])) {
            return null;
        }
        return $this->hashAndRolesRepository->findByIdentifier($credentials['password']);
    }
}
