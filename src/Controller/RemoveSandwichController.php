<?php

namespace App\Controller;

use App\Entity\Sandwich;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RemoveSandwichController extends AbstractController
{
    /**
     * @Route("/remove/{name}", name="remove_sandwich")
     */
    public function index(string $name, Request $request, ValidatorInterface $validator): Response
    {
        $sandwich = $this->getDoctrine()->getRepository(Sandwich::class)->findOneBy(['name' => $name]);
        $ingredients = $sandwich->getIngredients();
        for ($i = 0; $ingredients[$i]; $i++)
            $sandwich->removeIngredient($ingredients[$i]);
        $entitymanager = $this->getDoctrine()->getManager();
        $entitymanager->remove($sandwich);
        $entitymanager->flush();
        return $this->redirect("/");
    }
}
