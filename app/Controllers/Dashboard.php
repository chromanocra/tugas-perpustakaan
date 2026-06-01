<?php
namespace App\Controllers;

class Dashboard extends BaseController
{
    protected function requireLogin(string $flashKey = 'Error', string $message = 'Anda Harus Login Terlebih Dahulu', string $redirect = 'admin/login-admin')
    {
        if (session()->get('ses_id') == '' || session()->get('ses_level') == '') {
            session()->setFlashdata($flashKey, $message);
            echo '<script>document.location.href = "' . base_url($redirect) . '";</script>';
            exit;
        }
    }

    public function dashboard()
    {
        $this->requireLogin();
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/Login/dasboard_admin');
        echo view('Backend/Template/footer');
    }
}
