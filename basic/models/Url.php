<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class Url extends Model
{
    public function getRedirectUrl(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $redirectUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        return $redirectUrl;
    }
}