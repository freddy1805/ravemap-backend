<?php

namespace App\Websocket\Provider;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerAwareTrait;
use Ratchet\ConnectionInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Gos\Bundle\WebSocketBundle\Authentication\Provider\AuthenticationProviderInterface;
use Gos\Bundle\WebSocketBundle\Authentication\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class AuthProvider implements AuthenticationProviderInterface
{
    use LoggerAwareTrait;

    private TokenStorageInterface $tokenStorage;

    /**
     * @var JWSProviderInterface
     */
    private JWSProviderInterface $JWSProvider;

    /**
     * @var UserProviderInterface|JWTUserProvider
     */
    private UserProviderInterface $jwtUserProvider;

    /**
     * AuthProvider constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param UserProviderInterface $JWTUserProvider
     * @param JWSProviderInterface $JWSProvider
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserProviderInterface $JWTUserProvider, JWSProviderInterface $JWSProvider)
    {
        $this->tokenStorage = $tokenStorage;
        $this->JWSProvider = $JWSProvider;
        $this->jwtUserProvider = $JWTUserProvider;
    }

    public function authenticate(ConnectionInterface $connection): TokenInterface
    {
        $token = $this->getToken($connection);

        $identifier = $this->tokenStorage->generateStorageId($connection);

        $this->tokenStorage->addToken($identifier, $token);

        return $token;
    }

    private function getToken(ConnectionInterface $connection): TokenInterface
    {
        $token = null;

        /** @var RequestInterface $req */
        $req = $connection->httpRequest;

        $jwtToken = preg_replace('/token=/', '', $req->getUri()->getQuery());

        try {
            $jws = $this->JWSProvider->load($jwtToken);
            $user = $this->jwtUserProvider->loadUserByUsername($jws->getPayload()['username'], $jws->getPayload());
            $token = new JWTUserToken([], $user, $jwtToken);
        } catch (JWTDecodeFailureException $decodeFailureException) {
            $token = new AnonymousToken(' main', 'anon-'.$connection->WAMP->sessionId);
        }

        return $token;
    }

    public function supports(ConnectionInterface $connection): bool
    {
        return $connection instanceof ConnectionInterface;
    }
}
