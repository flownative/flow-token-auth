<?php
namespace Flownative\TokenAuthentication\Tests\Unit\Security;

use Flownative\TokenAuthentication\Security\SessionStartingHashToken;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\TokenInterface;
use Neos\Flow\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;

final class SessionStartingHashTokenTest extends UnitTestCase
{

    /**
     * @var SessionStartingHashToken
     */
    private $token;

    /**
     * @var ActionRequest|MockObject
     */
    private $mockActionRequest;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $mockHttpRequest;

    protected function setUp(): void
    {
        $this->token = new SessionStartingHashToken();
        $this->mockActionRequest = $this->getMockBuilder(ActionRequest::class)->disableOriginalConstructor()->getMock();

        $this->mockHttpRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->mockActionRequest->method('getHttpRequest')->willReturn($this->mockHttpRequest);
    }

    /**
     * @test
     */
    public function updateCredentialsDoesNotSetCredentialsIfNoneArePresent(): void
    {
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertNull($this->token->getCredentials());
    }

    /**
     * @test
     */
    public function updateCredentialsDoesNotAlterAuthenticationStatusIfNoCredentialsArePresent(): void
    {
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertSame(TokenInterface::NO_CREDENTIALS_GIVEN, $this->token->getAuthenticationStatus());
    }

    /**
     * @test
     */
    public function updateCredentialsUpdatesCredentialsFromQueryParams(): void
    {
        $this->mockHttpRequest->method('getQueryParams')->willReturn(['_authenticationHashToken' => 'SomeToken']);
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertSame(['password' => 'SomeToken'], $this->token->getCredentials());
    }

    /**
     * @test
     */
    public function updateCredentialsUpdatesAuthenticationStatusIfTokenQueryParamIsSpecified(): void
    {
        $this->mockHttpRequest->method('getQueryParams')->willReturn(['_authenticationHashToken' => 'SomeToken']);
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertSame(TokenInterface::AUTHENTICATION_NEEDED, $this->token->getAuthenticationStatus());
    }

    /**
     * @test
     */
    public function updateCredentialsUpdatesCredentialsFromAuthorizationHeader(): void
    {
        $this->mockHttpRequest->method('getHeaderLine')->with('Authorization')->willReturn('Bearer SomeBearerToken');
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertSame(['password' => 'SomeBearerToken'], $this->token->getCredentials());
    }

    /**
     * @test
     */
    public function updateCredentialsUpdatesAuthenticationStatusIfAuthorizationHeaderIsSpecified(): void
    {
        $this->mockHttpRequest->method('getHeaderLine')->with('Authorization')->willReturn('Bearer SomeBearerToken');
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertSame(TokenInterface::AUTHENTICATION_NEEDED, $this->token->getAuthenticationStatus());
    }

    /**
     * @test
     */
    public function updateCredentialsIgnoresAuthorizationHeaderIfQueryParamIsSpecified(): void
    {
        $this->mockHttpRequest->method('getQueryParams')->willReturn(['_authenticationHashToken' => 'SomeTokenFromQueryParam']);
        $this->mockHttpRequest->method('getHeaderLine')->with('Authorization')->willReturn('Bearer SomeTokenFromAuthHeader');
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertSame(['password' => 'SomeTokenFromQueryParam'], $this->token->getCredentials());
    }

    /**
     * @test
     */
    public function updateCredentialsIgnoresAuthorizationHeadersWithoutBearerPrefix(): void
    {
        $this->mockHttpRequest->method('getHeaderLine')->with('Authorization')->willReturn('SomeInvalidToken');
        $this->token->updateCredentials($this->mockActionRequest);
        self::assertNull($this->token->getCredentials());
        self::assertSame(TokenInterface::NO_CREDENTIALS_GIVEN, $this->token->getAuthenticationStatus());
    }
}
