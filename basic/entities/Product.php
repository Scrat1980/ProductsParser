<?php

declare(strict_types=1);

namespace app\entities;

class Product
{
    public ?string $title;
    public ?string $color;
    public ?string $size;
    public ?int $quantity;
    public ?float $price;
    public ?string $trackingNumber;
    public ?bool $productCode = false;
}