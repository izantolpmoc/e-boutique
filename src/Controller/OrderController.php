<?php

namespace App\Controller;

use App\Entity\CommandLine;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Cart\CartService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    #[Route('/order/new', name: 'new_order')]
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
        $order->setValid(true);
        $orderRepository->save($order, true);

        $cartService->reset();

        return $this->redirectToRoute('app_home');
    }
}
