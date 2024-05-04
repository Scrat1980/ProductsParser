<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class TrackingNumberGroup extends Model
{
    public ?string $trackingNumber;
    public ?int $itemsInParcel;
    public function getTrackNumberGroups($subject): array
    {
        $pattern = '/(\d+)\sitem.*?<a[^>]+href=\"([^>]+?)\"[^>]*> Track parcel <\/a>/';
        preg_match_all($pattern, $subject, $numbers);
        $groups = [];
        $urlManager = new Url();

        foreach ($numbers[1] as $i => $quantity) {
            foreach ($numbers[2] as $j => $url) {
                if ($i == $j)
                {
                    $tng = new self;
                    $tng->itemsInParcel = (int) $quantity;
                    $tng->trackingNumber = $this->extractTrackNumber(
                        $urlManager->getRedirectUrl(
                            $url
                        )
                    );
                    $groups[] = $tng;
                }
            }
        }

        return $groups;
    }
    private function extractTrackNumber($subject): string
    {
        $pattern = '/tracknum=(.*)/';
        $result = [];
        preg_match($pattern, $subject, $result);

        return $result[1];
    }

}