<?php
namespace App\Controllers;
use App\Models\M_admin;
use App\Models\M_Anggota;
use App\Models\M_Rak;
use App\Models\M_Kategori;
use App\Models\M_Buku;
class Admin extends BaseController
{
     public function login()
    {
        return view('backend/login/login');
    }


     public function autentikasi()
    {
        $modelAdmin = new M_admin();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $cekUsername = $modelAdmin->getDataAdmin(['username_admin' => $username,'is_delete_admin' =>'0'])->getNumRows();
        if ($cekUsername ==  0) {
            session()->setFlashdata('Error', 'Username Tidak Ditemukan');
            ?>
            <script>
                history.go(-1);
            </script>
            <?php
            } 
            else {
            $dataUser = $modelAdmin->getDataAdmin(['username_admin' => $username,'is_delete_admin' =>'0'])->getRowArray();
            $passwordUser = $dataUser['password_admin'];

            $verifikasipassword = password_verify($password, $passwordUser);
            if(!$verifikasipassword){
                session()->setFlashdata('Error', 'Password Tidak Sesuai');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $dataSesion = [
                    'ses_id' => $dataUser['id_admin'],
                    'ses_user' => $dataUser['nama_admin'],
                    'ses_level' => $dataUser['akses_level'] 
                ];
                session()->set($dataSesion);
                session()->setFlashdata('Success', 'Login Berhasil');
                ?>
                <script>
                    document.location.href = "<?= base_url('admin/dashboard-admin') ?>";
                </script>
                <?php
            }
        }
    }

    public function dashboard()
    {    
        if(session()->get('ses_id')=="" or session()->get('ses_level')== "")
        {
           session()->setFlashdata('Error', 'Anda Harus Login Terlebih Dahulu');
           ?>
           <script>
            document.location.href = "<?= base_url('admin/login-admin') ?>";
           </script>
              <?php

        }else{
            echo view('Backend/Template/header');
            echo view('Backend/Template/sidebar');
            echo view('Backend/Login/dasboard_admin');
            echo view('Backend/Template/footer');
        }
    }
    public function logout()
    {
        session()->remove('ses_id');
        session()->remove('ses_user');
        session()->remove('ses_level');
        session()->setFlashdata('info', 'Anda Telah Keluar dari Sistem!');
        ?>
        <script>
            document.location.href = "<?= base_url('admin/login-admin');?>";
        </script>
        <?php
    }


