<?php

namespace App\Controller;

// require_once '/home/andriy/Code/MVC_My_Quiz/vendor/autoload.php';

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTime;
use Doctrine\DBAL\Schema\View;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;




class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // dump($_REQUEST). die;
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
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

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activate/{id}/{key}")
     */
    public function activate($id, $key)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $checkKey = sha1($user->getEmail());
            
        if ($checkKey === $key) {
        
            
            // Checker si activation deja faite
            if ($user->getVerifiedAt() == null) {

                // sinon Rajouter value dans DB "verified"
                $user->setVerifiedAt(New DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // Faire page correspondant
                return $this->redirectToRoute('home');

            }  else {

                // si oui msg->deja activer
                // Faire page correspondant
                return $this->redirectToRoute('home');
            }
            
        }
        else {
            // echo "pas correct";
        }            
    }
}
