<?php
namespace App\Topic;

use Ratchet\Wamp\Topic;
use Doctrine\ORM\EntityManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Doctrine\ORM\EntityManagerInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;

use Gos\Bundle\WebSocketBundle\Pusher\Amqp\AmqpPusher;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulator;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ConversationTopic implements TopicInterface, SecuredTopicInterface, PushableTopicInterface
{
    protected $clientManipulator;

    private $_em;

    private $_authChecker;

    private $_conversation;

    private $_amqpPusher;

    public function __construct(
        ClientManipulatorInterface $clientManipulator, 
        EntityManagerInterface $em, 
        AuthorizationCheckerInterface $authChecker, 
        ContainerInterface $container
    )
    {
        $this->clientManipulator = $clientManipulator;
        $this->_em = $em;
        $this->_authChecker = $authChecker;
        $this->_amqpPusher = $container->get('gos_web_socket.pusher.amqp');
    }

    public function secure(
        ?ConnectionInterface $connection,
        Topic $topic,
        WampRequest $request,
        $payload = null,
        ?array $exclude = [],
        ?array $eligible = null,
        ?string $provider = null
    ): void
    {
        if($connection !== null)
        {
            if(!$this->clientManipulator->getClient($connection) instanceof \App\Entity\User)
            {
                throw new FirewallRejectionException();
            }

            $pubSubRouteChunk = explode('/', $topic->getId());
            $conversationId = isset($pubSubRouteChunk[1]) ? (int) $pubSubRouteChunk[1] : false;
            if($conversationId === false)
            {
                // krivo sam rastavio pubsub rutu, izbaci korisnika prije nego dođe do problema
                throw new FirewallRejectionException();
            }

            $this->_conversation = $this->_em->getRepository(\App\Entity\Conversation::class)->findOneBy(array(
                'id' => $conversationId
            ));

            if(!$this->_authChecker->isGranted('access', $this->_conversation))
            {
                // korisnik nema prava pristupa ovom razgovoru.

                throw new FirewallRejectionException();
            }
        }
        else 
        {
            // ZMQ Pusher!
        }
    }

    public function onPush(Topic $topic, WampRequest $request, $payload, string $provider): void
    {
        if(isset($payload['unreadParams']))
        {
            $messageId = $payload['unreadParams']['msgId'];
            $conversationId = $payload['unreadParams']['conversationId'];
            if($this->_conversation === NULL)
            {
                $this->_conversation = $this->_em->getRepository(\App\Entity\Conversation::class)->findOneBy(array(
                    'id' => $conversationId
                ));
            }

            $message = $this->_em->getRepository(\App\Entity\Message::class)->findOneBy(array(
                'id' => $messageId
            ));
            
            if($message !== null)
            {
                $conversationUsers = $this->_conversation->getUsers()->getValues();
                foreach($conversationUsers as $userInConversation) 
                {
                    $user = $this->clientManipulator->findByUsername($topic, $userInConversation->getUsername());
                    if($user === false) 
                    {
                        // korisnik $userInConversation se trenutno ne nalazi u ovom chat-u, stoga on ide na listu.
                        $userInConversation->addUnreadedMessage($message);

                        $this->_em->persist($userInConversation);
                        $this->_em->flush();

                        // ZmqPusher sad pusha notifikaciju u njegov private kanal kako bi mu prikazali odma notifikaciju.
                        
                        $receiverPayload = [
                            'type' => 'unread', // vs 'read'
                            'conversationId' => $conversationId,
                            'messageId' => $messageId,
                            'userId' => $userInConversation->getId()
                        ];

                        $this->_amqpPusher->push($receiverPayload, 'app_unread_messages', ['username' => $userInConversation->getUsername()]);
                    }
                }
            }

            unset($payload['unreadParams']);

            $topic->broadcast([
                'msg' => $payload,
            ]);
        }
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        // u ovom kanalu nema vise publisha.
        // throw new FirewallRejectionException();
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
    }

    /**
    * Like RPC is will use to prefix the channel
    * @return string
    */
    public function getName() : string
    {
        return 'conversation.topic';
    }
}