    public function input_data_admin(){
        if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "")
        {
            session()->setFlashdata('Error', 'Anda Harus Login Terlebih Dahulu');
            ?>
            <script>
                document.location.href = "<?= base_url('admin/login-admin');?>";
            </script>
            <?php
        } else {
            echo view('Backend/Template/header');
            echo view('Backend/Template/sidebar');
            echo view('Backend/MasterAdmin/input-admin');
            echo view('Backend/Template/footer');
        }
    }

    public function simpan_data_admin(){
        if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "")
        {
            session()->setFlashdata('Error', 'Anda Harus Login Terlebih Dahulu');
            ?>
            <script>
                document.location.href = "<?= base_url('admin/login-admin');?>";
            </script>
            <?php
        } else {
            $modelAdmin = new M_admin();
            $nama = $this->request->getPost('nama');
            $username = $this->request->getPost('username');
            $level = $this->request->getPost('level');
            
            $cekUsername = $modelAdmin->getDataAdmin(['username_admin' => $username])->getNumRows();
            if($cekUsername > 0){
                session()->setFlashdata('Error', 'Username Sudah Digunakan');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $hasil = $modelAdmin->autoNumber()->getRowArray();
                if(!$hasil){
                    $id = "ADM001";
                }else{
                    $kode =$hasil['id_admin'];
                    $noUrut = (int) substr($kode, -3);
                    $noUrut++;
                    $id = "ADM".sprintf("%03s", $noUrut);
                }
                $dataSimpan = [
                    'id_admin' => $id,
                    'nama_admin' => $nama,
                    'username_admin' => $username,
                    'password_admin' => password_hash('pass_admin', PASSWORD_DEFAULT),
                    'akses_level' => $level,
                    'is_delete_admin' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $modelAdmin->simpanDataAdmin($dataSimpan);
                session()->setFlashdata('Success', 'Data Admin Berhasil Ditambahkan');
                ?>
                <script>
                    document.location.href = "<?= base_url('admin/master-data-admin');?>";
                </script>
                <?php
            }
        }
    }

    public function master_data_admin(){
        if(session()->get('ses_id')==="" or session()->get('ses_user')==="" or session()->get('ses_level')===""){
            session()->setFlashdata('error','Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin');?>";
            </script>
            <?php
        }
        else{
            $modelAdmin = new M_admin; // inisiasi

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
    }

    public function edit_data_admin()
    {
        if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "")
        {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin');?>";
            </script>
            <?php
        } else {
            $modelAdmin = new M_admin();

            $uri = service('uri');
            $idEdit = $uri->getSegment(3);

            // Ambil data admin berdasarkan id yang dikirim via sha1
            $dataAdmin = $modelAdmin->getDataAdmin(['sha1(id_admin)' => $idEdit])->getRowArray();
            session()->set(['idUpdate' => $dataAdmin['id_admin']]);

            $page = $uri->getSegment(2);

            $data['page']         = $page;
            $data['web_title']    = "Edit Data Admin";
            $data['data_admin']   = $dataAdmin;

            echo view('Backend/Template/header', $data);
            echo view('Backend/Template/sidebar', $data);
            echo view('Backend/MasterAdmin/edit-admin', $data);
            echo view('Backend/Template/footer', $data);
        }
    }

    public function update_data_admin()
    {
        if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "")
        {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin');?>";
            </script>
            <?php
        } else {
            $modelAdmin = new M_admin();

            $nama  = $this->request->getPost('nama');
            $level = $this->request->getPost('level');

            if($nama == "" or $level == "") {
                session()->setFlashdata('error', 'Isian tidak boleh kosong!!');
                ?>
                <script>
                    history.go(-1);
                </script>
                <?php
            } else {
                $dataUpdate = [
                    'nama_admin'  => $nama,
                    'akses_level' => $level,
                    'updated_at'  => date('Y-m-d H:i:s')
                ];
                $whereUpdate = ['id_admin' => session()->get('idUpdate')];

                $modelAdmin->updateDataAdmin($dataUpdate, $whereUpdate);
                session()->remove('idUpdate');
                session()->setFlashdata('success', 'Data Admin Berhasil Diperbaharui!');
                ?>
                <script>
                    document.location = "<?= base_url('admin/master-data-admin');?>";
                </script>
                <?php
            }
        }
    }

    public function hapus_data_admin()
    {
        if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "")
        {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
            ?>
            <script>
                document.location = "<?= base_url('admin/login-admin');?>";
            </script>
            <?php
        } else {
            $modelAdmin = new M_admin();

            $uri     = service('uri');
            $idHapus = $uri->getSegment(3);

            $dataUpdate  = [
                'is_delete_admin' => '1',
                'updated_at'      => date('Y-m-d H:i:s')
            ];
            $whereUpdate = ['sha1(id_admin)' => $idHapus];

            $modelAdmin->updateDataAdmin($dataUpdate, $whereUpdate);
            session()->setFlashdata('success', 'Data Admin Berhasil Dihapus!');
            ?>
            <script>
                document.location = "<?= base_url('admin/master-data-admin');?>";
            </script>
            <?php
        }
    }

    // ==================== ANGGOTA ====================

public function master_data_anggota()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelAnggota = new M_Anggota();
        $uri   = service('uri');
        $pages = $uri->getSegment(2);
        $dataAnggota = $modelAnggota->getDataAnggota(['is_delete_anggota' => '0'])->getResultArray();
        $data['pages']        = $pages;
        $data['data_anggota'] = $dataAnggota;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterAnggota/master-data-anggota', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function input_data_anggota()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/MasterAnggota/input-anggota');
        echo view('Backend/Template/footer');
    }
}

