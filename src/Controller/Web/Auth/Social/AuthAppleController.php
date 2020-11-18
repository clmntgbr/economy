<?php

namespace App\Controller\Web\Auth\Social;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthAppleController extends AbstractController
{
    /**
     * @Route("/apple", name="auth_apple")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('apple')
            ->redirect(['name', 'email'], []);
    }

    /**
     * @Route("/apple/callback", name="auth_apple_callback")
     */
    public function connectCallbackAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('gas_stations');
    }
}
