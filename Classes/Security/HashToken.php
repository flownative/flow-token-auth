<?php
namespace Flownative\TokenAuthentication\Security;

use Neos\Flow\Security\Authentication\Token\SessionlessTokenInterface;

/**
 * A Flow security token that authenticates based on a hash delivered
 * without starting a session.
 */
class HashToken extends SessionStartingHashToken implements SessionlessTokenInterface
{
}
