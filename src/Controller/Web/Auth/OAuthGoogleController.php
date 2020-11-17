<?php

namespace App\Controller\Web\Auth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class OAuthGoogleController extends AbstractController
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
     * @Route("/google/check", name="auth_google_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('gas_stations');
    }
}
