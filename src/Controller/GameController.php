<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Entity\Game;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Route("/game", name="game")
*/
class GameController extends AbstractController
{
    /**
     * @Route("/{cat_id}", name="_cat")
     */
    public function category($cat_id)
    {

        // Retrieve categorie
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categorie = $repository->findOneBy([
            'id' => $cat_id
        ]);

        // TODO: Retrieve questions id, store those id.s somewhere
            // Retrieve questions
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findBy([
            'idCategorie' => $cat_id
        ]);
        // dd($questions[1]->getId());


        
        // TODO#2: Redirect to question method with those id stored
        return $this->redirectToRoute('game_quest', [
            'cat_id' => $cat_id,
            'quest_id' => $questions[0]->getId()
        ]);
    }
    
    /**
     * @Route("/{cat_id}/{quest_id}", name="_quest")
     */
    public function question($cat_id, $quest_id, Request $request)
    {
        // Retrieve categorie
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categorie = $repository->findOneBy([
            'id' => $cat_id
        ]);
        
        // Retrieve questions
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $question = $repository->findOneBy([
            'id' => $quest_id,
            'idCategorie' => $cat_id,
        ]);

        // Retrieve answers
        $repository = $this->getDoctrine()->getRepository(Reponse::class);
        $answers = $repository->findBy([
            'idQuestion' => $quest_id,
        ]);

        $form = $this->createFormBuilder()
            ->add('answer', ChoiceType::class, array(
                'choices'  => array(
                    $answers[0]->getReponse() => $answers[0]->getId(),
                    $answers[1]->getReponse() => $answers[1]->getId(),
                    $answers[2]->getReponse() => $answers[2]->getId(),
                ),
                'expanded' => true,
                'label' => false
            ))
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success float-right'],
                'label' => "Validate"
                ])
            ->getForm();

        $answer = new Game();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get all data
            $data = $form->getData();
            $temp_id = $request->cookies->get('temp_id');

            // dd($this->getUser()->getId());
            if ($this->getUser()) {
                $answer->setUserId($this->getUser()->getId());
            }
            else {
                if ($temp_id) {
                    $answer->setTempId($temp_id);
                }
                else {
                    // Set cookie if doesn't exists
                    $response = new Response();
    
                    $cookieValue1 = rand(0, 2147483647);
                    $cookieValue2 = rand(0, 2147483647);
                    $cookieValue = $cookieValue1 . $cookieValue2;
                    
                    $cookie = new Cookie("temp_id", $cookieValue);
                    $response->headers->setCookie($cookie);
                    $response->send();
                }
            }
            $answer->setQuestionId($quest_id);

            $answer_given = $repository->findOneBy([
                'id' => $data['answer'],
            ]);
            $answer->setAnswer($answer_given->getReponseExpected());

            

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($answer);
            $entityManager->flush();

            // if ($answer_given->getReponseExpected() == true){
            //     $this->addFlash('notice', 'Bien ouèj !');
            // }
            // else {
            //     $this->addFlash('notice', 'Aïe...');
            // }
    
            return $this->redirectToRoute('home');
        }


        return $this->render('game/question.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
            'question' => $question,
            'answers' => $answers
        ]);
    }
}
