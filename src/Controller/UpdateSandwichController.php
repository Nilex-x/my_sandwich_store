<?php

namespace App\Controller;

use App\Entity\Sandwich;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Util\StringUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UpdateSandwichController extends AbstractController
{
    /**
     * @Route("/update/{name}", name="update_sandwich")
     */
    public function index(string $name, Request $request): Response
    {
        $sandwich = $this->getDoctrine()->getRepository(Sandwich::class)->findOneBy(['name' => $name]);
        $price = $sandwich->getPrice();
        $ingredients = $sandwich->getIngredients();
        $form = $this->createFormBuilder()
        ->add('name', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'value' => $name
            ]
        ])
        ->add('price', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'value' => $sandwich->getPrice()
            ]
        ])
        ->add('ingredient', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'value' => $ingredients[0]->getName()
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add', 'attr' => [ 'class' => 'btn btn-outline-dark']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $modified = false;
            $change_ingredient = false;
            $ingredient = new Ingredient;
            $newName = $form['name']->getData();
            $newPrice = $form['price']->getData();
            $newIngredient = $form['ingredient']->getData();
            if (!hash_equals($name, $newName)) {
                $modified = true;
                $sandwich->setName($newName);
            }
            if (!hash_equals((string) $price, $newPrice)) {
                $modified = true;
                $sandwich->setPrice((int) $newPrice);
            }
            if (!hash_equals($ingredients[0]->getName(), $newIngredient)) {
                $modified = true;
                $temp = $this->getDoctrine()->getRepository(Ingredient::class)->findOneBy(['name' => $newIngredient]);
                if (temp) {
                    $temp->setSandwich($sandwich);
                    $sandwich->addIngredient($temp);
                } else {
                    $change_ingredient = true;
                    $ingredient->setName($newIngredient);
                    $ingredient->setSandwich($sandwich);
                    $sandwich->addIngredient($ingredient);
                }
            }

            $entitymanager = $this->getDoctrine()->getManager();
            if ($modified) {
                $entitymanager->persist($sandwich);
                if ($change_ingredient)
                    $entitymanager->persist($ingredient);
                $entitymanager->flush();
            }
            return $this->redirect("/");
        }
        return $this->render('update_sandwich/index.html.twig', [
            'Title' => 'Update',
            'sandwich' => $sandwich,
            'form' => $form->createView()
        ]);
    }
}
