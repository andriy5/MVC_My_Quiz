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
     * @Route("/{cat_id}", name="_new")
     */
    public function newGame($cat_id, Request $request)
    {

        // Retrieve categorie
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categorie = $repository->findOneBy([
            'id' => $cat_id
        ]);
        // Retrieve questions
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repository->findBy([
            'idCategorie' => $cat_id
        ]);

        // Set new biggame_id + cookie
        $repository = $this->getDoctrine()
        ->getRepository(Game::class);
    
        $query = $repository->createQueryBuilder('g')
            ->orderBy('g.biggame_id', 'DESC')
            ->getQuery();
        
        $biggame_id = $query->setMaxResults(1)->getOneOrNullResult();        
        $biggame_id = $biggame_id->getBigGameId()+1;
        $response = new Response();     
        $cookie = new Cookie("biggame_id", $biggame_id);
        $response->headers->setCookie($cookie);
        
        $limit = 10;
        $queue_questions = [];
        foreach ($questions as $key => $question) {
            array_push($queue_questions, $question->getId());

        }
        shuffle($queue_questions);
        while (count($queue_questions) > $limit ){
            array_shift($queue_questions);
        }

        $cookie = new Cookie("queue", json_encode($queue_questions));
        $response->headers->setCookie($cookie);
        $response->send();

        return $this->redirectToRoute('game_quest', [
            'cat_id' => $cat_id,
            'quest_id' => $queue_questions[0]
        ]);

        return $this->redirectToRoute('home');
    }
    
    /**
     * @Route("/{cat_id}/{quest_id}", name="_quest")
     */
    public function question($cat_id, $quest_id, Request $request)
    {
        // Desactivate to answer two times to the same question
        $repository = $this->getDoctrine()->getRepository(Game::class);
        $dejavu = $repository->findOneBy([
            'question_id' => $quest_id,
            'biggame_id' => $request->cookies->get('biggame_id')
        ]);
        
        $queue_questions = json_decode($request->cookies->get('queue'));

        if ($dejavu) {
            return $this->redirectToRoute('game_quest', [
                'cat_id' => $cat_id,
                'quest_id' => $queue_questions[0]
            ]);
        }




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

            // Set all data
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
            $answer->setBiggameId($request->cookies->get('biggame_id'));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($answer);
            $entityManager->flush();

            // if ($answer_given->getReponseExpected() == true){
            //     $this->addFlash('notice', 'Bien ouèj !');
            // }
            // else {
            //     $this->addFlash('notice', 'Aïe...');
            // }

            // Next question
            $queue_questions = json_decode($request->cookies->get('queue'));
            // dd($queue_questions);
            array_shift($queue_questions);
            $response = new Response();     
            $cookie = new Cookie("queue", json_encode($queue_questions));
            $response->headers->setCookie($cookie);
            $response->send();
            
            if (empty($queue_questions)){
                $response->headers->clearCookie('queue');
                $response->send();
                // TODO Template Finish
                echo "FINISH";
            }
            else {
                return $this->redirectToRoute('game_quest', [
                    'cat_id' => $cat_id,
                    'quest_id' => $queue_questions[0]
                ]);
            }


        }


        return $this->render('game/question.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
            'question' => $question,
            'answers' => $answers
        ]);
    }

    // /**
    //  * @Route("/end", name="_end")
    //  */
    // public function end() {

    // }

}