public function simpan_data_anggota()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelAnggota = new M_Anggota();
        $nama         = $this->request->getPost('nama');
        $jk           = $this->request->getPost('jenis_kelamin');
        $no_telp       = $this->request->getPost('no_telp');
        $alamat       = $this->request->getPost('alamat');
        $email        = $this->request->getPost('email');

        $hasil = $modelAnggota->autoNumber()->getRowArray();
        if(!$hasil) {
            $id = "AGT001";
        } else {
            $kode   = $hasil['id_anggota'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = "AGT".sprintf("%03s", $noUrut);
        }

        $dataSimpan = [
            'id_anggota'        => $id,
            'nama_anggota'      => $nama,
            'jenis_kelamin'     => $jk,
            'no_telp'           => $no_telp,   // ← ganti no_telp jadi no_telp
            'alamat'            => $alamat,
            'email'             => $email,
            'password_anggota'  => password_hash('pass_anggota', PASSWORD_DEFAULT),
            'is_delete_anggota' => '0',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        ];
        $modelAnggota->simpanDataAnggota($dataSimpan);
        session()->setFlashdata('success', 'Data Anggota Berhasil Ditambahkan!');
        ?><script>document.location = "<?= base_url('admin/master-data-anggota');?>";</script><?php
    }
}

public function edit_data_anggota()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelAnggota = new M_Anggota();
        $uri     = service('uri');
        $idEdit  = $uri->getSegment(3);
        $dataAnggota = $modelAnggota->getDataAnggota(['sha1(id_anggota)' => $idEdit])->getRowArray();
        session()->set(['idUpdateAnggota' => $dataAnggota['id_anggota']]);
        $data['data_anggota'] = $dataAnggota;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterAnggota/edit-anggota', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function update_data_anggota()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelAnggota = new M_Anggota();
        $nama   = $this->request->getPost('nama');
        $jk     = $this->request->getPost('jenis_kelamin');
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $email  = $this->request->getPost('email');

        $dataUpdate  = [
            'nama_anggota'  => $nama,
            'jenis_kelamin' => $jk,
            'no_telp'        => $no_telp,
            'alamat'        => $alamat,
            'email'         => $email,
            'updated_at'    => date('Y-m-d H:i:s')
        ];
        $whereUpdate = ['id_anggota' => session()->get('idUpdateAnggota')];
        $modelAnggota->updateDataAnggota($dataUpdate, $whereUpdate);
        session()->remove('idUpdateAnggota');
        session()->setFlashdata('success', 'Data Anggota Berhasil Diperbaharui!');
        ?><script>document.location = "<?= base_url('admin/master-data-anggota');?>";</script><?php
    }
}

public function hapus_data_anggota()
{
    $modelAnggota = new M_Anggota();
    $uri     = service('uri');
    $idHapus = $uri->getSegment(3);
    $dataUpdate  = ['is_delete_anggota' => '1', 'updated_at' => date('Y-m-d H:i:s')];
    $whereUpdate = ['sha1(id_anggota)' => $idHapus];
    $modelAnggota->updateDataAnggota($dataUpdate, $whereUpdate);
    session()->setFlashdata('success', 'Data Anggota Berhasil Dihapus!');
    ?><script>document.location = "<?= base_url('admin/master-data-anggota');?>";</script><?php
}

// ==================== RAK ====================

public function master_data_rak()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelRak = new M_Rak();
        $uri   = service('uri');
        $pages = $uri->getSegment(2);
        $dataRak = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();
        $data['pages']    = $pages;
        $data['data_rak'] = $dataRak;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterRak/master-data-rak', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function input_data_rak()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/MasterRak/input-rak');
        echo view('Backend/Template/footer');
    }
}

