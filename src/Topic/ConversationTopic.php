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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ConversationTopic implements TopicInterface, SecuredTopicInterface
{
    protected $clientManipulator;

    private $_em;

    private $_authChecker;

    private $_conversation;

    private $_usersWhoWriting = array();

    public function __construct(ClientManipulator $clientManipulator, EntityManager $em, AuthorizationChecker $authChecker)
    {
        $this->clientManipulator = $clientManipulator;
        $this->_em = $em;
        $this->_authChecker = $authChecker;
    }

    public function secure(ConnectionInterface $connection = null, Topic $topic, WampRequest $request, $payload = null, $exclude = null, $eligible = null, $provider = null)
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
            isset($clientPayload->conversationId) && 
            isset($clientPayload->payloadType)
        )
        {
            if($clientPayload->payloadType == 'writing')
            {
                if(!empty($clientPayload->message))
                {
                    $this->_usersWhoWriting[$clientPayload->username] = array(
                        'displayName' => $clientPayload->displayName,
                        'message' => 'is writing...'
                    );
                }
                else 
                {
                    unset($this->_usersWhoWriting[$clientPayload->username]);
                }

                $clientPayload->message = $this->_usersWhoWriting;
            }
            else 
            {
                $userSessionId = $connection->WAMP->sessionId;
                $exclude = [$userSessionId];
                
                $user = $this->clientManipulator->getClient($connection);

                $message = new \App\Entity\Message();
                $message->setConversation($this->_conversation);
                $message->setContent($clientPayload->message);
                $message->setCreatedBy($user);
                $message->setDeleted(false);

                $this->_em->merge($message);
                $this->_em->flush();
            }

            $topic->broadcast(json_encode($clientPayload), $exclude);
        }
        
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
        $clientPayload = array(
            'payloadType' => 'writing',
            'message' => $this->_usersWhoWriting
        );

        $topic->broadcast(json_encode($clientPayload), [], [$connection->WAMP->sessionId]);
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

        $clientPayload = array(
            'payloadType' => 'writing',
            'message' => $this->_usersWhoWriting
        );
        $topic->broadcast(json_encode($clientPayload));
    }

    /**
    * Like RPC is will use to prefix the channel
    * @return string
    */
    public function getName()
    {
        return 'conversation.topic';
    }
}