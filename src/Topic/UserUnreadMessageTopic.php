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
use Doctrine\ORM\EntityManagerInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;

class UserUnreadMessageTopic implements TopicInterface, SecuredTopicInterface, PushableTopicInterface
{
    protected $clientManipulator;

    private $_em;

    public function __construct(
        ClientManipulatorInterface $clientManipulator, 
        EntityManagerInterface $em
    )
    {
        $this->clientManipulator = $clientManipulator;
        $this->_em = $em;
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
        // if($connection !== null)
        // {
        //     $pubSubRouteChunk = explode('/', $topic->getId());
        //     if(
        //         !$this->clientManipulator->getClient($connection) instanceof \App\Entity\User ||
        //         !isset($pubSubRouteChunk[1]) || // username
        //         $pubSubRouteChunk[1] != $this->clientManipulator->getClient($connection)->getUsername()
        //     ) 
        //     {
        //         throw new FirewallRejectionException();
        //     }
        // }
        // else 
        // {
        //     // ZmqPusher
        // }
    }

    public function onPush(Topic $topic, WampRequest $request, $payload, string $provider): void
    {
        $responsePayload = '';
        if(isset($payload['userId']))
        {
            // UNREAD NOTIFICATION
            $userId = $payload['userId'];
            $conversationId = $payload['conversationId'];
            $responsePayload = $this->getConversationNotificationArr($userId, $conversationId);
        }
        else 
        {
            // REBUILD SIDEBAR
            $responsePayload = [
                'type' => 'sidebar',
                'template' => $payload
            ];
        }

        $topic->broadcast(json_encode($responsePayload));
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
        $user = $this->clientManipulator->getClient($connection);
        $userId = $user->getId();
        $conversationId = (int) $event;
        //@todo provjeriti dali ovaj userid ima pristup ovom conversation-u !!
        $unreadedNotif = $this->getConversationNotificationArr($userId, $conversationId);

        $topic->broadcast(json_encode($unreadedNotif));
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

    private function getConversationNotificationArr($userId, $conversationId)
    {
        $conversation = $this->_em->getRepository(\App\Entity\Conversation::class)->findOneBy(array(
            'id' => $conversationId
        ));

        if($conversation !== NULL)
        {
            $unreadedNotif = [];

            $response = $this->_em->getRepository(\App\Entity\User::class)->findNumberOfUnreadedMessages($userId, $conversationId);
            $unreadedMsgs = isset($response[0]['count']) ? (int) $response[0]['count'] : 0;
            $unreadedNotif[$conversation->getId()] = $unreadedMsgs;

            return $unreadedNotif;
        }
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName() : string
    {
        return 'user_unread_message.topic';
    }
}