public function simpan_data_rak()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelRak = new M_Rak();
        $nama_rak = $this->request->getPost('nama_rak');

        $hasil = $modelRak->autoNumber()->getRowArray();
        if(!$hasil) {
            $id = "RAK001";
        } else {
            $kode   = $hasil['id_rak'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = "RAK".sprintf("%03s", $noUrut);
        }

        $dataSimpan = [
            'id_rak'        => $id,
            'nama_rak'      => $nama_rak,
            'is_delete_rak' => '0',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];
        $modelRak->simpanDataRak($dataSimpan);
        session()->setFlashdata('success', 'Data Rak Berhasil Ditambahkan!');
        ?><script>document.location = "<?= base_url('admin/master-data-rak');?>";</script><?php
    }
}

public function edit_data_rak()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelRak = new M_Rak();
        $uri    = service('uri');
        $idEdit = $uri->getSegment(3);
        $dataRak = $modelRak->getDataRak(['sha1(id_rak)' => $idEdit])->getRowArray();
        session()->set(['idUpdateRak' => $dataRak['id_rak']]);
        $data['data_rak'] = $dataRak;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterRak/edit-rak', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function update_data_rak()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelRak = new M_Rak();
        $nama_rak    = $this->request->getPost('nama_rak');
        $dataUpdate  = ['nama_rak' => $nama_rak, 'updated_at' => date('Y-m-d H:i:s')];
        $whereUpdate = ['id_rak' => session()->get('idUpdateRak')];
        $modelRak->updateDataRak($dataUpdate, $whereUpdate);
        session()->remove('idUpdateRak');
        session()->setFlashdata('success', 'Data Rak Berhasil Diperbaharui!');
        ?><script>document.location = "<?= base_url('admin/master-data-rak');?>";</script><?php
    }
}

public function hapus_data_rak()
{
    $modelRak = new M_Rak();
    $uri     = service('uri');
    $idHapus = $uri->getSegment(3);
    $dataUpdate  = ['is_delete_rak' => '1', 'updated_at' => date('Y-m-d H:i:s')];
    $whereUpdate = ['sha1(id_rak)' => $idHapus];
    $modelRak->updateDataRak($dataUpdate, $whereUpdate);
    session()->setFlashdata('success', 'Data Rak Berhasil Dihapus!');
    ?><script>document.location = "<?= base_url('admin/master-data-rak');?>";</script><?php
}

// ==================== KATEGORI ====================

public function master_data_kategori()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelKategori = new M_Kategori();
        $uri   = service('uri');
        $pages = $uri->getSegment(2);
        $dataKategori = $modelKategori->getDataKategori(['id_delete_kategori' => '0'])->getResultArray();
        $data['pages']         = $pages;
        $data['data_kategori'] = $dataKategori;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterKategori/master-data-kategori', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function input_data_kategori()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        echo view('Backend/Template/header');
        echo view('Backend/Template/sidebar');
        echo view('Backend/MasterKategori/input-kategori');
        echo view('Backend/Template/footer');
    }
}

public function simpan_data_kategori()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelKategori = new M_Kategori();
        $nama_kategori = $this->request->getPost('nama_kategori');

        $hasil = $modelKategori->autoNumber()->getRowArray();
        if(!$hasil) {
            $id = "KTG001";
        } else {
            $kode   = $hasil['id_kategori'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = "KTG".sprintf("%03s", $noUrut);
        }

        $dataSimpan = [
            'id_kategori'        => $id,
            'nama_kategori'      => $nama_kategori,
            'id_delete_kategori' => '0',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s')
        ];
        $modelKategori->simpanDataKategori($dataSimpan);
        session()->setFlashdata('success', 'Data Kategori Berhasil Ditambahkan!');
        ?><script>document.location = "<?= base_url('admin/master-data-kategori');?>";</script><?php
    }
}

