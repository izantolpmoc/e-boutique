<?php

namespace App\Controller;

use App\Entity\CommandLine;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Cart\CartService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/new', name: 'new_order')]
    public function new(OrderRepository $orderRepository, CartService $cartService, Security $security): Response
    {
        $order = new Order();
        $user = $security->getUser();
        $cart=$cartService->getFullCart();
        $order->setUser($user);
        $order->setDate(new DateTime());

        foreach ($cart as $item) {
            $commandLine = new CommandLine();
            $commandLine->setQuantity($item['quantity']);
            $commandLine->setProduct($item['product']);
            $commandLine->setRelatedOrder($order);
            $order->addCommandLine($commandLine);
        }
        $uuid = Uuid::v4();
        $order->setNumber($uuid);
        $order->setValid(false);
        $orderRepository->save($order, true);

        $cartService->reset();

        return $this->redirectToRoute('app_order_show', [ 'id' => $order->getId() ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/{id}/{route}', name: 'app_order_delete', methods: ['POST'], defaults:['route' => 'app_order_index'])]
    public function delete($route, Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute($route, [], Response::HTTP_SEE_OTHER);
    }

}
