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
            $entityManager->persist($formData);
            $entityManager->flush();
            $this->addFlash('success', '¡Usuario registrado exitosamente!');

            /*try {
                $email = (new Email())
                    ->from('pwd.mailer5.2@gmail.com')
                    ->to($form['email']->getData())
                    ->subject("Bienvenido a PWD Mailer")
                    ->text("¡Gracias por registrarte en PWD Mailer!")
                    ->html("<img src='https://www.google.com/url?sa=i&url=https%3A%2F%2Fdepositphotos.com%2Fes%2Fvectors%2Femoji-saludando.html&psig=AOvVaw3FVWszHxuPP2w-ugj02vhs&ust=1697758947547000&source=images&cd=vfe&opi=89978449&ved=0CBEQjRxqFwoTCNjx2InjgIIDFQAAAAAdAAAAABAE'>");
                $mailer->send($email);
                //return new Response("Se envio el email correctamente");
            } catch (\Throwable $th) {
                //return new Response($th->getMessage());
            }*/

            return $this->redirectToRoute(route: 'app_register');
        }

        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView()
        ]);
    }
}
