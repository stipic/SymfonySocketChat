<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Conversation;
use App\Entity\User;
use App\Service\ConversationHandler;
use App\Service\MessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConversationController extends AbstractController
{
    /**
     * @Route("/channel/new/{id}", name="app_new_channel", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function newChannel(
        Conversation $currentConversation, 
        Request $request,
        ConversationHandler $conversationHandler
    )
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $response = [
            'success' => false,
            'status' => 200,
            'data' => [
                'message' => 'Failed',
                'errors' => [],
            ]
        ];

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
            list($response['success'], $response['data']['errors']) = $conversationHandler->createNewConversation(
                $channelName,
                $this->getUser(),
                true,
                !$channelIsPrivate,
                false,
                $users,
                $currentConversation
            );
            
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/conversation/{id}", methods={"GET"}, name="app_conversation")
     */
    public function conversation(
        Conversation $conversation,
        ConversationHandler $conversationHandler,
        MessageHandler $messageHandler,
        EntityManagerInterface $em
    )
    {
        $this->denyAccessUnlessGranted('access', $conversation);
        
        $sortedConversations = $conversationHandler->getUserConversations($this->getUser(), $conversation);

        $messageBlocks = $messageHandler->getMessageBlocks($conversation);

        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findAll();
        
        return $this->render('page/conversation.html.twig', [
            'messages' => $messageBlocks,
            'conversations' => $sortedConversations,
            'users' => $users
        ]);
    }
}