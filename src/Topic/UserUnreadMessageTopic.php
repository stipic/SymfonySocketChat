<?php
namespace App\Topic;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulator;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Doctrine\ORM\EntityManager;

class UserUnreadMessageTopic implements TopicInterface, SecuredTopicInterface, PushableTopicInterface
{
    protected $clientManipulator;

    private $_em;

    public function __construct(ClientManipulator $clientManipulator, EntityManager $em)
    {
        $this->clientManipulator = $clientManipulator;
        $this->_em = $em;
    }

    public function secure(ConnectionInterface $connection = null, Topic $topic, WampRequest $request, $payload = null, $exclude = null, $eligible = null, $provider = null)
    {
        if($connection !== null)
        {
            if(!$this->clientManipulator->getClient($connection) instanceof \App\Entity\User) 
            {
                throw new FirewallRejectionException();
            }
        }
        else 
        {
            // ZmqPusher
        }

        //@todo provjeri dali je ID usera jednak ID-u ovog topic-a
    }

    public function onPush(Topic $topic, $request, $payload, $provider)
    {
        $userId = $payload['userId'];
        // $conversationId = $payload['conversationId'];
        $user = $this->_em->getRepository(\App\Entity\User::class)->findOneBy(array(
            'id' => $userId
        ));

        $unreadedNotif = [];    
        $userConversations = $user->getConversations()->getValues();

        foreach($userConversations as $conversation)
        {
            
            $response = $this->_em->getRepository(\App\Entity\User::class)->findNumberOfUnreadedMessages($userId, $conversation->getId());
            $unreadedMsgs = isset($response[0]['count']) ? (int) $response[0]['count'] : 0;
            $unreadedNotif[$conversation->getId()] = $unreadedMsgs;
        }

        $topic->broadcast(json_encode($unreadedNotif));
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
    public function getName()
    {
        return 'user_unread_message.topic';
    }
}