public function edit_data_kategori()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelKategori = new M_Kategori();
        $uri    = service('uri');
        $idEdit = $uri->getSegment(3);
        $dataKategori = $modelKategori->getDataKategori(['sha1(id_kategori)' => $idEdit])->getRowArray();
        session()->set(['idUpdateKategori' => $dataKategori['id_kategori']]);
        $data['data_kategori'] = $dataKategori;
        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterKategori/edit-kategori', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function update_data_kategori()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelKategori = new M_Kategori();
        $nama_kategori = $this->request->getPost('nama_kategori');
        $dataUpdate    = ['nama_kategori' => $nama_kategori, 'updated_at' => date('Y-m-d H:i:s')];
        $whereUpdate   = ['id_kategori' => session()->get('idUpdateKategori')];
        $modelKategori->updateDataKategori($dataUpdate, $whereUpdate);
        session()->remove('idUpdateKategori');
        session()->setFlashdata('success', 'Data Kategori Berhasil Diperbaharui!');
        ?><script>document.location = "<?= base_url('admin/master-data-kategori');?>";</script><?php
    }
}

public function hapus_data_kategori()
{
    $modelKategori = new M_Kategori();
    $uri     = service('uri');
    $idHapus = $uri->getSegment(3);
    $dataUpdate  = ['id_delete_kategori' => '1', 'updated_at' => date('Y-m-d H:i:s')];
    $whereUpdate = ['sha1(id_kategori)' => $idHapus];
    $modelKategori->updateDataKategori($dataUpdate, $whereUpdate);
    session()->setFlashdata('success', 'Data Kategori Berhasil Dihapus!');
    ?><script>document.location = "<?= base_url('admin/master-data-kategori');?>";</script><?php
}
// ==================== BUKU ====================

public function master_buku()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelBuku = new M_Buku();
        $uri   = service('uri');
        $pages = $uri->getSegment(2);

        $dataBuku = $modelBuku->getDataBukuJoin(['tbl_buku.is_delete_buku' => '0'])->getResultArray();

        $data['pages']     = $pages;
        $data['data_buku'] = $dataBuku;

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterBuku/master-data-buku', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function input_buku()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelKategori = new M_Kategori();
        $modelRak      = new M_Rak();

        $data['data_kategori'] = $modelKategori->getDataKategori(['id_delete_kategori' => '0'])->getResultArray();
        $data['data_rak']      = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterBuku/input-buku', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function simpan_buku()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelBuku        = new M_Buku();
        $judul_buku       = $this->request->getPost('judul_buku');
        $pengarang        = $this->request->getPost('pengarang');
        $penerbit         = $this->request->getPost('penerbit');
        $tahun            = $this->request->getPost('tahun');
        $jumlah_eksemplar = $this->request->getPost('jumlah_eksemplar');
        $id_kategori      = $this->request->getPost('id_kategori');
        $keterangan       = $this->request->getPost('keterangan');
        $id_rak           = $this->request->getPost('id_rak');
$rules = [
    'cover_buku' => [
        'rules'  => 'uploaded[cover_buku]|max_size[cover_buku,1024]|ext_in[cover_buku,jpg,jpeg,png]',
        'errors' => [
            'uploaded' => 'Cover buku wajib diunggah.',
            'max_size' => 'Ukuran cover maksimal 1 MB.',
            'ext_in'   => 'Format file cover harus jpg, jpeg, atau png.'
        ]
    ],
    'e_book' => [
        'rules'  => 'uploaded[e_book]|max_size[e_book,10240]|ext_in[e_book,pdf]',
        'errors' => [
            'uploaded' => 'File e-book wajib diunggah.',
            'max_size' => 'Ukuran e-book maksimal 10 MB.',
            'ext_in'   => 'Format file e-book harus PDF.'
        ]
    ]
];

