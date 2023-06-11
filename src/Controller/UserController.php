<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/account', name: 'app_user_account', methods: ['GET'])]
    public function account(OrderRepository $orderRepository): Response
    {
        return $this->render('user/account.html.twig');
    }


    #[Route('/{id}/edit/{route}', name: 'app_user_edit', methods: ['GET', 'POST'], defaults:['route'=>'app_user_index'])]
    public function edit($route, Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        $loggedUser = $this->getUser();
        if ($loggedUser instanceof User) {
            // check if the logged user has the same id as the $user being edited or has the ROLE_ADMIN
            if ($loggedUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException();
            }
        }
        else throw $this->createAccessDeniedException();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $userRepository->find($user->getId());
            $user->setPassword($currentUser->getPassword());
            $userRepository->save($user, true);

            return $this->redirectToRoute($route, [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{route}', name: 'app_user_delete', methods: ['POST'], defaults:['route'=> 'app_user_index'])]
    public function delete(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $entityManager, CartService $cartService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Get all related addresses and delete them
            $address = $user->getAddress();
            $entityManager->remove($address);


            // Get all related orders and set them as invalid
            $orders = $user->getOrders();
            foreach ($orders as $order) {
                $entityManager->remove($order);
            }
            $userRepository->remove($user, true);

            // If we deleted the currently authenticated user, invalidate their session
            $request->getSession()->invalidate();

            // reset current cart
            $cartService->reset();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
