<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class Product extends Model
{
    public ?string $title;
    public ?string $color;
    public ?string $size;
    public ?int $quantity;
    public ?float $price;
    public ?string $trackingNumber;
    public ?bool $productCode = false;

    /**
     * @param string $body
     * @return Product[]
     */
    public function getMainProductsData(string $body): array
    {
        $rawProductData = $this->getRawProductsData($body);
        $products = [];
        foreach ($rawProductData as $datum) {
            $products[] = $this->assemble($datum);
        }

        return $products;
    }

    /**
     * @param string $subject
     * @return Product
     */
    private function assemble(string $subject): Product
    {
        $pattern = '/<img[^>]+products[^>]+alt=\"([^>]+)\"[^>]*>.*?\$(\d{1,5}\.\d{2}).*?>([\w|\s]+)\/([\s|\w|\d|\.|\/|-]*)\/\sQty\s(\d+)/';
        $rawProductPart = [];
        preg_match_all($pattern, $subject, $rawProductPart);

        $product = new self;
        $product->title = trim($rawProductPart[1][0]);
        $product->color = trim($rawProductPart[3][0]);
        $product->size = trim($rawProductPart[4][0]);
        $product->quantity = (int) $rawProductPart[5][0];
        $product->price = (float) $rawProductPart[2][0];

        return $product;
    }

    /**
     * @param string $subject
     * @return string[]
     */
    private function getRawProductsData(string $subject): array
    {
        $pattern = '/<img[^>]+products[^>]+alt=\"[^>]+\"[^>]*>.*?\$\d{1,5}\.\d{2}.*?<\/table>/';
        $rawProducts = [];
        preg_match_all($pattern, $subject,$rawProducts);

        return $rawProducts[0];
    }
}