if (!$this->validate($rules)) {
    // Ambil semua pesan error dari validasi yang gagal
    $validationErrors = $this->validator->listErrors();
    
    // Set flashdata dengan error bawaan validator
    session()->setFlashdata('error', $validationErrors);
    
    // Kembalikan ke halaman sebelumnya beserta input yang sudah diisi
    return redirect()->to('/admin/input-buku')->withInput();
}
        // Upload cover buku
        $coverBuku  = $this->request->getFile('cover_buku');
        $ext1       = $coverBuku->getClientExtension();
        $namaFile1  = "Cover-Buku-".date("ymdHis").".".$ext1;
        $coverBuku->move('Assets/CoverBuku', $namaFile1);

        // Upload e-book
        $eBook      = $this->request->getFile('e_book');
        $ext2       = $eBook->getClientExtension();
        $namaFile2  = "E-Book-".date("ymdHis").".".$ext2;
        $eBook->move('Assets/E-Book', $namaFile2);

        // Auto number
        $hasil = $modelBuku->autoNumber()->getRowArray();
        if(!$hasil) {
            $id = "BKU001";
        } else {
            $kode   = $hasil['id_buku'];
            $noUrut = (int) substr($kode, -3);
            $noUrut++;
            $id = "BKU".sprintf("%03s", $noUrut);
        }

        $dataSimpan = [
            'id_buku'          => $id,
            'judul_buku'       => ucwords($judul_buku),
            'pengarang'        => ucwords($pengarang),
            'penerbit'         => ucwords($penerbit),
            'tahun'            => $tahun,
            'jumlah_eksemplar' => $jumlah_eksemplar,
            'id_kategori'      => $id_kategori,
            'keterangan'       => $keterangan,
            'id_rak'           => $id_rak,
            'cover_buku'       => $namaFile1,
            'e_book'           => $namaFile2,
            'is_delete_buku'   => '0',
            'created_at'       => date('Y-m-d H:i:s'),
            'deleted_at'       => date('Y-m-d H:i:s')
        ];

        $modelBuku->simpanDataBuku($dataSimpan);
        session()->setFlashdata('success', 'Data Buku Berhasil Ditambahkan!');
        ?><script>document.location = "<?= base_url('admin/master-data-buku-buku');?>";</script><?php
    }
}

public function edit_buku()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelBuku     = new M_Buku();
        $modelKategori = new M_Kategori();
        $modelRak      = new M_Rak();

        $uri    = service('uri');
        $idEdit = $uri->getSegment(3);

        $dataBuku = $modelBuku->getDataBuku(['sha1(id_buku)' => $idEdit])->getRowArray();
        session()->set(['idUpdateBuku' => $dataBuku['id_buku']]);

        $data['data_buku']     = $dataBuku;
        $data['data_kategori'] = $modelKategori->getDataKategori(['id_delete_kategori' => '0'])->getResultArray();
        $data['data_rak']      = $modelRak->getDataRak(['is_delete_rak' => '0'])->getResultArray();

        echo view('Backend/Template/header', $data);
        echo view('Backend/Template/sidebar', $data);
        echo view('Backend/MasterBuku/edit-buku', $data);
        echo view('Backend/Template/footer', $data);
    }
}

