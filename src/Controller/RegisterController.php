<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $form['password']->getData()
            ));
            try{
                $entityManager->persist($formData);
                $entityManager->flush();
                $this->addFlash('success', '¡Usuario registrado exitosamente!');
                try {
                    $email = (new Email())
                        ->from('pwd.mailer5.2@gmail.com')
                        ->to($form['email']->getData())
                        ->subject("Bienvenido a PWD Mailer")
                        ->text("¡Gracias por registrarte en PWD Mailer!")
                        ->html("<img src='https://st2.depositphotos.com/1001911/6524/v/450/depositphotos_65242063-stock-illustration-hat-tip-emoticon.jpg'>");
                    $mailer->send($email);
                } catch (\Throwable $th) {
                    $this->addFlash('success', '¡El correo no fue enviado!');
                }
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                // Manejar el error de clave primaria duplicada
                $this->addFlash('success', '¡El correo ya está en uso!');
            }
            
        }

        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView()
        ]);
    }
}
