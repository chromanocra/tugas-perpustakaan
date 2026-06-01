<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'tbl_admin';

        $username = getenv('ADMIN_SEED_USERNAME') ?: 'admin';
        $plain     = getenv('ADMIN_SEED_PASSWORD') ?: 'admin123';

        if ($this->db->table($table)->where('username_admin', $username)->countAllResults() > 0) {
            return;
        }

        $nextIdRow = $this->db->query(
            'SELECT COALESCE(MAX(CAST(NULLIF(TRIM(`id_admin`), \'\') AS UNSIGNED)), 0) + 1 AS n FROM `' . $table . '`'
        )->getRow();
        $idAdmin = str_pad((string) ($nextIdRow->n ?? 1), 6, '0', STR_PAD_LEFT);

        $now = date('Y-m-d H:i:s');

        $this->db->table($table)->insert([
            'id_admin'         => $idAdmin,
            'nama_admin'       => 'Administrator',
            'username_admin'   => $username,
            'password_admin'   => password_hash($plain, PASSWORD_DEFAULT),
            'akses_level'      => '1',
            'is_delete_admin'  => '0',
            'created_at'       => $now,
            'update_at'        => $now,
        ]);
    }
}
