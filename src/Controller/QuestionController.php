<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\QuestionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="question_index", methods={"GET"})
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $questions = $this->getDoctrine()
            ->getRepository(Question::class)
            ->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    // /**
    //  * @Route("/show/new", name="question_show_created", methods={"GET"})
    //  */
    // public function showCreated($quest_id) {

    // }

    /**
     * @Route("/{cat_id}/new", name="question_new", methods={"GET","POST"})
     */
    public function new($cat_id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($cat_id == 0){
            $form = $this->createFormBuilder()
                ->add('Categorie', TextType::class)
                ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success float-right'],
                'label' => "Validate"
                ])
                ->getForm();


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $category = new Categorie();
                $category->setName($data['Categorie']);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($category);
                $entityManager->flush();

                return $this->redirectToRoute("question_new", ['cat_id' => $category->getId()]);
            }

            return $this->render('category/new.html.twig', [
                'form' => $form->createView(),
            ]);

        }
        
        $form = $this->createFormBuilder()
            ->add('question', TextType::class)
            ->add('reponse1', TextType::class)
            ->add('reponse2', TextType::class)
            ->add('reponse3', TextType::class)
            ->add('true', ChoiceType::class, array(
                'choices'  => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                ),
                'expanded' => true,
                'label' => "Which one is true ?",
            ))
            ->add('save', SubmitType::class, [
            'attr' => ['class' => 'btn btn-success float-right'],
            'label' => "Validate"
            ])
            ->getForm();
            
            
            $repository = $this->getDoctrine()->getRepository(Categorie::class);
            $categorie = $repository->findOneBy([
                'id' => $cat_id
            ]);
                
                
                
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getData();

            $question = new Question();
            $question->setCategorie($categorie);
            $question->setQuestion($data['question']);
            
            $entityManager->persist($question);

            for ($i=1; $i <= 3; $i++) { 
                $reponse[$i] = new Reponse();
                $reponse[$i]->setReponse($data['reponse' . $i]);
                $reponse[$i]->setQuestion($question);

                if ($data['true'] == $i) {
                    $reponse[$i]->setReponseExpected(true);
                }
                else {
                    $reponse[$i]->setReponseExpected(false);
                }

                $entityManager->persist($reponse[$i]);
            }

            $entityManager->flush();
            
            $this->addFlash(
                'notice',
                "🎉 Congratulations ! You've just created a new question in " . $categorie->getName() . "."
            );

            return $this->render('question/created.html.twig', [
                'question' => $question,
                'name' => $categorie,
                'reponses' => $reponse,
                'cat_id' => $cat_id
            ]);
        }

        return $this->render('question/new.html.twig', [
            'form' => $form->createView(),
            'name' => $categorie
        ]);
    }

    /**
     * @Route("/{id}", name="question_show", methods={"GET"})
     */
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Question $question): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Question $question): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index');
    }
}
