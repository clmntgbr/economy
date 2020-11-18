<?php

namespace App\Controller\Web\Auth\Social;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthLinkedinController extends AbstractController
{
    /**
     * @Route("/linkedin", name="auth_linkedin")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('linkedin')
            ->redirect([], []);
    }

    /**
     * @Route("/linkedin/callback", name="auth_linkedin_callback")
     */
    public function connectCallbackAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('gas_stations');
    }
}
