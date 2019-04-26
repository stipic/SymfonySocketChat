<?php
namespace App\EventListener;

use App\Entity\Message;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MessageParserListener 
{
    private $_parser;
    
    private $_twig;

    private $_fileInfo;

    public function __construct(TwigEngine $twig)
    {
        $this->_parser = new \JBBCode\Parser();
        $this->_twig = $twig;
    }

    public function parse(string $content, array $fileInfo) : string
    {
        $this->_fileInfo = $fileInfo;

        $content = $this->parseUrls($content); // ovo drzati na vrhu, posto parsa i kreira mozda bbc koji ce onda multimedia dodatno sparsati
        
        $content = $this->parseMultimedia($content);

        return $content;
    }

    public function parseUrls(string &$content)
    {
        // extract ALL urls from string.
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $content, $urls);

        $urls = isset($urls[0]) ? $urls[0] : [];
        if(!empty($urls))
        {
            $essence = new \Essence\Essence();

            //@todo keširanje već pasiranog bi bilo super.

            foreach($urls as $singleUrl)
            {
                $aHref = '<a href="' . $singleUrl . '" target="_blank">' . $singleUrl . '</a>';
                $content = str_replace($singleUrl, $aHref, $content);
                $media = $essence->extract($singleUrl);

                if($media)
                {
                    $provider = $media->provider_url;
                    $thumb = isset($media->thumbnailUrl) ? $media->thumbnailUrl : $media->thumbnail_url;

                    if(isset($media->html))
                    {
                        $content .= '<br />' . $media->html;
                    }
                    else 
                    {
                        $content .= '<br />' . $thumb;
                    }
                }
                else 
                {
                    // ajmo dohvatiti barem osnovno sto nam HTTP header-i nude.
                    $tags = get_meta_tags($singleUrl);
                    if(!empty($tags))
                    {
                        $template = $this->_twig->render('message-blocks/base-url.inc.html.twig', $tags);
                        $content .= '<br />' . $template;
                    }
                }
            }
        }
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
        $template = $this->_twig->render('message-blocks/image.inc.html.twig', ['file' => $this->_fileInfo]);
        $builder = new \JBBCode\CodeDefinitionBuilder('img', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    public function parseAudio(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/audio.inc.html.twig', ['file' => $this->_fileInfo]);
        $builder = new \JBBCode\CodeDefinitionBuilder('audio', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    public function parseVideo(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/video.inc.html.twig', ['file' => $this->_fileInfo]);
        $builder = new \JBBCode\CodeDefinitionBuilder('video', $template);
        $this->_parser->addCodeDefinition($builder->build());
        
        $this->_parser->parse($content);

        return $this->_parser->getAsHtml();
    }

    public function parseFile(string $content) : string
    {
        $template = $this->_twig->render('message-blocks/file.inc.html.twig', ['file' => $this->_fileInfo]);
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
                method_exists($entity, 'setParsedContent')) 
            {
                $file = [
                    'name' => '',
                    'size' => ''
                ];

                if($entity->getFile() != NULL)
                {
                    $file = [
                        'name' => $entity->getFile()->getName(),
                        'size' => $entity->getFile()->getFileSize()
                    ];
                }

                $parsedContent = $this->parse($entity->getContent(), $file);
                $entity->setParsedContent($parsedContent);
                
                //@todo ajmo oznaciti da je ova poruka parsirana i da je ne parsiramo uvijek iznova?
            }
        }
    }
}