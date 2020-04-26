<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{  

    /**
     * @Route("/show", name="profile_show", methods={"GET"})
     */
    public function show(): Response
    {
        $user = $this->getUser();
        // dump($user). die;
        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", name="profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        $currentMail = $user->getEMail();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Compare the new and the old email
            // $data = $form->getData();
            // $data = $request->request->get('email');

            $this->getDoctrine()->getManager()->flush();
            $newMail = $user->getEMail();

            
            if ($currentMail != $newMail) {
              // Reset value verified_at on the DB
              // dump($user). die;
              $entityManager = $this->getDoctrine()->getManager();
              $user->eraseVerifiedAt();
              $entityManager->flush();


              // Send an email
              $key = sha1($user->getEmail());
              $linkActivation = "http://localhost:8000/activate/{$user->getId()}/$key";
  
              $email = (new TemplatedEmail())
                  ->from('hello@example.com')
                  ->to($user->getEmail())
                  //->cc('cc@example.com')
                  //->bcc('bcc@example.com')
                  //->replyTo('fabien@example.com')
                  //->priority(Email::PRIORITY_HIGH)
                  ->subject('Vous êtes inscrits sur My Quiz')
                  ->htmlTemplate('email/verify.html.twig')
                  ->context([
                      'linkActivation' => $linkActivation
                  ])
                  ;
  
              $mailer->send($email);

            }
            
            $this->addFlash(
              'notice',
              'Your changes were saved!'
          );
            return $this->redirectToRoute('profile_show');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="profile_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $user = $this->getUser();


        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Delete the session to be able to redirect to the homepage correctly
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
}
