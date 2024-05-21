<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;





class DefaultController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    #[Route('/', name: 'home')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
{
    $phoneNumber = $request->request->get('phoneNumber');
    $password = $request->request->get('password');

    // Retrieve the user from the database based on the provided phone number
    $user = $userRepository->findOneBy(['phoneNumber' => $phoneNumber]);

    // If the user does not exist, redirect back to the login page with an error message
    if (!$user) {
        $this->addFlash('error', 'Invalid phone number or password.');
        return $this->redirectToRoute('index');
    }

    // Verify the password
    if (!$passwordHasher->isPasswordValid($user, $password)) {
        $this->addFlash('error', 'Invalid phone number or password.');
        return $this->redirectToRoute('index');
    }

    // If the user authentication is successful, redirect to the dashboard
    return $this->redirectToRoute('dashboard');
}

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard.html.twig');
    }

}
