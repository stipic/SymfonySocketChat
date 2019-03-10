<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Conversation;
use App\Form\ConversationFormType;
use App\Service\ConversationHandler;

class ConversationController extends Controller
{
    /**
     * @Route("/conversation/new", name="app_new_conversation")
     * @Method({"GET"})
     */
    public function newConversation(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $conversation = new Conversation();
        $userRepository = $this->get('doctrine')->getManager()->getRepository('App:User');
        $users = $userRepository->findAll();
        
        $form = $this->createForm(ConversationFormType::class, $conversation, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager = $this->getDoctrine()->getManager();

            $name = $form->get('channelName')->getData();
            $selectedUsers = $form->get('users')->getData()->getValues();

            $conversation->setChannelName($name);
            $conversation->setCreatedBy($this->getUser());
            $conversation->setIsChannel(true);
            $conversation->setIsChannelPublic(false);
            $conversation->setDeleted(false);

            $this->getUser()->addConversation($conversation);
            foreach($selectedUsers as $user)
            {
                $user->addConversation($conversation);
                $entityManager->persist($user);
            }

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

        $conversationHandler = $this->get('app_conversation_handler');
        $sortedConversations = $conversationHandler->getUserConversations($this->getUser(), $conversation);

        $messageHandler = $this->get('app_message_handler');
        $sortedMessages = $messageHandler->getConversationMessages($conversation);

        return $this->render('page/conversation.html.twig', [
            'messages' => $sortedMessages,
            'conversations' => $sortedConversations
        ]);
    }
}