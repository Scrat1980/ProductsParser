<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class TrackingNumber extends Model
{
    public function addTrackingNumbers($products, $body): void
    {
        $trackingNumberGroups = (new TrackingNumberGroup())->getTrackNumberGroups($body);

        $i = 0;
        foreach ($trackingNumberGroups as $trackingNumberGroup)
        {
            for ($j = 1; $j <= $trackingNumberGroup->itemsInParcel; $j++)
            {
                $products[$i]->trackingNumber =
                    $trackingNumberGroup->trackingNumber;
                $i++;
            }
        }
    }

}