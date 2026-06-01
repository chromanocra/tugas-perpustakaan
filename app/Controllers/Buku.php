<?php
namespace App\Controllers;

use App\Models\M_Buku;
use App\Models\M_Kategori;
use App\Models\M_Rak;

class Buku extends BaseController
{
    protected function requireLogin()
    {
        if (session()->get('ses_id') == '' || session()->get('ses_user') == '' || session()->get('ses_level') == '') {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            echo '<script>document.location = "' . base_url('admin/login-admin') . '";</script>';
            exit;
        }
    }

    public function master_buku()
    {
        $this->requireLogin();
        $modelBuku = new M_Buku();
        $uri = service('uri');
        $pages = $uri->getSegment(2);

        $dataBuku = $modelBuku->getDataBukuJoin(['tbl_buku.is_delete_buku' => '0'])->getResultArray();

        $data['pages'] = $pages;
        $data['data_buku'] = $dataBuku;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterBuku/master-data-buku', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function input_buku()
    {
        $this->requireLogin();
        $modelKategori = new M_Kategori();
        $modelRak = new M_Rak();

        $data['data_kategori'] = $modelKategori->getDataKategori(['id_delete_kategori' => '0'])->getResultArray();
        $data['data_rak'] = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterBuku/input-buku', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function simpan_buku()
    {
        $this->requireLogin();
        $modelBuku = new M_Buku();
        $judul_buku = $this->request->getPost('judul_buku');
        $pengarang = $this->request->getPost('pengarang');
        $penerbit = $this->request->getPost('penerbit');
        $tahun = $this->request->getPost('tahun');
        $jumlah_eksemplar = $this->request->getPost('jumlah_eksemplar');
        $id_kategori = $this->request->getPost('id_kategori');
        $keterangan = $this->request->getPost('keterangan');
        $id_rak = $this->request->getPost('id_rak');

        $rules = [
            'cover_buku' => [
                'rules' => 'uploaded[cover_buku]|max_size[cover_buku,1024]|ext_in[cover_buku,jpg,jpeg,png]',
                'errors' => [
                    'uploaded' => 'Cover buku wajib diunggah.',
                    'max_size' => 'Ukuran cover maksimal 1 MB.',
                    'ext_in' => 'Format file cover harus jpg, jpeg, atau png.'
                ]
            ],
            'e_book' => [
                'rules' => 'uploaded[e_book]|max_size[e_book,10240]|ext_in[e_book,pdf]',
                'errors' => [
                    'uploaded' => 'File e-book wajib diunggah.',
                    'max_size' => 'Ukuran e-book maksimal 10 MB.',
                    'ext_in' => 'Format file e-book harus PDF.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->listErrors();
            session()->setFlashdata('error', $validationErrors);
            return redirect()->to('/admin/input-buku')->withInput();
        }

        $coverBuku = $this->request->getFile('cover_buku');
        $ext1 = $coverBuku->getClientExtension();
        $namaFile1 = 'Cover-Buku-' . date('ymdHis') . '.' . $ext1;
        $coverBuku->move('Assets/CoverBuku', $namaFile1);

        $eBook = $this->request->getFile('e_book');
        $ext2 = $eBook->getClientExtension();
        $namaFile2 = 'E-Book-' . date('ymdHis') . '.' . $ext2;
        $eBook->move('Assets/E-Book', $namaFile2);

        $hasil = $modelBuku->autoNumber()->getRowArray();
        if (!$hasil) {
            $id = 'BKU001';
        } else {
            $kode = $hasil['id_buku'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = 'BKU' . sprintf('%03s', $noUrut);
        }

        $dataSimpan = [
            'id_buku' => $id,
            'judul_buku' => ucwords($judul_buku),
            'pengarang' => ucwords($pengarang),
            'penerbit' => ucwords($penerbit),
            'tahun' => $tahun,
            'jumlah_eksemplar' => $jumlah_eksemplar,
            'id_kategori' => $id_kategori,
            'keterangan' => $keterangan,
            'id_rak' => $id_rak,
            'cover_buku' => $namaFile1,
            'e_book' => $namaFile2,
            'is_delete_buku' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $modelBuku->simpanDataBuku($dataSimpan);
        session()->setFlashdata('success', 'Data Buku Berhasil Ditambahkan!');
        echo '<script>document.location = "' . base_url('admin/master-data-buku-buku') . '";</script>';
    }

    public function edit_buku()
    {
        $this->requireLogin();
        $modelBuku = new M_Buku();
        $modelKategori = new M_Kategori();
        $modelRak = new M_Rak();

        $uri = service('uri');
        $idEdit = $uri->getSegment(3);

        $dataBuku = $modelBuku->getDataBuku(['sha1(id_buku)' => $idEdit])->getRowArray();
        session()->set(['idUpdateBuku' => $dataBuku['id_buku']]);

        $data['data_buku'] = $dataBuku;
        $data['data_kategori'] = $modelKategori->getDataKategori(['id_delete_kategori' => '0'])->getResultArray();
        $data['data_rak'] = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterBuku/edit-buku', $data);
        echo view('Backend/Template/footer', $data);
    }

    public function update_buku()
    {
        $this->requireLogin();
        $modelBuku = new M_Buku();
        $judul_buku = $this->request->getPost('judul_buku');
        $pengarang = $this->request->getPost('pengarang');
        $penerbit = $this->request->getPost('penerbit');
        $tahun = $this->request->getPost('tahun');
        $jumlah_eksemplar = $this->request->getPost('jumlah_eksemplar');
        $id_kategori = $this->request->getPost('id_kategori');
        $keterangan = $this->request->getPost('keterangan');
        $id_rak = $this->request->getPost('id_rak');

        $dataBukulama = $modelBuku->getDataBuku(['id_buku' => session()->get('idUpdateBuku')])->getRowArray();

        $coverBuku = $this->request->getFile('cover_buku');
        $eBook = $this->request->getFile('e_book');

        if ($coverBuku->getSize() > 0) {
            if (
                !$this->validate([
                    'cover_buku' => 'max_size[cover_buku,1024]|ext_in[cover_buku,jpg,jpeg,png]'
                ])
            ) {
                session()->setFlashdata('error', 'Format file cover: jpg, jpeg, png. Maksimal 1 MB');
                return redirect()->to('/admin/edit-buku/' . sha1(session()->get('idUpdateBuku')))->withInput();
            }
            if (file_exists('Assets/CoverBuku/' . $dataBukulama['cover_buku'])) {
                unlink('Assets/CoverBuku/' . $dataBukulama['cover_buku']);
            }
            $ext1 = $coverBuku->getClientExtension();
            $namaFile1 = 'Cover-Buku-' . date('ymdHis') . '.' . $ext1;
            $coverBuku->move('Assets/CoverBuku', $namaFile1);
        } else {
            $namaFile1 = $dataBukulama['cover_buku'];
        }

        if ($eBook->getSize() > 0) {
            if (
                !$this->validate([
                    'e_book' => 'max_size[e_book,10240]|ext_in[e_book,pdf]'
                ])
            ) {
                session()->setFlashdata('error', 'Format file e-book: pdf. Maksimal 10 MB');
                return redirect()->to('/admin/edit-buku/' . sha1(session()->get('idUpdateBuku')))->withInput();
            }
            if (file_exists('Assets/E-Book/' . $dataBukulama['e_book'])) {
                unlink('Assets/E-Book/' . $dataBukulama['e_book']);
            }
            $ext2 = $eBook->getClientExtension();
            $namaFile2 = 'E-Book-' . date('ymdHis') . '.' . $ext2;
            $eBook->move('Assets/E-Book', $namaFile2);
        } else {
            $namaFile2 = $dataBukulama['e_book'];
        }

        $dataUpdate = [
            'judul_buku' => ucwords($judul_buku),
            'pengarang' => ucwords($pengarang),
            'penerbit' => ucwords($penerbit),
            'tahun' => $tahun,
            'jumlah_eksemplar' => $jumlah_eksemplar,
            'id_kategori' => $id_kategori,
            'keterangan' => $keterangan,
            'id_rak' => $id_rak,
            'cover_buku' => $namaFile1,
            'e_book' => $namaFile2,
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $whereUpdate = ['id_buku' => session()->get('idUpdateBuku')];
        $modelBuku->updateDataBuku($dataUpdate, $whereUpdate);
        session()->remove('idUpdateBuku');
        session()->setFlashdata('success', 'Data Buku Berhasil Diperbaharui!');
        echo '<script>document.location = "' . base_url('admin/master-data-buku-buku') . '";</script>';
    }

    public function hapus_buku()
    {
        $this->requireLogin();
        $modelBuku = new M_Buku();
        $uri = service('uri');
        $idHapus = $uri->getSegment(3);

        $dataBuku = $modelBuku->getDataBuku(['sha1(id_buku)' => $idHapus])->getRowArray();

        if (file_exists('Assets/CoverBuku/' . $dataBuku['cover_buku'])) {
            unlink('Assets/CoverBuku/' . $dataBuku['cover_buku']);
        }
        if (file_exists('Assets/E-Book/' . $dataBuku['e_book'])) {
            unlink('Assets/E-Book/' . $dataBuku['e_book']);
        }

        $dataUpdate = ['is_delete_buku' => '1', 'deleted_at' => date('Y-m-d H:i:s')];
        $whereUpdate = ['sha1(id_buku)' => $idHapus];
        $modelBuku->updateDataBuku($dataUpdate, $whereUpdate);

        session()->setFlashdata('success', 'Data Buku Berhasil Dihapus!');
        echo '<script>document.location = "' . base_url('admin/master-data-buku-buku') . '";</script>';
    }
}
