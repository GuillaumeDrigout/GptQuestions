<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Category;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/produits', name: 'products')]
    public function index(Request $request): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        // Handle search form submission
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $orderBy = [];
            if ($search->getSort() === 'asc') {
                $orderBy = ['prix' => 'ASC'];
            } elseif ($search->getSort() === 'desc') {
                $orderBy = ['prix' => 'DESC'];
            }
        
            // Get all products sorted by price
            $products = $this->entityManager->getRepository(Product::class)
                ->findBy([], $orderBy);
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        
        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }
}
