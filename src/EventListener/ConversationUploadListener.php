<?php
namespace App\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\PreUploadEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\File;

class ConversationUploadListener
{
    private $_em;

    private $_authChecker;

    private $_tokenStorage;

    private $_params;

    private $_conversation;

    private $_pusher;

    public function __construct(ObjectManager $em, AuthorizationChecker $authChecker, TokenStorageInterface $token, ParameterBagInterface $params, $pusher)
    {
        $this->_em = $em;
        $this->_authChecker = $authChecker;
        $this->_tokenStorage = $token;
        $this->_params = $params;
        $this->_pusher = $pusher;
    }
    
    public function afterUpload(PostUploadEvent $event)
    {
        $user = $this->_tokenStorage->getToken()->getUser();
        $file = $event->getRequest()->files->get('qqfile');
        $path = '/' . $this->_params->get('storage_dir') . '/' . $event->getFile()->getPathname();
        $filename = $file->getClientOriginalName();
        $mimeType = $event->getFile()->getMimeType();
        $filesize = $event->getRequest()->server->get('CONTENT_LENGTH');

        $content = '<img width="100" src="' . $path . '" />';
        $payload = array(
            'username' => $user->getUsername(),
            'wsConversationRoute' => 'conversation/' . $this->_conversation->getId(),
            'displayName' => $user->getDisplayName(),
            'message' => $content,
            'conversationId' => $this->_conversation->getId(),
            'payloadType' => 'text'
        );

        $file = new File();
        $file->setName($filename);
        $file->setPath($path);
        $file->setMimeType($mimeType);
        $file->setFileSize($filesize);

        $this->_em->persist($file);

        // Create new Message!

        $message = new \App\Entity\Message();
        $message->setConversation($this->_conversation);
        $message->setContent($content);
        $message->setCreatedBy($user);
        $message->addFileToMessage($file);
        $message->setDeleted(false);

        $this->_em->persist($message);
        $this->_em->flush();

        // Push!
        $this->_pusher->push($payload, 'app_topic_chat', ['conversationId' => $this->_conversation->getId()]);
    }

    public function beforeUpload(PreUploadEvent $event)
    {
        $conversationId = (int) $event->getRequest()->get('conversationId');
        $this->_conversation = $this->_em->getRepository(\App\Entity\Conversation::class)->findOneBy(array(
            'id' => $conversationId
        ));

        if(!$this->_authChecker->isGranted('access', $this->_conversation))
        {
            throw new AccessDeniedException();
        }
    }
}