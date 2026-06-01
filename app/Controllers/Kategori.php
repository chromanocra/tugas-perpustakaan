<?php
namespace App\Controllers;

use App\Models\M_Kategori;

class Kategori extends BaseController
{
    protected function requireLogin()
    {
        if (session()->get('ses_id') == '' || session()->get('ses_user') == '' || session()->get('ses_level') == '') {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            echo '<script>document.location = "' . base_url('admin/login-admin') . '";</script>';
            exit;
        }
    }

    public function master_data_kategori()
    {
        $this->requireLogin();
        $modelKategori = new M_Kategori();
        $uri = service('uri');
        $pages = $uri->getSegment(2);
        $dataKategori = $modelKategori->getDataKategori(['id_delete_kategori' => '0'])->getResultArray();

        $data['pages'] = $pages;
        $data['data_kategori'] = $dataKategori;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterKategori/master-data-kategori', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function input_data_kategori()
    {
        $this->requireLogin();
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/MasterKategori/input-kategori');
        echo view('Backend/Template/footer');
    }

    public function simpan_data_kategori()
    {
        $this->requireLogin();
        $modelKategori = new M_Kategori();
        $nama_kategori = $this->request->getPost('nama_kategori');

        $hasil = $modelKategori->autoNumber()->getRowArray();
        if (!$hasil) {
            $id = 'KTG001';
        } else {
            $kode = $hasil['id_kategori'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = 'KTG' . sprintf('%03s', $noUrut);
        }

        $dataSimpan = [
            'id_kategori' => $id,
            'nama_kategori' => $nama_kategori,
            'id_delete_kategori' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $modelKategori->simpanDataKategori($dataSimpan);
        session()->setFlashdata('success', 'Data Kategori Berhasil Ditambahkan!');
        echo '<script>document.location = "' . base_url('admin/master-data-kategori') . '";</script>';
    }

    public function edit_data_kategori()
    {
        $this->requireLogin();
        $modelKategori = new M_Kategori();
        $uri = service('uri');
        $idEdit = $uri->getSegment(3);

        $dataKategori = $modelKategori->getDataKategori(['sha1(id_kategori)' => $idEdit])->getRowArray();
        session()->set(['idUpdateKategori' => $dataKategori['id_kategori']]);

        $data['data_kategori'] = $dataKategori;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterKategori/edit-kategori', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function update_data_kategori()
    {
        $this->requireLogin();
        $modelKategori = new M_Kategori();
        $nama_kategori = $this->request->getPost('nama_kategori');

        $dataUpdate = [
            'nama_kategori' => $nama_kategori,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $whereUpdate = ['id_kategori' => session()->get('idUpdateKategori')];
        $modelKategori->updateDataKategori($dataUpdate, $whereUpdate);
        session()->remove('idUpdateKategori');
        session()->setFlashdata('success', 'Data Kategori Berhasil Diperbaharui!');
        echo '<script>document.location = "' . base_url('admin/master-data-kategori') . '";</script>';
    }

    public function hapus_data_kategori()
    {
        $this->requireLogin();
        $modelKategori = new M_Kategori();
        $uri = service('uri');
        $idHapus = $uri->getSegment(3);

        $dataUpdate = ['id_delete_kategori' => '1', 'updated_at' => date('Y-m-d H:i:s')];
        $whereUpdate = ['sha1(id_kategori)' => $idHapus];
        $modelKategori->updateDataKategori($dataUpdate, $whereUpdate);
        session()->setFlashdata('success', 'Data Kategori Berhasil Dihapus!');
        echo '<script>document.location = "' . base_url('admin/master-data-kategori') . '";</script>';
    }
}
