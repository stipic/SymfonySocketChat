<?php
namespace App\Service;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\Common\Persistence\ObjectManager;

class MessageHandler 
{
    private $_em;

    private $_zmqPusher;

    private $_twig;
    
    public function __construct(ObjectManager $em, $pusher, $twig) 
    {
        $this->_em = $em;
        $this->_zmqPusher = $pusher;
        $this->_twig = $twig;
    }

    public function insertMessage($msg, Conversation $conversation, $params = array())
    {
        if(!empty($msg) && strlen($msg) > 0)
        {
            $msg = trim($msg);

            $message = new Message();
            $message->setConversation($conversation);
            $message->setContent($msg);

            if(isset($params['createdBy']))
            {
                $message->setCreatedBy($params['createdBy']);
            }
            
            $message->setDeleted(false);

            if(isset($params['files']))
            {
                foreach($params['files'] as $messageFile)
                {
                    $message->addFileToMessage($messageFile);
                }
            }

            $this->_em->merge($message);
            $this->_em->flush();

            $msgTemplate = $this->_twig->render('inc/message-item.inc.html.twig', array(
                'message' => $message
            ));

            $this->_zmqPusher->push($msgTemplate, 'app_topic_chat', ['conversationId' => $conversation->getId()]);

            return [
                $message,
                $msgTemplate
            ];
        }

        return false;
    }
}