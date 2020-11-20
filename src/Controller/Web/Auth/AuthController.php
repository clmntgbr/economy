<?php

namespace App\Controller\Web\Auth;

use App\Entity\User\User;
use App\Form\Auth\RegisterType;
use App\Security\Guard\AuthLoginAuthenticator;
use App\Util\ResponseBody;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface */
    private $validator;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var AuthLoginAuthenticator */
    private $authenticator;

    /** @var GuardAuthenticatorHandler */
    private $guardHandler;

    public function __construct(GuardAuthenticatorHandler $guardHandler, AuthLoginAuthenticator $authenticator, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->authenticator = $authenticator;
        $this->guardHandler = $guardHandler;
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
         if ($this->getUser() instanceof User) {
             return $this->redirectToRoute('app_gas_stations');
         }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'type' => $request->query->get('type') ?? null
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request): Response
    {
        $credentials = $request->request->get("credentials");

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        try {
            $form->submit($credentials);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('auth_login', ['type' => urlencode(uniqid())]);
        }

        $errors = $this->validator->validate($user, null, ['User:Register']);

        if (count($errors) > 0) {
            $this->addFlash('error', ResponseBody::getValidatorErrorsToHTML($errors));
            return $this->redirectToRoute('auth_login', ['type' => urlencode(uniqid())]);
        }

        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->authenticator,
            'main'
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
