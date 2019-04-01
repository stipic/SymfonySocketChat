<?php
namespace App\EventListener;

use App\Entity\Message;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MessageParserListener 
{
    private $_parser;
    
    private $_twig;

    public function __construct(TwigEngine $twig)
    {
        $this->_parser = new \JBBCode\Parser();
        $this->_twig = $twig;
    }

    public function parse(string $content) : string
    {
        $content = $this->parseMultimedia($content);

        return $content;
    }

    public function parseMultimedia(string $content) : string 
    {
        $content = $this->parseImage($content);
        $content = $this->parseAudio($content);
        $content = $this->parseVideo($content);
        $content = $this->parseFile($content);
        return $content;
    }

    public function parseImage(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/image.inc.html.twig', []);
        $builder = new \JBBCode\CodeDefinitionBuilder('img', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    public function parseAudio(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/audio.inc.html.twig', []);
        $builder = new \JBBCode\CodeDefinitionBuilder('audio', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    public function parseVideo(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/video.inc.html.twig', []);
        $builder = new \JBBCode\CodeDefinitionBuilder('video', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    public function parseFile(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/file.inc.html.twig', []);
        $builder = new \JBBCode\CodeDefinitionBuilder('file', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if($entity instanceof Message)
        {
            if(
                method_exists($entity, 'getContent') &&
                method_exists($entity, 'setContent')) 
            {
                //@todo napraviti relaciju message i files entiteta kako bi mogao dohvatiti informacije o uploadanom file-u
                $entity->setContent($this->parse($entity->getContent()));
            }
        }
    }
}