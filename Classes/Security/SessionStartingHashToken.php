<?php
namespace Flownative\TokenAuthentication\Security;

use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\Token\AbstractToken;
use Neos\Flow\Security\Exception\InvalidAuthenticationStatusException;

/**
 * A Flow security token that authenticates based on a hash delivered and
 * starts a session.
 */
class SessionStartingHashToken extends AbstractToken
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @param ActionRequest $actionRequest
     * @return void
     * @throws InvalidAuthenticationStatusException
     */
    public function updateCredentials(ActionRequest $actionRequest)
    {
        $authenticationHashToken = $actionRequest->getHttpRequest()->getQueryParams()['_authenticationHashToken'] ?? null;

        if ($authenticationHashToken === null) {
            $authorizationHeader = $actionRequest->getHttpRequest()->getHeaderLine('Authorization');
            if (strncmp($authorizationHeader, 'Bearer ', 7) === 0) {
                $authenticationHashToken = substr($authorizationHeader, 7);
            }
        }

        if ($authenticationHashToken !== null) {
            $this->credentials['password'] = $authenticationHashToken;
            $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
        }
    }
}
