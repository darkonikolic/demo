<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use App\Security\ApiTokenHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityController extends AbstractController
{
    public function __construct(private readonly ApiTokenHandler $apiTokenHandler)
    {
    }

    #[Route('/auth', name: 'app_login', methods: ['POST'])]
    public function login(IriConverterInterface $iriConverter, #[CurrentUser] User $user = null): Response
    {
        if (!$user) {
            return $this->json(
                ['Error: Login not successful.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = $this->apiTokenHandler->determineUserToken($user);

        if ($token === null) {
            return $this->json(
                ['Error: Login not successful. Token is not generated.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->json(
            ['token' => $token],
            Response::HTTP_OK,
            ['Location' => $iriConverter->getIriFromResource($user)]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new Exception('This should never be reached');
    }
}
