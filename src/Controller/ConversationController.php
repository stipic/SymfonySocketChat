<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Conversation;
use App\Form\ConversationFormType;

class ConversationController extends Controller
{
    /**
     * @Route("/conversation/new", name="app_new_conversation")
     * @Method({"GET"})
     */
    public function newConversation(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $conversation = new Conversation();
        $userRepository = $this->get('doctrine')->getManager()->getRepository('App:User');
        $users = $userRepository->findAll();
        
        $form = $this->createForm(ConversationFormType::class, $conversation, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $name = $form->get('name')->getData();
            $selectedUsers = $form->get('users')->getData()->getValues();

            $conversation->setName($name);
            $conversation->setCreatedBy($this->getUser());
            $conversation->setIsChannel(true);
            $conversation->setDeleted(false);

            $conversation->addUserToConversation($this->getUser());
            foreach($selectedUsers as $user)
            {
                $conversation->addUserToConversation($user);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conversation);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('page/new_conversation.html.twig', [
            'conversationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/conversation/{id}", name="app_conversation")
     * @Method({"GET"})
     */
    public function conversation(Conversation $conversation, Request $request)
    {
        $this->denyAccessUnlessGranted('access', $conversation);

        //@todo ovo za svaku poruku radi novi SQL upit, trebalo bi kreirati repositoryMetodu koja jednim pozivom dohvaca.
        $conversationMessages = $conversation->getMessages()->getValues();
        
        $userRepository = $this->get('doctrine')->getManager()->getRepository('App:User');
        $users = $userRepository->findAll();

        return $this->render('page/conversation.html.twig', [
            'messages' => $conversationMessages,
            'users' => $users
        ]);
    }
}