<?php

namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{

    #[Route('/explore/{category}', name: 'app_product_customer_view', methods: ['GET'])]
    public function explore(ProductRepository $productRepository, CategoryRepository $categoryRepository, int $category = null): Response
    {

        // If no category id was provided, fetch all products
        $products = $productRepository->findAll();

        if ($category) {
            // If a category id was provided, fetch products from that category
            $products = $productRepository->findByCategory($category);
        }

        return $this->render('product/customer_view.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
            'selectedCategory' => $category
        ]);
    }


    
    #[Route('/cart', name: 'cart')]
    public function displayCart(CartService $cartService)
    {
        $cart=$cartService->getFullCart();

        return $this->render('product/cart.html.twig', [
            'cart'=>$cart
        ]);
    }

    

    // Cart related actions
    #[Route('/addToCart/{id}/{redirectRoute}', name: 'add_to_cart', defaults: ['redirectRoute' => 'cart'])]
    public function addCart($id, $redirectRoute, CartService $cartService)
    {
        $cartService->add($id);
        return $this->redirectToRoute($redirectRoute);
    }
    

    #[Route('/removeFromCart/{id}', name: 'remove_from_cart')]
    public function removeCart($id, CartService $cartrService)
    {
        $cartrService->remove($id);
        return $this->redirectToRoute('cart');

    }

    #[Route('/deleteFromCart/{id}', name: 'delete_from_cart')]
    public function deleteCart($id, CartService $cartService)
    {
        $cartService->delete($id);
        return $this->redirectToRoute('cart');

    }
}
