<?php
namespace App\Controllers;

use App\Models\M_admin;

class Auth extends BaseController
{
    protected function backWithError(string $message)
    {
        session()->setFlashdata('Error', $message);
        echo '<script>history.go(-1);</script>';
        exit;
    }

    public function login()
    {
        return view('backend/login/login');
    }

    public function autentikasi()
    {
        $modelAdmin = new M_admin();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $cekUsername = $modelAdmin->getDataAdmin(['username_admin' => $username, 'is_delete_admin' => '0'])->getNumRows();
        if ($cekUsername == 0) {
            $this->backWithError('Username Tidak Ditemukan');
        }

        $dataUser = $modelAdmin->getDataAdmin(['username_admin' => $username, 'is_delete_admin' => '0'])->getRowArray();
        $passwordUser = $dataUser['password_admin'];

        if (!password_verify($password, $passwordUser)) {
            $this->backWithError('Password Tidak Sesuai');
        }

        $dataSesion = [
            'ses_id' => $dataUser['id_admin'],
            'ses_user' => $dataUser['nama_admin'],
            'ses_level' => $dataUser['akses_level']
        ];
        session()->set($dataSesion);
        session()->setFlashdata('Success', 'Login Berhasil');
        echo '<script>document.location.href = "' . base_url('admin/dashboard-admin') . '";</script>';
    }

    public function logout()
    {
        session()->remove('ses_id');
        session()->remove('ses_user');
        session()->remove('ses_level');
        session()->setFlashdata('info', 'Anda Telah Keluar dari Sistem!');
        echo '<script>document.location.href = "' . base_url('admin/login-admin') . '";</script>';
    }
}
