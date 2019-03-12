<?php
namespace App\Topic;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulator;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class UserUnreadMessageTopic implements TopicInterface, SecuredTopicInterface
{
    protected $clientManipulator;

    private $_onlineUsers = array();

    public function __construct(ClientManipulator $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
    }

    public function secure(ConnectionInterface $connection = null, Topic $topic, WampRequest $request, $payload = null, $exclude = null, $eligible = null, $provider = null)
    {
        if(!$this->clientManipulator->getClient($connection) instanceof \App\Entity\User) {
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
    public function getName()
    {
        return 'user_unread_message.topic';
    }
}
