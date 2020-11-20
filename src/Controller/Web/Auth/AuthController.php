<?php

namespace App\Controller\Web\Auth;

use App\Entity\User\User;
use App\Form\Auth\RegisterType;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Security\Guard\AuthLoginAuthenticator;
use App\Util\ResponseBody;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends AbstractController
{
    use ResetPasswordControllerTrait;

    /** @var ResetPasswordHelperInterface */
    private $resetPasswordHelper;

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

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, GuardAuthenticatorHandler $guardHandler, AuthLoginAuthenticator $authenticator, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->authenticator = $authenticator;
        $this->guardHandler = $guardHandler;
        $this->resetPasswordHelper = $resetPasswordHelper;
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

    /**
     * @Route("/reset/email", name="reset_email")
     */
    public function resetEmail(Request $request): Response
    {
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('auth_reset_password');
        }

        //TODO remove this
        $token = $this->getTokenFromSession();

        return $this->render('auth/reset_email.html.twig', [
            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            'resetToken' => $token,
        ]);
    }

    /**
     * @Route("/reset/password", name="reset_password")
     */
    public function resetPasswordAction(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }

        return $this->render('auth/reset_password.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/reset/token/{token}", name="reset_token")
     */
    public function resetPasswordTokenAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('auth_reset_token');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('error', sprintf('There was a problem validating your reset request - %s', $e->getReason()));
            return $this->redirectToRoute('auth_reset_password');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);

            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Votre mot de passe à bien été changé.');
            return $this->redirectToRoute('auth_login');
        }

        return $this->render('auth/reset_token.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        $this->setCanCheckEmailInSession();

        if (!$user) {
            return $this->redirectToRoute('auth_reset_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            //TODO remove that session thing
            $this->storeTokenInSession($resetToken->getToken());
        } catch (ResetPasswordExceptionInterface $e) {
             $this->addFlash('error', sprintf(
                 'There was a problem handling your password reset request - %s',
                 $e->getReason()
             ));

            return $this->redirectToRoute('auth_reset_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@economy.com', 'ResetPassword'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('auth/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        ;

        $mailer->send($email);

        return $this->redirectToRoute('auth_reset_email');
    }
}
