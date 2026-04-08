<?php
namespace App\Models;

use App\Interfaces\ProductInterface;
use CodeIgniter\Model;

class ProductModel extends Model implements ProductInterface
{
    protected $table = 'products'; // For future database integration
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'price', 'stock'];

    // Karena belum pakai SQL, kita simpan data dummy di sini (sebagai Gudang Data)
    // TODO: TUGAS MAHASISWA!
    // Ubah struktur data menjadi data yang sesuai dengan produk startup kalian
    public function getDummyData(): array
    {
        return [
            ['id' => 1, 'name' => 'Plastik PET', 'price' => 2000, 'stock' => 50],
            ['id' => 2, 'name' => 'Kardus', 'price' => 1500, 'stock' => 30],
            ['id' => 3, 'name' => 'Botol Kaca', 'price' => 500, 'stock' => 20],
        ];
    }

    public function isStockAvailable(int $productId, int $quantity): bool
    {
        $products = $this->getDummyData();
        foreach ($products as $product) {
            if ($product['id'] === $productId) {
                return $product['stock'] >= $quantity;
            }
        }
        return false;
    }

    public function reduceStock(int $productId, int $quantity): bool
    {
        // In dummy implementation, this would modify the array
        // In real DB, use update query
        return true; // Placeholder
    }
}