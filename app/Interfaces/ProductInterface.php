<?php
namespace App\Interfaces;

interface ProductInterface
{
    public function getDummyData(): array;
    public function isStockAvailable(int $productId, int $quantity): bool;
    public function reduceStock(int $productId, int $quantity): bool;
}