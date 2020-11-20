<?php

namespace App\Controller\Web\Auth\Social;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthFacebookController extends AbstractController
{
    /**
     * @Route("/facebook", name="auth_facebook")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook')
            ->redirect(['public_profile', 'email'], []);
    }

    /**
     * @Route("/facebook/callback", name="auth_facebook_callback")
     */
    public function connectCallbackAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('app_gas_stations');
    }
}
