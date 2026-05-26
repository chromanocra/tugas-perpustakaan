<?php
namespace App\Models;

use CodeIgniter\Model;

class M_Kategori extends Model
{
    protected $table = 'tbl_kategori';

    public function getDataKategori($where = false)
    {
        if ($where === false) {
            $builder = $this->db->table($this->table);
            $builder->select("*");
            $builder->orderBy('nama_kategori', 'Asc');
            return $builder->get();
        } else {
            $builder = $this->db->table($this->table);
            $builder->select("*");
            $builder->where($where);
            $builder->orderBy('nama_kategori', 'Asc');
            return $builder->get();
        }
    }

    public function autoNumber()
    {
        $builder = $this->db->table($this->table);
        $builder->select("id_kategori");
        $builder->orderBy("id_kategori", "DESC");
        $builder->limit(1);
        return $builder->get();
    }

    public function simpanDataKategori($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function updateDataKategori($data, $where)
    {
        $builder = $this->db->table($this->table);
        $builder->where($where);
        return $builder->update($data);
    }
}