<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    private $entityManager;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart)
    {
        $cartComplete = [];

        if ($cart->get() !== null && count($cart->get()) > 0) {
            foreach ($cart->get() as $id => $quantity) {
                $cartComplete[] = [
                    'product' => $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]),
                    'quantity' => $quantity
                ];
            }
        }
        
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete,
            'categories' => $categories,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, $id)
    {
        $cart = $this->session->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'remove_my_cart')]
    public function remove(Cart $cart)
    {
        $cart->remove();

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/delete/{id}', name: 'delete_from_cart')]
    public function delete(Cart $cart, $id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        $this->session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/merge', name: 'merge_cart')]
    public function mergeCart(Cart $cart)
    {
        // Merge the session cart with the local storage cart
        $sessionCart = $this->session->get('cart', []);
        $localStorageCart = json_decode($this->session->get('localStorageCart', '{}'), true);
        $mergedCart = array_merge_recursive($sessionCart, $localStorageCart);

        // Update the user cart in the session
        $this->session->set('cart', $mergedCart);

        // Remove the local storage cart
        $this->session->remove('localStorageCart');

        return $this->redirectToRoute('cart');
    }
}
