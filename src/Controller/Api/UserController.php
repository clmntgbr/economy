<?php

namespace App\Controller\Api;

use App\Entity\User\User;
use App\Form\Auth\RegisterType;
use App\Repository\User\UserRepository;
use App\Util\ResponseBody;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ResponseBody */
    private $responseBody;

    public function __construct(EntityManagerInterface $entityManager, ResponseBody $responseBody)
    {
        $this->entityManager = $entityManager;
        $this->responseBody = $responseBody;
    }

    /**
     * @Rest\Get(path="/user", name="user")
     * @Rest\View
     */
    public function getUserAction(Request $request, Security $security)
    {
        return $this->responseBody->create(Response::HTTP_OK, $security->getUser(), []);
    }
}
