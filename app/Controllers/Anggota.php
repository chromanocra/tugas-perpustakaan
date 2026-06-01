<?php
namespace App\Controllers;

use App\Models\M_Anggota;

class Anggota extends BaseController
{
    protected function requireLogin()
    {
        if (session()->get('ses_id') == '' || session()->get('ses_user') == '' || session()->get('ses_level') == '') {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            echo '<script>document.location = "' . base_url('admin/login-admin') . '";</script>';
            exit;
        }
    }

    public function master_data_anggota()
    {
        $this->requireLogin();
        $modelAnggota = new M_Anggota();
        $uri = service('uri');
        $pages = $uri->getSegment(2);
        $dataAnggota = $modelAnggota->getDataAnggota(['is_delete_anggota' => '0'])->getResultArray();

        $data['pages'] = $pages;
        $data['data_anggota'] = $dataAnggota;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterAnggota/master-data-anggota', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function input_data_anggota()
    {
        $this->requireLogin();
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/MasterAnggota/input-anggota');
        echo view('Backend/Template/footer');
    }

    public function simpan_data_anggota()
    {
        $this->requireLogin();
        $modelAnggota = new M_Anggota();
        $nama = $this->request->getPost('nama');
        $jk = $this->request->getPost('jenis_kelamin');
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $email = $this->request->getPost('email');

        $hasil = $modelAnggota->autoNumber()->getRowArray();
        if (!$hasil) {
            $id = 'AGT001';
        } else {
            $kode = $hasil['id_anggota'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = 'AGT' . sprintf('%03s', $noUrut);
        }

        $dataSimpan = [
            'id_anggota' => $id,
            'nama_anggota' => $nama,
            'jenis_kelamin' => $jk,
            'no_telp' => $no_telp,
            'alamat' => $alamat,
            'email' => $email,
            'password_anggota' => password_hash('pass_anggota', PASSWORD_DEFAULT),
            'is_delete_anggota' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $modelAnggota->simpanDataAnggota($dataSimpan);
        session()->setFlashdata('success', 'Data Anggota Berhasil Ditambahkan!');
        echo '<script>document.location = "' . base_url('admin/master-data-anggota') . '";</script>';
    }

    public function edit_data_anggota()
    {
        $this->requireLogin();
        $modelAnggota = new M_Anggota();
        $uri = service('uri');
        $idEdit = $uri->getSegment(3);

        $dataAnggota = $modelAnggota->getDataAnggota(['sha1(id_anggota)' => $idEdit])->getRowArray();
        session()->set(['idUpdateAnggota' => $dataAnggota['id_anggota']]);

        $data['data_anggota'] = $dataAnggota;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterAnggota/edit-anggota', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function update_data_anggota()
    {
        $this->requireLogin();
        $modelAnggota = new M_Anggota();
        $nama = $this->request->getPost('nama');
        $jk = $this->request->getPost('jenis_kelamin');
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $email = $this->request->getPost('email');

        $dataUpdate = [
            'nama_anggota' => $nama,
            'jenis_kelamin' => $jk,
            'no_telp' => $no_telp,
            'alamat' => $alamat,
            'email' => $email,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $whereUpdate = ['id_anggota' => session()->get('idUpdateAnggota')];
        $modelAnggota->updateDataAnggota($dataUpdate, $whereUpdate);
        session()->remove('idUpdateAnggota');
        session()->setFlashdata('success', 'Data Anggota Berhasil Diperbaharui!');
        echo '<script>document.location = "' . base_url('admin/master-data-anggota') . '";</script>';
    }

    public function hapus_data_anggota()
    {
        $this->requireLogin();
        $modelAnggota = new M_Anggota();
        $uri = service('uri');
        $idHapus = $uri->getSegment(3);

        $dataUpdate = ['is_delete_anggota' => '1', 'updated_at' => date('Y-m-d H:i:s')];
        $whereUpdate = ['sha1(id_anggota)' => $idHapus];
        $modelAnggota->updateDataAnggota($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Anggota Berhasil Dihapus!');
        echo '<script>document.location = "' . base_url('admin/master-data-anggota') . '";</script>';
    }
}
