<?php
namespace App\Service;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\MessageBlock;
use App\Entity\User;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\Common\Persistence\ObjectManager;

class MessageHandler 
{
    private $_em;

    private $_zmqPusher;

    private $_twig;
    
    public function __construct(ObjectManager $em, $pusher, TwigEngine $twig) 
    {
        $this->_em = $em;
        $this->_zmqPusher = $pusher;
        $this->_twig = $twig;
    }

    public function getConversationMessages(Conversation $conversation) 
    {
        $conversationMessages = $conversation->getMessageBlocks()->getValues();
        return $conversationMessages;
    }

    public function insertMessage($msg, Conversation $conversation, $params = array())
    {
        if(!empty($msg) && strlen($msg) > 0)
        {
            $msg = trim($msg);
            $user = $params['createdBy'];

            $message = new Message();
            $message->setContent($msg);
            $message->setCreatedBy($user);
            $message->setDeleted(false);

            if(isset($params['files']))
            {
                foreach($params['files'] as $messageFile)
                {
                    $message->addFileToMessage($messageFile);
                }
            }

            $messageBlock = $this->_isNewMessageChunk($conversation, $user);            
            if($messageBlock instanceof MessageBlock)
            {
                // CHUNK!
                $messageBlock->setUpdatedAt(new \DateTime()); // Updateaj blokov updatedAt !!! VAZNO !!!

                $msgTemplate = [
                    'template' => $this->_twig->render('inc/message-chunk.inc.html.twig', array(
                        'messageChunk' => $message, 
                    )),
                    'msgType' => 'chunk'
                ];
            }
            else 
            {
                // BLOK!
                $messageBlock = new MessageBlock();
                $messageBlock->setConversation($conversation);
                $messageBlock->setCreatedBy($user);
                
                $msgTemplate = [
                    'template' => $this->_twig->render('inc/message-item.inc.html.twig', array(
                        'messageBlock' => [
                            'createdBy' => $user,
                            'getCreatedAt' => new \DateTime(),
                            'messages' => [$message]
                        ], 
                        'messageGroupKeyId' => $message->getId(),

                    )),
                    'msgType' => 'msg_block'
                ];
            }

            $message->setMessageBlock($messageBlock);

            $this->_em->persist($messageBlock);
            $message = $this->_em->merge($message);
            $this->_em->flush();

            $msgTemplate['unreadParams']['msgId'] = $message->getId();
            $msgTemplate['unreadParams']['conversationId'] = $conversation->getId();

            $this->_zmqPusher->push($msgTemplate, 'app_topic_chat', ['conversationId' => $conversation->getId()]);

            return [
                $message,
                $msgTemplate
            ];
        }

        return false;
    }

    /**
     * Metoda koja dohvaca zadnji MessageBlok razgovora i onda provjerava
     * dali je user koji upravo objavljuje poruku vlasnik zadnjeg bloka, ukoliko JE
     * provjerava dali je zadnji "updatedAt" (vrijeme kada je zadnja poruka injectana u blok) u intervalu 
     * od 5 minuta, ukoliko i to je onda je nova poruka CHUNK i vracamo $messageBlok objekt u suprotnom vracamo false
     */
    private function _isNewMessageChunk(Conversation $conversation, User $user)
    {
        $messageBlockRepository = $this->_em->getRepository(MessageBlock::class);
        $lastMessageBlockInConversation = $messageBlockRepository->findOneBy(
            array('conversation' => $conversation->getId()),
            array('id' => 'DESC')
        );

        if($lastMessageBlockInConversation !== null) 
        {
            if($lastMessageBlockInConversation->getCreatedBy() == $user)
            {
                if($lastMessageBlockInConversation->getUpdatedAt() == null)
                {
                    return $lastMessageBlockInConversation;
                }
                else 
                {
                    $diff = (new \DateTime())->diff($lastMessageBlockInConversation->getUpdatedAt());
                    if($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i < 5)
                    {
                        return $lastMessageBlockInConversation;
                    }
                }
            }
        }

        return false;
    }
}