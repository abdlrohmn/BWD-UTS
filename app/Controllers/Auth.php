<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends Controller
{
    public function index(): string
    {
        // Tampilkan halaman login
        return view('login_view');
    }

    public function process(): ResponseInterface
    {
        try {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            if ($this->validateCredentials($username, $password)) {
                session()->set('isLoggedIn', true);
                session()->set('username', $username);
                session()->regenerate(); // Security: regenerate session ID
                return redirect()->to('/dashboard')->with('success', 'Login berhasil!');
            } else {
                session()->setFlashdata('error', 'Login Gagal!');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem!');
            return redirect()->back();
        }
    }

    public function logout(): ResponseInterface
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Logout berhasil!');
    }

    private function validateCredentials(string $username, string $password): bool
    {
        // Dummy validation - in real app, check against database
        return $username === 'admin' && $password === 'bisnis123';
    }
}