<?php
namespace App\Controllers;

use App\Models\M_admin;

class Admin extends BaseController
{
    protected function requireLogin(string $flashKey = 'error', string $message = 'Silakan login terlebih dahulu!', string $redirect = 'admin/login-admin')
    {
        if (session()->get('ses_id') == '' || session()->get('ses_user') == '' || session()->get('ses_level') == '') {
            session()->setFlashdata($flashKey, $message);
            echo '<script>document.location = "' . base_url($redirect) . '";</script>';
            exit;
        }
    }

    public function input_data_admin()
    {
        $this->requireLogin('Error', 'Anda Harus Login Terlebih Dahulu');
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/MasterAdmin/input-admin');
        echo view('Backend/Template/footer');
    }

    public function simpan_data_admin()
    {
        $this->requireLogin('Error', 'Anda Harus Login Terlebih Dahulu');

        $modelAdmin = new M_admin();
        $nama = $this->request->getPost('nama');
        $username = $this->request->getPost('username');
        $level = $this->request->getPost('level');

        $cekUsername = $modelAdmin->getDataAdmin(['username_admin' => $username])->getNumRows();
        if ($cekUsername > 0) {
            session()->setFlashdata('Error', 'Username Sudah Digunakan');
            echo '<script>history.go(-1);</script>';
            return;
        }

        $hasil = $modelAdmin->autoNumber()->getRowArray();
        if (!$hasil) {
            $id = 'ADM001';
        } else {
            $kode = $hasil['id_admin'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = 'ADM' . sprintf('%03s', $noUrut);
        }

        $dataSimpan = [
            'id_admin' => $id,
            'nama_admin' => $nama,
            'username_admin' => $username,
            'password_admin' => password_hash('pass_admin', PASSWORD_DEFAULT),
            'akses_level' => $level,
            'is_delete_admin' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'update_at' => date('Y-m-d H:i:s')
        ];
        $modelAdmin->simpanDataAdmin($dataSimpan);
        session()->setFlashdata('Success', 'Data Admin Berhasil Ditambahkan');
        echo '<script>document.location.href = "' . base_url('admin/master-data-admin') . '";</script>';
    }

    public function master_data_admin()
    {
        $this->requireLogin('error', 'Silakan login terlebih dahulu!');

        $modelAdmin = new M_admin();
        $uri = service('uri');
        $pages = $uri->getSegment(2);
        $dataUser = $modelAdmin->getDataAdmin(['is_delete_admin' => '0', 'akses_level !=' => '1'])->getResultArray();

        $data['pages'] = $pages;
        $data['data_user'] = $dataUser;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterAdmin/master-data-admin', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function edit_data_admin()
    {
        $this->requireLogin('error', 'Silakan login terlebih dahulu!');

        $modelAdmin = new M_admin();
        $uri = service('uri');
        $idEdit = $uri->getSegment(3);

        $dataAdmin = $modelAdmin->getDataAdmin(['sha1(id_admin)' => $idEdit])->getRowArray();
        session()->set(['idUpdate' => $dataAdmin['id_admin']]);

        $page = $uri->getSegment(2);

        $data['page'] = $page;
        $data['web_title'] = 'Edit Data Admin';
        $data['data_admin'] = $dataAdmin;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterAdmin/edit-admin', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function update_data_admin()
    {
        $this->requireLogin('error', 'Silakan login terlebih dahulu!');

        $modelAdmin = new M_admin();

        $nama = $this->request->getPost('nama');
        $level = $this->request->getPost('level');

        if ($nama == '' or $level == '') {
            session()->setFlashdata('error', 'Isian tidak boleh kosong!!');
            echo '<script>history.go(-1);</script>';
            return;
        }

        $dataUpdate = [
            'nama_admin' => $nama,
            'akses_level' => $level,
            'update_at' => date('Y-m-d H:i:s')
        ];
        $whereUpdate = ['id_admin' => session()->get('idUpdate')];

        $modelAdmin->updateDataAdmin($dataUpdate, $whereUpdate);
        session()->remove('idUpdate');
        session()->setFlashdata('success', 'Data Admin Berhasil Diperbaharui!');
        echo '<script>document.location = "' . base_url('admin/master-data-admin') . '";</script>';
    }

    public function hapus_data_admin()
    {
        $this->requireLogin('error', 'Silakan login terlebih dahulu!');

        $modelAdmin = new M_admin();
        $uri = service('uri');
        $idHapus = $uri->getSegment(3);

        $dataUpdate = ['is_delete_admin' => '1', 'update_at' => date('Y-m-d H:i:s')];
        $whereUpdate = ['sha1(id_admin)' => $idHapus];

        $modelAdmin->updateDataAdmin($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Admin Berhasil Dihapus!');
        echo '<script>document.location = "' . base_url('admin/master-data-admin') . '";</script>';
    }
}