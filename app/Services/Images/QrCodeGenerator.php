<?php

namespace App\Services\Images;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

class QrCodeGenerator
{
    public function __construct(protected ImageManager $manager)
    {
    }

    public function make(string $data, int $size = 240): ImageInterface
    {
        $builder = new Builder(writer: new PngWriter());
        $result = $builder->build(data: $data, size: $size, margin: 8);

        return $this->manager->read($result->getString());
    }
}