public function update_buku()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelBuku        = new M_Buku();
        $judul_buku       = $this->request->getPost('judul_buku');
        $pengarang        = $this->request->getPost('pengarang');
        $penerbit         = $this->request->getPost('penerbit');
        $tahun            = $this->request->getPost('tahun');
        $jumlah_eksemplar = $this->request->getPost('jumlah_eksemplar');
        $id_kategori      = $this->request->getPost('id_kategori');
        $keterangan       = $this->request->getPost('keterangan');
        $id_rak           = $this->request->getPost('id_rak');

        $dataBukulama = $modelBuku->getDataBuku(['id_buku' => session()->get('idUpdateBuku')])->getRowArray();

        $coverBuku = $this->request->getFile('cover_buku');
        $eBook     = $this->request->getFile('e_book');

        // Cek apakah cover diganti
        if($coverBuku->getSize() > 0) {
            if(!$this->validate([
                'cover_buku' => 'max_size[cover_buku,1024]|ext_in[cover_buku,jpg,jpeg,png]'
            ])){
                session()->setFlashdata('error', 'Format file cover: jpg, jpeg, png. Maksimal 1 MB');
                return redirect()->to('/admin/edit-buku/'.sha1(session()->get('idUpdateBuku')))->withInput();
            }
            // Hapus file lama
            if(file_exists('Assets/CoverBuku/'.$dataBukulama['cover_buku'])){
                unlink('Assets/CoverBuku/'.$dataBukulama['cover_buku']);
            }
            $ext1      = $coverBuku->getClientExtension();
            $namaFile1 = "Cover-Buku-".date("ymdHis").".".$ext1;
            $coverBuku->move('Assets/CoverBuku', $namaFile1);
        } else {
            $namaFile1 = $dataBukulama['cover_buku'];
        }

        // Cek apakah e-book diganti
        if($eBook->getSize() > 0) {
            if(!$this->validate([
                'e_book' => 'max_size[e_book,10240]|ext_in[e_book,pdf]'
            ])){
                session()->setFlashdata('error', 'Format file e-book: pdf. Maksimal 10 MB');
                return redirect()->to('/admin/edit-buku/'.sha1(session()->get('idUpdateBuku')))->withInput();
            }
            // Hapus file lama
            if(file_exists('Assets/E-Book/'.$dataBukulama['e_book'])){
                unlink('Assets/E-Book/'.$dataBukulama['e_book']);
            }
            $ext2      = $eBook->getClientExtension();
            $namaFile2 = "E-Book-".date("ymdHis").".".$ext2;
            $eBook->move('Assets/E-Book', $namaFile2);
        } else {
            $namaFile2 = $dataBukulama['e_book'];
        }

        $dataUpdate = [
            'judul_buku'       => ucwords($judul_buku),
            'pengarang'        => ucwords($pengarang),
            'penerbit'         => ucwords($penerbit),
            'tahun'            => $tahun,
            'jumlah_eksemplar' => $jumlah_eksemplar,
            'id_kategori'      => $id_kategori,
            'keterangan'       => $keterangan,
            'id_rak'           => $id_rak,
            'cover_buku'       => $namaFile1,
            'e_book'           => $namaFile2,
            'deleted_at'       => date('Y-m-d H:i:s')
        ];

        $whereUpdate = ['id_buku' => session()->get('idUpdateBuku')];
        $modelBuku->updateDataBuku($dataUpdate, $whereUpdate);
        session()->remove('idUpdateBuku');
        session()->setFlashdata('success', 'Data Buku Berhasil Diperbaharui!');
        ?><script>document.location = "<?= base_url('admin/master-data-buku-buku');?>";</script><?php
    }
}

public function hapus_buku()
{
    if(session()->get('ses_id') == "" or session()->get('ses_user') == "" or session()->get('ses_level') == "") {
        session()->setFlashdata('error', 'Silakan login terlebih dahulu!');
        ?><script>document.location = "<?= base_url('admin/login-admin');?>";</script><?php
    } else {
        $modelBuku = new M_Buku();
        $uri       = service('uri');
        $idHapus   = $uri->getSegment(3);

        $dataBuku = $modelBuku->getDataBuku(['sha1(id_buku)' => $idHapus])->getRowArray();

        // Hapus file cover dan e-book
        if(file_exists('Assets/CoverBuku/'.$dataBuku['cover_buku'])){
            unlink('Assets/CoverBuku/'.$dataBuku['cover_buku']);
        }
        if(file_exists('Assets/E-Book/'.$dataBuku['e_book'])){
            unlink('Assets/E-Book/'.$dataBuku['e_book']);
        }

        $dataUpdate  = ['is_delete_buku' => '1', 'deleted_at' => date('Y-m-d H:i:s')];
        $whereUpdate = ['sha1(id_buku)' => $idHapus];
        $modelBuku->updateDataBuku($dataUpdate, $whereUpdate);

        session()->setFlashdata('success', 'Data Buku Berhasil Dihapus!');
        ?><script>document.location = "<?= base_url('admin/master-data-buku-buku');?>";</script><?php
    }
}

}

   