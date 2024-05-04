<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class Order extends Model
{
    public ?string $number;
    public ?array $products;
    public function parse(): array
    {
        $order = new self;
        $body = (new EmailParser())->getBody();
        $order->number = $this->extractOrderNumber($body);
        $order->products = (new Product())->getMainProductsData($body);
        (new TrackingNumber())->addTrackingNumbers($order->products, $body);
        $dto = $this->transform($order);

        return $dto;
    }
    private function transform(Order $order): array
    {
        $dto = [
            'order_number' => $order->number,
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
}