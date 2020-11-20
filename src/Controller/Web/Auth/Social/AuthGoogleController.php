<?php

namespace App\Controller\Web\Auth\Social;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthGoogleController extends AbstractController
{
    /**
     * @Route("/google", name="auth_google")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['profile', 'email'], []);
    }

    /**
     * @Route("/google/callback", name="auth_google_callback")
     */
    public function connectCallbackAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('app_gas_stations');
    }
}
