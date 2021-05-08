<?php

namespace App\Controller;

use App\Entity\Sandwich;
use App\Entity\Ingredient;
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
     * @Route("/add/sandwich", name="add_sandwich")
     */
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        $form = $this->createFormBuilder()
        ->add('name', TextType::class, [
            'attr' => [
                'placeholder' => 'Name',
                'type' => 'text'
            ]
        ])
        ->add('price', NumberType::class, [
            'attr' => [
                'placeholder' => 'Price',
            ]
        ])
        ->add('ingredient', TextType::class, [
            'attr' => [
                'placeholder' => 'ingredient'
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add'])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $price = $form['price']->getData();
            $name_ingredient = $form['ingredient']->getData();
            $ingredient = $this->getDoctrine()->getRepository(Ingredient::class)->findOneBy([
                'name' => $name_ingredient,
            ]);
            if (!$ingredient) {
                return $this->render('home/index.html.twig', [
                    'content' => 'Your ingredient not exist'
                ]);
            }
            $sandwich = new Sandwich();
            $sandwich->setName($name);
            $sandwich->setPrice($price);
            $ingredient->setSandwich($sandwich);
            $sandwich->addIngredient($ingredient);
            $errors = $validator->validate($sandwich);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            $this->getDoctrine()->getManager()->persist($sandwich)->flush();
            return $this->render('home/index.html.twig', [
                'content' => $sandwich->GetName()
            ]);
        }
        return $this->render('add_sandwich/index.html.twig', [
            'Title' => 'Add sandwich',
            'form' => $form->createView()
        ]);
    }
}
