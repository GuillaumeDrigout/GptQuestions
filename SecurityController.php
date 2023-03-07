<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $response = $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'categories' => $categories
        ]);
        $response->headers->setCookie(new Cookie('isLoggedIn', true));
        return $response;
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(Cart $cart, CartController $cartController): void
    {
        // Save the cart data to local storage
        $cart->save();

        // Merge the cart data with the one stored in the session
        $cartData = json_encode($cart->get());
        $cartData = unserialize($cartData);
        $cartController->mergeCart($cartData);

        // Clear the isLoggedIn cookie
        $response = new Response();
        $response->headers->clearCookie('isLoggedIn');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
