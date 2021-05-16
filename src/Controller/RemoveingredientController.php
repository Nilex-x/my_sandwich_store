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

class RemoveingredientController extends AbstractController
{
    /**
     * @Route("/remove/ingredient/{name}", name="removeingredient")
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
            $ingredients = $sandwich->getIngredients();
            $ingredient = null;
            $new_name_ingredient = $form['name']->getData();
            for ($i = 0; $ingredients[$i]; $i++) {
                $name_ingredient = $ingredients[$i]->getName();
                if (hash_equals($name_ingredient, $new_name_ingredient)) {
                    $ingredient = $ingredients[$i];
                    break;
                }
            }
            if ($ingredient == null) {
                return $this->render('add_ingredient/index.html.twig', [
                    'Title' => 'error',
                    'content' => 'the ingredient is not exist in the sandwich',
                    'form' => $form->createView()
                ]);
            }
            $ingredient->setSandwich(null);
            $sandwich->removeIngredient($ingredient);
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($sandwich);
            $entitymanager->flush();
            return $this->redirect("/");
        }
        return $this->render('remove/index.html.twig', [
            'Title' => 'remove ingredient',
            'content' => 'name of ingredient ?',
            'form' => $form->createView()
        ]);
    }
}
