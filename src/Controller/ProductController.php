<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

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


    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        
        $media1 = new Media();
        $media1->setType("image");
        $media1->setProduct($product);
        $product->getMedia()->add($media1);
        // $media2 = new Media();
        // $media2->setType("image");
        // $media2->setProduct($product);
        // $product->getMedia()->add($media2);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
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

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $medias = $product->getMedia();
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'medias'=> $medias
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
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
