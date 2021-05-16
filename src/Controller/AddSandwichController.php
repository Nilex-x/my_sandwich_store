<?php

namespace App\Controller;

use App\Entity\Sandwich;
use App\Entity\Ingredient;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddSandwichController extends AbstractController
{
    /**
     * @Route("/add", name="add_sandwich")
     */
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
        ->add('name', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'style' => 'width:250px;',
                'placeholder' => 'Ex: jambon beurre'
            ]
        ])
        ->add('price',TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'style' => 'width:100px;',
                'placeholder' => 'Ex: 10'
            ]
        ])
        ->add('ingredient', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'style' => 'width:500px;',
                'placeholder' => 'Ex: tomato'
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add', 'attr' => [ 'class' => 'btn btn-outline-dark']])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $change_ingredient = false;
            $ingredients = $form['ingredient']->getData();
            $name = $form['name']->getData();
            $price = $form['price']->getData();
            $sandwich = new Sandwich();
            $ingredient = new Ingredient();
            $sandwich->setName($name);
            $sandwich->setPrice($price);
            $temp = $this->getDoctrine()->getRepository(Ingredient::class)->findOneBy(['name' => $ingredients]);
            if ($temp) {
                $temp->setSandwich($sandwich);
                $sandwich->addIngredient($temp);
            } else {
                $change_ingredient = true;
                $ingredient->setName($ingredients);
                $ingredient->setSandwich($sandwich);
                $sandwich->addIngredient($ingredient);
            }
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($sandwich);
            if ($change_ingredient)
                $entitymanager->persist($ingredient);
            $entitymanager->flush();
            return $this->redirect("/");
        }
        return $this->render('add_sandwich/index.html.twig', [
            'Title' => 'Add',
            'content' => 'tell me your sandwich would you want ?',
            'form' => $form->createView()
        ]);
    }
}
