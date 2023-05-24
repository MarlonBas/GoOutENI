<?php

namespace App\Controller;

use Detection\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{


    /**
     * @Route("/", name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        //  $string = $this->getRequest()->getCookie('REMEMBERME');



        $mobileDetect = new MobileDetect();
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        if ($mobileDetect->isMobile()) {
            // get the login error if there is one
            // last username entered by the user
            return $this->render('mobile/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

        } else {

            // get the login error if there is one
            // last username entered by the user

            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

        }
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): Response
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }
}
