<?php

namespace App\Security;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private readonly ApiTokenRepository $apiTokenRepository)
    {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->apiTokenRepository->findOneBy(['token' => $accessToken]);

        if (!$token) {
            throw new BadCredentialsException();
        }

        if (!$token->isValid()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        $token->getOwnedBy()->setRoles($token->getScopes());

        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }

    public function determineUserToken(User $user): ?string
    {
        return $user->getValidTokenStrings()[0] ?? $this->generateNewUserToken($user);
    }

    private function generateNewUserToken(User $user) : ?string
    {
        $tokenObj = new ApiToken();
        $tokenObj->setOwnedBy($user);
        $tokenObj->setScopes($user->getRoles());
        $this->apiTokenRepository->save($tokenObj, true);
        return $tokenObj->getToken();
    }
}
