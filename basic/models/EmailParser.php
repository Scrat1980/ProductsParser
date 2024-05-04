<?php

declare(strict_types=1);

namespace app\models;

use PhpMimeMailParser\Parser as MimeParser;
use Yii;
use yii\base\Model;

class EmailParser extends Model
{
    public function getBody(): string
    {
        /**
         * @var string $path Path to source email file
         */
        $path = '../../asos.eml';
        $parser = new MimeParser();
        $parser->setStream(fopen($path, "r"));

        return $parser->getMessageBody('htmlEmbedded');
    }
}