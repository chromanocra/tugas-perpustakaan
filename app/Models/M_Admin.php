<?php
namespace App\Models;

use CodeIgniter\Model;

class M_Admin extends Model
{
    protected $table = 'tbl_admin';

    public function getDataAdmin($where = false)
    {
        if ($where === false) {
            $builder = $this->db->table($this->table);
            $builder->select("*");
            $builder->orderBy('nama_admin', 'Asc');
            return $builder->get();
        } else {
            $builder = $this->db->table($this->table);
            $builder->select("*");
            $builder->where($where);
            $builder->orderBy('nama_admin', 'Asc');
            return $builder->get();
        }
    }

    // Untuk generate ID otomatis seperti ADM001, ADM002, dst
    public function autoNumber()
    {
        $builder = $this->db->table($this->table);
        $builder->select("id_admin");
        $builder->orderBy('id_admin', 'DESC');
        $builder->limit(1);
        return $builder->get();
    }

    // Untuk simpan data admin baru
    public function simpanDataAdmin($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function saveDataAdmin($data)
    {
        $builder = $this->db->table($this->table);
        return $builder->insert($data);
    }

    public function updateDataAdmin($data, $where)
{
    $builder = $this->db->table($this->table);
    $builder->where($where);
    return $builder->update($data);
}
}