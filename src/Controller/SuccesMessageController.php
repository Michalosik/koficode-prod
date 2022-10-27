<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuccesMessageController extends AbstractController
{
    #[Route('/success', name: 'success')]
    public function index(): Response
    {
        return $this->render('messages/success.html.twig', [
            'controller_name' => 'SuccesMessageController',
        ]);
    }
}
