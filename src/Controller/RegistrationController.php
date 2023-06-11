<?php

namespace App\Controller;

use App\Entity\CustomerAddress;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CustomerAddressRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $address = new CustomerAddress();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $address = $user->getAddress();
            $address->setName($user->getName());
            $address->setFirstname($user->getFirstname());
            $address->setPhone($user->getPhone());
            $address->setType("default");
            
            // $address->setFirstname($user->getFirstname());
            // $address->setName($user->getName());
            // $address->setPhone($user->getPhone());
            // $address->setAddress($user->getAddress()->getAddress());
            // $address->setCity($user->getAddress()->getCity());
            // $address->setCountry($user->getAddress()->getCountry());
            // $address->setPostalcode($user->getAddress()->getPostalcode());

            // $addressRepository->save($address, true);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
