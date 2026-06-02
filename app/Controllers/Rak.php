<?php
namespace App\Controllers;

use App\Models\M_Rak;

class Rak extends BaseController
{
    protected function requireLogin()
    {
        if (session()->get('ses_id') == '' || session()->get('ses_user') == '' || session()->get('ses_level') == '') {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            echo '<script>document.location = "' . base_url('admin/login-admin') . '";</script>';
            exit;
        }
    }

    public function master_data_rak()
    {
        $this->requireLogin();
        $modelRak = new M_Rak();
        $uri = service('uri');
        $pages = $uri->getSegment(2);
        $dataRak = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();

        $data['pages'] = $pages;
        $data['data_rak'] = $dataRak;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterRak/master-data-rak', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function input_data_rak()
    {
        $data = [ 
            'web_title' => 'Input Data Rak' 
        ]; 
        $this->requireLogin();
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterRak/input-rak', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function simpan_data_rak()
    {
        $this->requireLogin();

        $modelRak = new M_Rak();
        $nama_rak = $this->request->getPost('nama_rak');

        $hasil = $modelRak->autoNumber()->getRowArray();
        if (!$hasil) {
            $id = 'RAK001';
        } else {
            $kode = $hasil['id_rak'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = 'RAK' . sprintf('%03s', $noUrut);
        }

        $dataSimpan = [
            'id_rak' => $id,
            'nama_rak' => $nama_rak,
            'is_delete_rak' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $modelRak->simpanDataRak($dataSimpan);

        session()->setFlashdata('success', 'Data Rak Berhasil Ditambahkan!');
        echo '<script>document.location = "' . base_url('admin/master-data-rak') . '";</script>';
    }

    public function edit_data_rak()
    {
        $this->requireLogin();

        $modelRak = new M_Rak();

        $uri = service('uri');
        $idEdit = $uri->getSegment(3);

        $dataRak = $modelRak->getDataRak(['sha1(id_rak)' => $idEdit])->getRowArray();
        session()->set(['idUpdateRak' => $dataRak['id_rak']]);
        $data['data_rak'] = $dataRak;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterRak/edit-rak', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function update_data_rak()
    {
        $this->requireLogin();

        $modelRak = new M_Rak();
        $nama_rak = $this->request->getPost('nama_rak');

        $dataUpdate = [
            'nama_rak' => $nama_rak,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $whereUpdate = ['id_rak' => session()->get('idUpdateRak')];
        $modelRak->updateDataRak($dataUpdate, $whereUpdate);
        session()->remove('idUpdateRak');
        session()->setFlashdata('success', 'Data Rak Berhasil Diperbaharui!');
        echo '<script>document.location = "' . base_url('admin/master-data-rak') . '";</script>';
    }

    public function hapus_data_rak()
    {
        // Validasi login
        $this->requireLogin();

        $modelRak = new M_Rak();
        $uri = service('uri');
        $idHapus = $uri->getSegment(3); // Ini berisi hash SHA1 dari id_rak

        //Cek data rak ada atau tidak berdasarkan SHA1 ID dari URL
        $dataRak = $modelRak->getDataRak(['sha1(id_rak)' => $idHapus])->getRowArray(); 

        // Validasi jika data rak tidak ditemukan
        if (!$dataRak) { 
            session()->setFlashdata('error', 'Data tidak ditemukan!'); 
            echo '<script>document.location = "' . base_url('admin/master-data-rak') . '";</script>';
            exit;
        } 

        $id = $dataRak['id_rak'];

        //Panggil koneksi database CI4 untuk Query Builder tbl_buku
        $db = \Config\Database::connect();

        // Validasi cek data rak sedang digunakan oleh buku atau tidak
        $cek = $db->table('tbl_buku') 
            ->where('id_rak', $id) 
            ->where('is_delete_buku', '0') 
            ->countAllResults(); 

        if ($cek > 0) { 
            session()->setFlashdata('error', 'Rak sedang digunakan oleh buku!'); 
            echo '<script>document.location = "' . base_url('admin/master-data-rak') . '";</script>';
            exit;
        } 

        //Proses Hapus Permanen (menggunakan ke primary key asli)
        $modelRak->where('id_rak', $id)->delete(); 

        session()->setFlashdata('success', 'Data rak berhasil dihapus permanen!'); 
        echo '<script>document.location = "' . base_url('admin/master-data-rak') . '";</script>';
        exit;
    } 
}