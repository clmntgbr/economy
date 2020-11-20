<?php

namespace App\Controller\Web\User;

use App\Util\DotEnv;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web/user")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DotEnv */
    private $dotEnv;

    public function __construct(EntityManagerInterface $entityManager, DotEnv $dotEnv)
    {
        $this->entityManager = $entityManager;
        $this->dotEnv = $dotEnv;
    }

    /**
     * @Route("/account", name="user_account")
     */
    public function getUserAccountAction(Request $request)
    {
        return $this->render('user/user_account.html.twig', []);
    }
}
