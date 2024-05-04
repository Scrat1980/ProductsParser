<?php

namespace app\models;

use app\entities\Order;
use app\entities\Product;
use app\entities\TrackingNumberGroup;
use PhpMimeMailParser\Parser as MimeParser;
use Yii;
use yii\base\Model;

class Parser extends Model
{
    public function parse(): array
    {
        $order = new Order();
        $body = $this->getBody();
        $order->orderNumber = $this->extractOrderNumber($body);
        $order->products = $this->getMainProductsData($body);
        $this->addTrackingNumbers($order->products, $body);
        $dto = $this->transform($order);

        return $dto;
    }
    private function transform(Order $order): array
    {
        $dto = [
            'order_number' => $order->orderNumber,
            'products' => [],
        ];

        foreach ($order->products as $product) {
            $dto['products'][] = [
                'title' => $product->title,
                'color' => $product->color,
                'size' => $product->size,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'tracking_number' => $product->trackingNumber,
                'productCode' => $product->productCode,
            ];
        }

        return $dto;
    }
    private function extractOrderNumber($subject): string
    {
        $pattern = '/Order No.:\s(\d+)/';
        preg_match($pattern, $subject,$orderNumberContainer);

        return $orderNumberContainer[1];
    }
    private function addTrackingNumbers($products, $body): void
    {
        $trackingNumberGroups = $this->getTrackNumberGroups($body);

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
    private function extractTrackNumber($subject): string
    {
        $pattern = '/tracknum=(.*)/';
        $result = [];
        preg_match($pattern, $subject, $result);

        return $result[1];
    }
    private function getTrackNumberGroups($subject): array
    {
        $pattern = '/(\d+)\sitem.*?<a[^>]+href=\"([^>]+?)\"[^>]*> Track parcel <\/a>/';
//        $pattern = '/(\d+)\sitem.*?href=\"(.*?)\".*?Track parcel/';
//        $pattern = '/(\d+)\sitem.*?Track parcel.*?href=\"(.*?)\"/';
        preg_match_all($pattern, $subject, $numbers);
        $groups = [];

        foreach ($numbers[1] as $i => $quantity) {
            foreach ($numbers[2] as $j => $url) {
                if ($i == $j)
                {
                    $tng = new TrackingNumberGroup();
                    $tng->itemsInParcel = (int) $quantity;
                    $tng->trackingNumber = $this->extractTrackNumber($this->getRedirectUrl($url));
                    $groups[] = $tng;
                }
            }
        }

        return $groups;
    }
    private function getRedirectUrl(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }

    /**
     * @param string $body
     * @return Product[]
     */
    private function getMainProductsData(string $body): array
    {
        $finalPattern4 = '<img[^>]+products[^>]+alt=\"[^>]+\"[^>]*>.*?\$\d{1,5}\.\d{2}.*?<\/table>';
        $matches = [];
        preg_match_all('/'. $finalPattern4 .'/', $body,$matches);

        $innerPattern3 = '<img[^>]+products[^>]+alt=\"([^>]+)\"[^>]*>.*?\$(\d{1,5}\.\d{2}).*?>([\w|\s]+)\/([\s|\w|\d|\.|\/|-]*)\/\sQty\s(\d+)';
        $products = [];
        $rawProductPart = [];
        foreach ($matches[0] as $i => $match) {
            preg_match_all('/' . $innerPattern3 . '/', $match, $rawProductPart);
            $product = new Product();
            $product->title = trim($rawProductPart[1][0]);
            $product->color = trim($rawProductPart[3][0]);
            $product->size = trim($rawProductPart[4][0]);
            $product->quantity = (int) $rawProductPart[5][0];
            $product->price = (float) $rawProductPart[2][0];
            $products[] = $product;
        }

        return $products;
    }
    private function getBody(): string
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