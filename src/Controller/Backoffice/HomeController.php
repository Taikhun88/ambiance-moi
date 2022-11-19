<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/backoffice/home', name: 'app_backoffice_home')]
    public function index(): Response
    {
        return $this->render('backoffice/home/index.html.twig');
    }
}
