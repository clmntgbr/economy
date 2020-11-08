<?php

namespace App\Controller\Web\Auth;

use App\Entity\User\User;
use App\Form\Auth\AuthenticationType;
use App\Repository\User\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Util\DotEnv;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    /** @var SessionInterface */
    private $session;

    /** @var AuthenticationManagerInterface */
    private $authenticationManager;

    /** @var DotEnv */
    private $dotEnv;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var ValidatorInterface */
    private $validator;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(SessionInterface $session, AuthenticationManagerInterface $authenticationManager, TokenStorageInterface $tokenStorage, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserRepository $userRepository, DotEnv $dotEnv)
    {
        $this->validator = $validator;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->authenticationManager = $authenticationManager;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->dotEnv = $dotEnv;
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, GuardAuthenticatorHandler $guardAuthenticatorHandler, LoginFormAuthenticator $formAuthenticator)
    {
        $user = new User();

        $form = $this->createForm(AuthenticationType::class, $user, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registeredUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($registeredUser instanceof User) {
                if ($this->passwordEncoder->isPasswordValid($registeredUser, $user->getPassword())) {
                    return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                        $user,
                        $request,
                        $formAuthenticator,
                        'main'
                    );
                }
            }
        }

        return $this->render('auth/login.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
