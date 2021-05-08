<?php

namespace App\Controller;

use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShowIngredientController extends AbstractController
{
    /**
     * @Route("/show/ingredient", name="show_ingredient")
     */
    public function show(): Response
    {
        $repo = $this->getDoctrine()
            ->getRepository(Ingredient::class);

        $ingredients = $repo->findAll();
        if (!$ingredients) {
            return $this->render('home/index.html.twig', [
                'content' => 'empty list of ingredient'
            ]);
        }
        return $this->render('/show_ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }
}
