<?php

namespace App\Controller\Api\Auth;

use App\Entity\Auth\Token;
use App\Entity\User\User;
use App\Form\Auth\RegisterType;
use App\Repository\Auth\TokenRepository;
use App\Repository\User\UserRepository;
use App\Util\ResponseBody;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ResponseBody */
    private $responseBody;

    /** @var ValidatorInterface */
    private $validator;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserRepository */
    private $userRepository;

    /** @var TokenRepository */
    private $tokenRepository;

    /** @var JWTTokenManagerInterface */
    private $authenticator;

    /** @var EncoderFactoryInterface */
    private $encoderFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ResponseBody $responseBody,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        TokenRepository $tokenRepository,
        JWTTokenManagerInterface $authenticator,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->entityManager = $entityManager;
        $this->responseBody = $responseBody;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->authenticator = $authenticator;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @Rest\Post(path="/register", name="register")
     * @Rest\View
     */
    public function registerAction(Request $request)
    {
        $user = new User();

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(RegisterType::class, $user);

        try {
            $form->submit($data);
        } catch (\Exception $exception) {
            return $this->responseBody->create(Response::HTTP_BAD_REQUEST, [], ResponseBody::getErrorsFormatted('notknown', $exception->getMessage()));
        }

        $errors = $this->validator->validate($user, null, ['User:Register']);

        if (count($errors) > 0) {
            return $this->responseBody->create(Response::HTTP_BAD_REQUEST, [], ResponseBody::getValidatorErrors($errors));
        }

        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->responseBody->create(Response::HTTP_CREATED, $user, []);
    }

    /**
     * @Rest\Post(path="/authentication", name="authentication")
     * @Rest\View
     */
    public function authenticationAction(Request $request)
    {
        $user = new User();

        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(RegisterType::class, $user);

        try {
            $form->submit($data);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 404);
        }

        $errors = $this->validator->validate($user, null, ['User:Authentication']);

        if (count($errors) > 0) {
            return $this->responseBody->create(Response::HTTP_BAD_REQUEST, [], ResponseBody::getValidatorErrors($errors));
        }

        $registeredUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        if (!($registeredUser instanceof User)) {
            return $this->responseBody->create(Response::HTTP_BAD_REQUEST, [], ResponseBody::getErrorsFormatted('email', sprintf(ResponseBody::USER_NOT_FOUND, $user->getEmail())));
        }

        if ($this->passwordEncoder->isPasswordValid($registeredUser, $user->getPassword())) {
            $token = $this->authenticator->create($registeredUser);
            $this->getAuthToken($token, $registeredUser);
            return $this->responseBody->create(Response::HTTP_ACCEPTED, ['token' => sprintf('Bearer %s', $token)], []);
        }

        return $this->responseBody->create(Response::HTTP_BAD_REQUEST, [], ResponseBody::getErrorsFormatted('password', ResponseBody::WRONG_PASSWORD));
    }

    private function getAuthToken(string $token, User $user)
    {
        $preAuthToken = new PreAuthenticationJWTUserToken($token);
        $options = $this->authenticator->decode($preAuthToken);

        $authToken = $this->tokenRepository->findOneBy(['userId' => $user->getId()]);

        $date = new DateTime();
        if (!($authToken instanceof Token)) {
            $authToken = new Token();
        }

        $authToken->update($user->getId(), password_hash($token, PASSWORD_DEFAULT), $date->setTimestamp($options['exp']));

        $this->entityManager->persist($authToken);
        $this->entityManager->flush();
    }
}
