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

    public function getConversationMessages(Conversation $conversation) 
    {
        //@todo ovo za svaku poruku radi novi SQL upit, trebalo bi kreirati repositoryMetodu koja jednim pozivom dohvaca.
        $sortedMessagesIndex = [];
        $sortedMessages = [];
        
        $conversationMessages = $conversation->getMessages()->getValues();

        foreach($conversationMessages as $messageMasterKey => $messageMaster) 
        {
            if(!isset($sortedMessagesIndex[$messageMasterKey]))
            {
                $sortedMessagesIndex[$messageMasterKey] = true;
                $sortedMessages[$messageMasterKey] = [
                    'groupOwner' => $messageMaster->getCreatedBy(),
                    'groupStartedAt' => $messageMaster->getCreatedAt(),
                    'messages' => [$messageMaster]
                ];

                foreach($conversationMessages as $messageSlaveKey => $messageSlave) 
                {
                    if(!isset($sortedMessagesIndex[$messageSlaveKey]))
                    {
                        if($this->_isMessageChunk($messageMaster, $messageSlave))
                        {
                            // ukoliko su vlasnici poruka isti, a vremenska razlika manja od 5 minuta, mergaj to u jedan chunk
                            $sortedMessagesIndex[$messageSlaveKey] = true;
                            $sortedMessages[$messageMasterKey]['messages'][] = $messageSlave;
                        }
                        else 
                        {
                            break;
                        }
                    }
                }
            }
        }

        return $sortedMessages;
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

            //@todo, provjeri dali je zadnja poruka chunk ili blok
            // i ovisno o svemu vracamo ili cijeli message blok ili samo chunk koji mergamo u postojeci blok.

            //ukoliko je chunk, MORAMO mu reci koji messsage-blok je owner. - UPDATE: NE, NEMORAMO, ako je chunk, UVIJEK je zadnji blok!!!
            if($this->_isNewMessageChunk($conversation))
            {
                $msgTemplate = [
                    'template' => $this->_twig->render('inc/message-chunk.inc.html.twig', array(
                        'messageChunk' => $message, 
                    )),
                    'msgType' => 'chunk'
                ];
            }
            else 
            {
                $formatedMessage = [
                    'groupOwner' => $message->getCreatedBy(),
                    'groupStartedAt' => $message->getCreatedAt(),
                    'messages' => [$message]
                ];

                $msgTemplate = [
                    'template' => $this->_twig->render('inc/message-item.inc.html.twig', array(
                        'message' => $formatedMessage, 
                        'messageGroupKeyId' => $message->getId(),

                    )),
                    'msgType' => 'msg_block'
                ];
            }

            $this->_zmqPusher->push($msgTemplate, 'app_topic_chat', ['conversationId' => $conversation->getId()]);

            return [
                $message,
                $msgTemplate
            ];
        }

        return false;
    }

    private function _isNewMessageChunk(Conversation $conversation)
    {
        $messageRepository = $this->_em->getRepository(Message::class);
        $lastTwoMessagesInConversation = $messageRepository->findBy(
            array('conversation' => $conversation->getId()),
            array('id' => 'DESC'),
            2,
            0
        );

        if(isset($lastTwoMessagesInConversation[0]) && isset($lastTwoMessagesInConversation[1]))
        {
            $messageSlave = $lastTwoMessagesInConversation[0]; // 0 zadnja poruka
            $messageMaster = $lastTwoMessagesInConversation[1]; // 1 predzadnja poruka

            if($this->_isMessageChunk($messageMaster, $messageSlave))
            {
                return true;
            }
        }

        return false;
    }

    private function _isMessageChunk(Message $messageMaster, Message $messageSlave)
    {
        $diff = $messageSlave->getCreatedAt()->diff($messageMaster->getCreatedAt());
        if(
            $messageMaster->getCreatedBy() === $messageSlave->getCreatedBy() && 
            $diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i < 5 
        )
        {
            return true;
        }

        return false;
    }
}