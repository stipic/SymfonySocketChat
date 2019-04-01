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
use App\Service\MessageHandler;
use App\Entity\File;

class ConversationUploadListener
{
    private $_em;

    private $_authChecker;

    private $_tokenStorage;

    private $_params;

    private $_conversation;

    private $_messageHandler;

    public function __construct(
        ObjectManager $em, 
        AuthorizationChecker $authChecker, 
        TokenStorageInterface $token, 
        ParameterBagInterface $params, 
        MessageHandler $messageHandler
    )
    {
        $this->_em = $em;
        $this->_authChecker = $authChecker;
        $this->_tokenStorage = $token;
        $this->_params = $params;
        $this->_messageHandler = $messageHandler;
    }
    
    public function afterUpload(PostUploadEvent $event)
    {
        $user = $this->_tokenStorage->getToken()->getUser();
        $file = $event->getRequest()->files->get('qqfile');
        $path = '/' . $this->_params->get('storage_dir') . '/' . $event->getFile()->getPathname();
        $filename = $file->getClientOriginalName();
        $mimeType = $event->getFile()->getMimeType();

        $mimeTypeBBC = '';
        switch($mimeType)
        {
            // Pictures
            case 'image/jpeg':
            case 'image/png': 
            case 'image/gif':
            case 'image/jpeg': 
            case 'image/svg+xml':
            case 'image/webp':
            {
                $mimeTypeBBC = '[img]' . $path . '[/img]';
                break;
            }
            
            // Audio
            case 'audio/mp3': 
            case 'audio/mpeg': 
            {
                $mimeTypeBBC = '[audio]' . $path . '[/audio]';
                break;
            }

            // Video
            case 'video/mp4': 
            {
                $mimeTypeBBC = '[video]' . $path . '[/video]';
                break;
            }

            // Other, files:
            default:
            {
                $mimeTypeBBC = '[file]' . $path . '[/file]';
                break;
            }
        }

        $filesize = $event->getRequest()->server->get('CONTENT_LENGTH');
        
        $file = new File();
        $file->setName($filename);
        $file->setPath($path);
        $file->setMimeType($mimeType);
        $file->setFileSize($filesize);

        $this->_em->persist($file);
        $this->_em->flush();

        // Create new Message!

        $this->_messageHandler->insertMessage($mimeTypeBBC, $this->_conversation, array(
            'files' => [
                $file
            ],
            'createdBy' => $user,
            'ignoreXss' => true
        ));
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