<?php
namespace App\Controller;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Service\ConversationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/message/{id}/new", name="app_new_message", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function newMessage(Conversation $conversation, Request $request)
    {
        $this->denyAccessUnlessGranted('access', $conversation);
        $msg = $request->request->get('message');
        $messageHandler = $this->get('app_message_handler');
        list($message, $messageView) = $messageHandler->insertMessage($msg, $conversation, array(
            'createdBy' => $this->getUser()
        ));

        return new Response();
    }

    /**
     * @Route("/message/{id}/section", name="app_conversation_messages", condition="request.isXmlHttpRequest()", methods={"GET"})
     */
    public function getConversationSection(Conversation $conversation, Request $request)
    {
        $this->denyAccessUnlessGranted('access', $conversation);

        $conversationHandler = $this->get('app_conversation_handler');
        $sortedConversations = $conversationHandler->getUserConversations($this->getUser(), $conversation);

        $messageHandler = $this->get('app_message_handler');
        $sortedMessages = $messageHandler->getMessageBlocks($conversation);

        // update unreaded messages.

        return $this->render('inc/message-section.inc.html.twig', array(
            'messages' => $sortedMessages,
            'conversations' => $sortedConversations,
        ));
    }

    /**
     * @Route("/message/{id}/from/{startOffset}", name="app_conversation_messages_by_offset", condition="request.isXmlHttpRequest()", methods={"GET"})
     */
    public function getMessageBlocksByOffset(Conversation $conversation, int $startOffset, Request $request)
    {
        $this->denyAccessUnlessGranted('access', $conversation);

        $messageHandler = $this->get('app_message_handler');
        $sortedMessages = $messageHandler->getMessageBlocks($conversation, $startOffset);

        //@todo render msgs & return
        return [];//@todo
    }
}