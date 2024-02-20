<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    /**
     * Redirige l'utilisateur vers le fournisseur OAuth pour l'authentification.
     * @Route("/oauth/login", name="oauth_login")
     */
    public function index(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('keycloak')->redirect();
    }
    
    /**
     * Gère la réponse de l'authentification OAuth après la redirection.
     * @Route("/oauth/callback", name="oauth_check")
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     */
    public function connecCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        
    }
    
    /**
     * Gère la déconnexion de l'utilisateur de l'application.
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        
    }
}
