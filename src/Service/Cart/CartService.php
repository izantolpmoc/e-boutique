<?php 

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public $requestStack;

    public $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack=$requestStack;
        $this->productRepository=$productRepository;
    }

    public function add(int $id)
    {
        $cart=$this->requestStack->getSession()->get('cart', []);

        if(empty($cart[$id])):
            $cart[$id] = 1;
        else:
            $cart[$id] ++;
        endif;

		$this->requestStack->getSession()->set('cart', $cart); //mise à jour de la requestStack
    }

    public function remove(int $id)
    {
        $cart=$this->requestStack->getSession()->get('cart', []);

        if(!empty($cart[$id]) && $cart[$id]!==1):
            $cart[$id] --;
        else:
            unset($cart[$id]);
        endif;

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function delete(int $id)
    {
        $cart=$this->requestStack->getSession()->get('cart', []);

        if(!empty($cart[$id])):
            unset($cart[$id]);
        endif;

        $this->requestStack->getSession()->set('cart', $cart);

    }

    public function getFullCart(): array
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        $cartDetail = [];

        foreach ($cart as $id => $quantity):

            $cartDetail[]=[
                'product'=>$this->productRepository->find($id),
                'quantity'=>$quantity
            ];

        endforeach;

        return $cartDetail;
    }
}

