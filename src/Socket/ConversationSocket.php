<?php
namespace App\Socket;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulator;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\MessageComponentInterface;
use Doctrine\ORM\EntityManager;

class ConversationSocket implements TopicInterface, SecuredTopicInterface
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
        if(!$this->clientManipulator->getClient($connection) instanceof \App\Entity\User)
        {
            throw new FirewallRejectionException();
        }

        // Provjeri dali korisnik ima permission na ovaj razgovor.
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
        // $message = 'SERVER: ' . $connection->resourceId . " has joined " . $topic->getId();
        // $topic->broadcast(['msg' => $message]);
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
        //this will broadcast the message to ALL subscribers of this topic.
        // $topic->broadcast(['msg' => $connection->resourceId . " has left " . $topic->getId()]);
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
        $clientPayload = json_decode($event);
        if(
            isset($clientPayload->username) &&
            isset($clientPayload->wsConversationRoute) &&
            isset($clientPayload->displayName) &&
            isset($clientPayload->message) &&
            isset($clientPayload->conversationId)
        )
        {
            $userSessionId = $connection->WAMP->sessionId;
            $exclude = [$userSessionId];
            $conversationId = $clientPayload->conversationId;
            $conversation = $this->_em->getRepository(\App\Entity\Conversation::class)->findOneBy(array(
                'id' => $conversationId
            ));
            $user = $this->clientManipulator->getClient($connection);

            $message = new \App\Entity\Message();
            $message->setConversation($conversation);
            $message->setContent($clientPayload->message);
            $message->setCreatedBy($user);
            $message->setDeleted(false);

            $this->_em->merge($message);
            $this->_em->flush();

            //  * Send a message to all the connections in this topic
            //  * @param string|array $msg Payload to publish
            //  * @param array $exclude A list of session IDs the message should be excluded from (blacklist)
            //  * @param array $eligible A list of session Ids the message should be send to (whitelist)
            //  * @return Topic The same Topic object to chain
            //     public function broadcast($msg, array $exclude = array(), array $eligible = array());

            
            $topic->broadcast($event, $exclude);
        }
        
    }

    /**
    * Like RPC is will use to prefix the channel
    * @return string
    */
    public function getName()
    {
        return 'acme.topic';
    }
}