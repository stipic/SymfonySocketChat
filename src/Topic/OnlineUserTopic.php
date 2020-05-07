<?php
namespace App\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulator;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class OnlineUserTopic implements TopicInterface, SecuredTopicInterface
{
    protected $clientManipulator;

    private $_onlineUsers = array();

    public function __construct(ClientManipulatorInterface $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
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
        if(!$this->clientManipulator->getClient($connection) instanceof \App\Entity\User)
        {
            throw new FirewallRejectionException();
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
        $eligible = [$connection->WAMP->sessionId];
        $topic->broadcast(json_encode($this->_onlineUsers), [], $eligible);
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
        $user = $this->clientManipulator->getClient($connection);
        $this->_onlineUsers[$user->getId()] = $user->getUsername();
        $topic->broadcast(json_encode($this->_onlineUsers));
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
        $user = $this->clientManipulator->getClient($connection);
        unset($this->_onlineUsers[$user->getId()]);
        $topic->broadcast(json_encode($this->_onlineUsers));
    }

    /**
    * Like RPC is will use to prefix the channel
    * @return string
    */
    public function getName() : string
    {
        return 'online_user.topic';
    }
}