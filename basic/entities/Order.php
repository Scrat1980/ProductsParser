<?php

declare(strict_types=1);

namespace app\entities;

class Order
{
    public ?string $orderNumber;
    public ?array $products;
}