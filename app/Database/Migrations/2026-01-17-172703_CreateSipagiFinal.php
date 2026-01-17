<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SipagiFinalMaster extends Migration
{
    /**
     * FUNGSI PEMBANTU: Inisial getSoft_DeleteColumns
     * Menambahkan kolom pelacak (CCTV) secara otomatis.
     * Pak Rizky bisa memanggil ini di setiap tabel agar kodingan efisien (Clean Code).
     */
    private function getSoft_DeleteColumns()
    {
        return [
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true], // Kapan data diinput
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true], // Kapan data terakhir diupdate
            'deleted_at'    => ['type' => 'TIMESTAMP', 'null' => true], // Soft Delete: Data tidak hilang, hanya ditandai hapus
            'created_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true], // Siapa yang input (ID Pegawai)
            'updated_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true], // Siapa yang edit terakhir
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1], // Status aktif (1) atau non-aktif (0)
            'change_note'   => ['type' => 'TEXT', 'null' => true], // Catatan kenapa data diubah (Audit)
        ];
    }

    public function up()
    {
        // Matikan proteksi foreign key agar proses build tabel tidak gagal ditengah jalan
        $this->db->disableForeignKeyChecks();

        // --- 1. TABEL WILAYAH (db_regions_id) ---
        $this->forge->addField(array_merge([
            'id'        => ['type' => 'VARCHAR', 'constraint' => 15], // Kode wilayah (PK)
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100], // Nama daerah
            'level'     => ['type' => 'ENUM', 'constraint' => ['provinsi', 'kota', 'kecamatan', 'kelurahan']],
            'parent_id' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_regions_id');

        // --- 2. TABEL UNIT KERJA (db_workplaces) ---
        $this->forge->addField(array_merge([
            'id_workplace' => ['type' => 'VARCHAR', 'constraint' => 20],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'type'         => ['type' => 'ENUM', 'constraint' => ['pusat', 'wilayah', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'sppg']],
            'address'      => ['type' => 'TEXT', 'null' => true], // Dari Data Lama
            'region_id'    => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id_workplace', true);
        $this->forge->createTable('db_workplaces');

        // --- 3. TABEL PEGAWAI (db_employees) - FULL BIODATA ---
        $this->forge->addField(array_merge([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nik_ktp'          => ['type' => 'VARCHAR', 'constraint' => 16, 'unique' => true],
            'nik_pppk'         => ['type' => 'VARCHAR', 'constraint' => 25, 'null' => true],
            'full_name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'gender'           => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
            'blood_type'       => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'home_address'     => ['type' => 'TEXT', 'null' => true],
            'phone_number'     => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'username'         => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'digital_signature'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'profile_picture'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_employees');

        // --- 4. TABEL RELAWAN (db_volunteers) ---
        $this->forge->addField(array_merge([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nik_ktp'      => ['type' => 'VARCHAR', 'constraint' => 16, 'unique' => true],
            'full_name'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'specialty'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'phone_number' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'username'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'password'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_volunteers');

        // --- 5 & 6. TABEL DOKUMEN PEGAWAI & RELAWAN ---
        $docField = [
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'doc_label'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addField(array_merge($docField, ['employee_id' => ['type' => 'INT', 'unsigned' => true]], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_employee_documents');

        $this->forge->addField(array_merge($docField, ['volunteer_id' => ['type' => 'INT', 'unsigned' => true]], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_volunteer_documents');

        // --- 7 & 8. TABEL PENUGASAN ---
        $this->forge->addField(array_merge([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'employee_id'  => ['type' => 'INT', 'unsigned' => true],
            'workplace_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'role_level'   => ['type' => 'INT', 'constraint' => 2], // 1: Superadmin, 2: Dapur, dll
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_assignments');

        $this->forge->addField(array_merge([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'volunteer_id' => ['type' => 'INT', 'unsigned' => true],
            'workplace_id' => ['type' => 'VARCHAR', 'constraint' => 20],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_volunteer_assignments');

        // --- 9 & 10. TABEL YAYASAN & AFILIASI ---
        $this->forge->addField(array_merge([
            'id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'pic'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_foundations');

        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'foundation_id' => ['type' => 'INT', 'unsigned' => true],
            'workplace_id'  => ['type' => 'VARCHAR', 'constraint' => 20],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_affiliations');

        // --- 11 & 12. LOGIKA GIZI & SEKOLAH (Mesin Utama SIPAGI) ---
        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'jenjang'       => ['type' => 'ENUM', 'constraint' => ['PAUD', 'TK', 'SD_KECIL', 'SD_BESAR', 'SMP', 'SMA', 'BUMIL']],
            'price_index'   => ['type' => 'DECIMAL', 'constraint' => '15,2'], // Harga satuan porsi
            'category_name' => ['type' => 'VARCHAR', 'constraint' => 50],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_system_rules');

        $this->forge->addField(array_merge([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username'          => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'workplace_id'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 150],
            'jenjang'           => ['type' => 'ENUM', 'constraint' => ['PAUD', 'TK', 'SD_KECIL', 'SD_BESAR', 'SMP', 'SMA', 'BUMIL']],
            'total_male'        => ['type' => 'INT', 'default' => 0], // Untuk hitung gizi L
            'total_female'      => ['type' => 'INT', 'default' => 0], // Untuk hitung gizi P
            'pic_contact'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'delivery_distance' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_beneficiaries');

        // --- 13 & 14. STANDAR GIZI & BAHAN ---
        $this->forge->addField(array_merge([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'gender'      => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
            'age_range'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'energy_kcal' => ['type' => 'INT'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_nutrition_standards_id');

        $this->forge->addField(array_merge([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'unit'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'gram'],
            'kcal_per_100g'    => ['type' => 'INT'],
            'protein_per_100g' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'fat_per_100g'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'carbo_per_100g'   => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ingredients');

        // --- 15, 16, 17. MENU & MAPPING ---
        $this->forge->addField(array_merge([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'menu_name' => ['type' => 'VARCHAR', 'constraint' => 150],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_menu_blueprints');

        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'menu_id'       => ['type' => 'INT', 'unsigned' => true],
            'ingredient_id' => ['type' => 'INT', 'unsigned' => true],
            'weight_grams'  => ['type' => 'INT'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_menu_items');

        $this->forge->addField(array_merge([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'menu_id'           => ['type' => 'INT', 'unsigned' => true],
            'jenjang'           => ['type' => 'ENUM', 'constraint' => ['PAUD', 'TK', 'SD_KECIL', 'SD_BESAR', 'SMP', 'SMA', 'BUMIL']],
            'male_multiplier'   => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 1.00],
            'female_multiplier' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 1.00],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_nutrition_mapping');

        // --- 18 & 19. KEUANGAN (LEDGERS) ---
        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'workplace_id'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'balance_bahan' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'balance_ops'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers');

        $this->forge->addField(array_merge([
            'id'        => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'ledger_id' => ['type' => 'INT', 'unsigned' => true],
            'type'      => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
            'amount'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers_details');

        // --- 20 & 21. TABUNGAN PEGAWAI ---
        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'employee_id'   => ['type' => 'INT', 'unsigned' => true],
            'total_savings' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers_private');

        $this->forge->addField(array_merge([
            'id'                => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'private_ledger_id' => ['type' => 'INT', 'unsigned' => true],
            'amount'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'type'              => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers_private_details');

        // --- 22. BUDGET MINGGUAN ---
        $this->forge->addField(array_merge([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'workplace_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'week_number'  => ['type' => 'INT'],
            'max_limit'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'status'       => ['type' => 'ENUM', 'constraint' => ['open', 'locked'], 'default' => 'open'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_weekly_budgets');

        // --- 23, 24, 25, 26. LOGISTIK ---
        $this->forge->addField(array_merge([
            'id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'=> ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_suppliers');

        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'supplier_id'   => ['type' => 'INT', 'unsigned' => true],
            'ingredient_id' => ['type' => 'INT', 'unsigned' => true],
            'price'         => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_supplier_prices');

        $this->forge->addField(array_merge([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'workplace_id'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'ingredient_id' => ['type' => 'INT', 'unsigned' => true],
            'stock_qty'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_inventory');

        $this->forge->addField(array_merge([
            'id'           => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'inventory_id' => ['type' => 'INT', 'unsigned' => true],
            'type'         => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
            'qty'          => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_inventory_logs');

        // --- 27. DISTRIBUSI ---
        $this->forge->addField(array_merge([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'beneficiary_id' => ['type' => 'INT', 'unsigned' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['packing', 'on_way', 'delivered'], 'default' => 'packing'],
            'arrival_time'   => ['type' => 'DATETIME', 'null' => true],
        ], $this->getSoft_DeleteColumns()));
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_delivery_orders');

        // --- 28. AUDIT LOGS (CCTV Pusat) ---
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'action'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'table_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'old_value'  => ['type' => 'JSON', 'null' => true],
            'new_value'  => ['type' => 'JSON', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_audit_logs');

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();
        $tables = [
            't_audit_logs',
            't_delivery_orders',
            't_inventory_logs',
            'db_inventory',
            't_supplier_prices',
            'db_suppliers',
            't_weekly_budgets',
            'db_ledgers_private_details',
            'db_ledgers_private',
            'db_ledgers_details',
            'db_ledgers',
            't_nutrition_mapping',
            't_menu_items',
            't_menu_blueprints',
            'db_ingredients',
            'db_nutrition_standards_id',
            'db_beneficiaries',
            'db_system_rules',
            't_affiliations',
            'db_foundations',
            'db_volunteer_assignments',
            'db_assignments',
            'db_volunteer_documents',
            'db_employee_documents',
            'db_volunteers',
            'db_employees',
            'db_workplaces',
            'db_regions_id'
        ];
        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }
        $this->db->enableForeignKeyChecks();
    }
}