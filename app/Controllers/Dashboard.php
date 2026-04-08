<?php
namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index(): ResponseInterface|string
    {
        try {
            $data = $this->prepareDashboardData();
            return view('dashboard_view', $data);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            return $this->showErrorPage();
        }
    }

    private function prepareDashboardData(): array
    {
        return [
            'nama_startup' => "Kepul Lite",
            'products' => [] // Data handled by JavaScript/Local Storage
        ];
    }

    private function showErrorPage(): string
    {
        return view('errors/html/error_500', ['message' => 'Terjadi kesalahan pada sistem.']);
    }
}