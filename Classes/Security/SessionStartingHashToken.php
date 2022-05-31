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
     * @return bool
     * @throws InvalidAuthenticationStatusException
     */
    public function updateCredentials(ActionRequest $actionRequest)
    {
        $authenticationHashToken = $actionRequest->getHttpRequest()->getQueryParams()['_authenticationHashToken'] ?? null;

        if (!$authenticationHashToken) {
            $authorizationHeaders = $actionRequest->getHttpRequest()->getHeader('Authorization');
            if (!empty($authorizationHeaders)) {
                foreach ($authorizationHeaders as $authorizationHeader) {
                    if (strpos($authorizationHeader, 'Bearer ') === 0) {
                        $authenticationHashToken = str_replace('Bearer ', '', $authorizationHeader);
                        break;
                    }
                }
            }
        }

        if ($authenticationHashToken) {
            $this->credentials['password'] = $authenticationHashToken;
            $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
        }

        return false;
    }
}
