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

class AddIngredientController extends AbstractController
{
    /**
     * @Route("/add/ingredient/{name}", name="add_ingredient")
     */
    public function index(string $name, Request $request): Response
    {
        $sandwich = $this->getDoctrine()->getRepository(Sandwich::class)->findOneBy(['name' => $name]);
        $form = $this->createFormBuilder()
        ->add('name', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'style' => 'width:250px;',
                'placeholder' => 'Ex: poulet'
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add', 'attr' => [ 'class' => 'btn btn-outline-dark']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_ingredient = false;
            $ingredients = $sandwich->getIngredients();
            $ingredient = new Ingredient;
            $new_name_ingredient = $form['name']->getData();
            for ($i = 0; $ingredients[$i]; $i++) {
                $name_ingredient = $ingredients[$i]->getName();
                if (hash_equals($name_ingredient, $new_name_ingredient)) {
                    return $this->render('add_ingredient/index.html.twig', [
                        'Title' => 'error',
                        'content' => 'the ingredient is already add to sandwich',
                        'form' => $form->createView()
                    ]);
                }
            }
            $temp = $this->getDoctrine()->getRepository(Ingredient::class)->findOneBy(['name' => $new_name_ingredient]);
            if ($temp) {
                $temp->setSandwich($sandwich);
                $sandwich->addIngredient($temp);
            } else {
                $new_ingredient = true;
                $ingredient->setName($new_name_ingredient);
                $ingredient->setSandwich($sandwich);
                $sandwich->addIngredient($ingredient);
            }
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($sandwich);
            if ($new_ingredient)
                $entitymanager->persist($ingredient);
            $entitymanager->flush();
            return $this->redirect("/");
        }
        return $this->render('add_ingredient/index.html.twig', [
            'Title' => 'add ingredient',
            'content' => 'name of ingredient ?',
            'form' => $form->createView()
        ]);
    }
}
