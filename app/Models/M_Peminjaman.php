<?php
namespace App\Models;
use CodeIgniter\Model;

class M_Peminjaman extends Model
{
    protected $table = 'tbl_peminjaman';
    protected $primaryKey = 'no_peminjaman';
    protected $allowedFields = ['no_peminjaman', 'id_anggota', 'tgl_pinjam', 'total_pinjam', 'status_transaksi', 'status_ambil_buku', 'qr_code'];
}
