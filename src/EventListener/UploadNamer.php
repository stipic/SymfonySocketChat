<?php
namespace App\EventListener;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class UploadNamer implements NamerInterface
{   
    public function name(FileInterface $file)
    {
        $uniqHash = time() . rand(0,1000000);
        $filename = $uniqHash . '_' . $file->getClientOriginalName();
        return date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
    }
}