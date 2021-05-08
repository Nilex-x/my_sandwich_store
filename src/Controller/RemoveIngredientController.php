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

class RemoveIngredientController extends AbstractController
{
    /**
     * @Route("/remove/ingredient", name="remove_ingredient")
     */
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        $form = $this->createFormBuilder()
        ->add('name', TextType::class, [
            'attr' => [
                'placeholder' => 'Ingredient'
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add'])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getdata();
            $ingredient = $this->getDoctrine()->getRepository(Ingredient::class)->findOneBy([
                'name' => $name,
            ]);
            if (!$ingredient) {
                return $this->render('home/index.html.twig', [
                    'Title' => 'Error',
                    'content' => 'Your ingredient not exist'
                ]);
            }
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->remove($ingredient);
            $entitymanager->flush();
        }
        return $this->render('remove_ingredient/index.html.twig', [
            'Title' => 'Remove',
            'content' => 'which ingredient whould you remove ?',
            'form' => $form->createView()
        ]);
    }
}
