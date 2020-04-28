<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/game", name="game")
*/
class GameController extends AbstractController
{
    public function _construct() {

    }

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
        // dd($answers);
            
        return $this->render('game/question.html.twig', [
            'categorie' => $categorie,
            'question' => $question,
            'answers' => $answers
        ]);
    }
}
