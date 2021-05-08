<?php

namespace App\Controller;

use App\Entity\Sandwich;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowSandwichController extends AbstractController
{
    /**
     * @Route("/show/sandwich", name="show_sandwich")
     */
    public function index(): Response
    {
        $sandwich = $this->getDoctrine()->getRepository(Sandwich::class)->findAll();
        if (!$sandwich) {
            return $this->render('home/index.html.twig', [
                'content' => 'empty list of sandwich'
            ]);
        }
        return $this->render('/show_sandwich/index.html.twig', [
            'Sandwichs' => $sandwich
        ]);
    }
}
