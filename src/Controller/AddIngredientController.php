<?php

namespace App\Controller;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddIngredientController extends AbstractController
{
    /**
     * @Route("/add/ingredient", name="add_ingredient")
     */
    public function index(Request $request, ValidatorInterface $validator)
    {
        $form = $this->createFormBuilder()
        ->add('input', TextType::class, [
            'attr' => [
                'placeholder' => 'Ingredient'
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add'])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $name = $form['input']->getData();
            $ingredient = $this->getDoctrine()->getRepository(Ingredient::class)>findOneBy([
                'name' => $name
            ]);
            if ($ingredient) {
                return $this->render('home/index.html.twig', [
                    'content' => 'Your ingredient already exist'
                ]);
            }
            $ingredient = new Ingredient();
            $ingredient->setName($name);
            $errors = $validator->validate($ingredient);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            $entityManager->persist($ingredient);
            $entityManager->flush();
            return $this->render('home/index.html.twig', [
                'content' => $ingredient->GetName()
            ]);
        }
        return $this->render('add_ingredient/index.html.twig', [
            'Title' => 'Add ingredient',
            'form' => $form->createView()
        ]);
    }
}
