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
use App\Entity\User;

class ConversationController extends Controller
{
    /**
     * @Route("/channel/new", name="app_new_channel", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function newChannel(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $em = $this->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);

        $channelName = $request->get('channelName');
        $channelIsPrivate = $request->get('channelIsPrivate');
        $channelUsers = $request->get('channelUsers');

        if($channelIsPrivate !== NULL)
        {
            $channelIsPrivate = true;

            $users = explode(',', $channelUsers);
            $users = $userRepository->findBy([
                'username' => $users
            ]);

            $roleHelper = $this->get('app_role_helper');
            $roleHierarchy = $roleHelper->getParentRoles('ROLE_MODERATOR');

            $additionalUsersToAdd = $userRepository->findByRole($roleHierarchy, $users);
            $users = array_merge($users, $additionalUsersToAdd);
        }
        else 
        {
            $channelIsPrivate = false;
            $users = $userRepository->findAll();
        }

        if(!empty($users) && is_array($users))
        {
            //@todo conversationHandler->createNewConversation();
            $conversation = new Conversation();
            $conversation->setChannelName($channelName);
            $conversation->setCreatedBy($this->getUser());
            $conversation->setIsChannel(true);
            $conversation->setIsChannelPublic(!$channelIsPrivate);
            $conversation->setDeleted(false);

            foreach($users as $user)
            {
                $user->addConversation($conversation);
                $em->persist($user);
            }

            $em->persist($conversation);
            $em->flush();
        }

        return new Response();
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
        $messageBlocks = $messageHandler->getConversationMessages($conversation);

        $em = $this->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findAll();
        
        return $this->render('page/conversation.html.twig', [
            'messages' => $messageBlocks,
            'conversations' => $sortedConversations,
            'users' => $users
        ]);
    }
}