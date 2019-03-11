<?php
namespace App\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulator;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\MessageComponentInterface;

class ConversationNotificationTopic implements TopicInterface, SecuredTopicInterface
{
    protected $clientManipulator;

    private $_usersWhoWriting = array();

    public function __construct(ClientManipulator $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
    }

    public function secure(ConnectionInterface $connection = null, Topic $topic, WampRequest $request, $payload = null, $exclude = null, $eligible = null, $provider = null)
    {
        if(!$this->clientManipulator->getClient($connection) instanceof \App\Entity\User)
        {
            throw new FirewallRejectionException();
        }

        // ne zelimo raditi bzvz upit na bazu dali korisnik ima prava 'access' na ovaj conversation
        // pošto je bezopasno, odnosno ako se i uspije spojiti znamo da je to vec neki postojeci korisnik
        // i ne moze raditi nikakvu stetu osim fakeati notifikaciju u nekom kanalu da on piše
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
        $isWriting = (bool) $event;
        if($isWriting == true)
        {
            $this->_usersWhoWriting[$user->getUsername()] = array(
                'username' => $user->getUsername(), //@todo
                'message' => 'is writing...'
            );
        }
        else 
        {
            unset($this->_usersWhoWriting[$user->getUsername()]);
        }

        $userSessionId = $connection->WAMP->sessionId;
        $exclude = [$userSessionId];

        $topic->broadcast(json_encode($this->_usersWhoWriting), $exclude);
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
        $topic->broadcast(json_encode($this->_usersWhoWriting), [], [$connection->WAMP->sessionId]);
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
        unset($this->_usersWhoWriting[$user->getUsername()]);
        $topic->broadcast(json_encode($this->_usersWhoWriting));
    }

    /**
    * Like RPC is will use to prefix the channel
    * @return string
    */
    public function getName()
    {
        return 'writing_notification.topic';
    }
}