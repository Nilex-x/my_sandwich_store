<?php

namespace App\Controller;

use App\Entity\Sandwich;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowSandwichController extends AbstractController
{
    /**
     * @Route("/", name="show_sandwich")
     */
    public function index(): Response
    {
        $sandwich = $this->getDoctrine()->getRepository(Sandwich::class)->findAll();
        if (!$sandwich) {
            return $this->render('/show_sandwich/index.html.twig', [
                'content' => 'empty list of sandwich',
                'Sandwichs' => null
            ]);
        }
        return $this->render('/show_sandwich/index.html.twig', [
            'content' => 'your list of sandwich :',
            'Sandwichs' => $sandwich
        ]);
    }
}
