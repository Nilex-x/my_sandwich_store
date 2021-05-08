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
     * @Route("/remove/sandwich", name="remove_sandwich")
     */
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        $form = $this->createFormBuilder()
        ->add('name', TextType::class, [
            'attr' => [
                'placeholder' => 'sandwich'
            ]
        ])
        ->add('search', SubmitType::class, ['label' => 'Add'])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getdata();
            $sandwich = $this->getDoctrine()->getRepository(Sandwich::class)->findOneBy([
                'name' => $name,
            ]);
            if (!$sandwich) {
                return $this->render('home/index.html.twig', [
                    'Title' => 'Error',
                    'content' => 'Your sandwich not exist'
                ]);
            }
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->remove($sandwich);
            $entitymanager->flush();
        }
        return $this->render('remove/index.html.twig', [
            'Title' => 'Remove',
            'content' => 'which sandwich whould you remove ?',
            'form' => $form->createView()
        ]);
    }
}
