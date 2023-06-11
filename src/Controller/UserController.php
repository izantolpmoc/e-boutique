<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
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
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/account', name: 'app_user_account', methods: ['GET'])]
    public function account(Security $security, UserRepository $userRepository): Response
    {
        return $this->render('user/account.html.twig');
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit/{route}', name: 'app_user_edit', methods: ['GET', 'POST'], defaults:['route'=>'app_user_index'])]
    public function edit($route, Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

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
