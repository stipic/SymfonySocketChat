<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Message;
use App\Entity\Conversation;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController extends Controller
{
    /**
     * @Route("/message/{id}/new", name="app_new_message")
     * @Method({"POST"})
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
}