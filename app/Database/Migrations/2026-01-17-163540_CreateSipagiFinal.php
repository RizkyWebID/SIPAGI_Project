<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSipagiFinal extends Migration
{
    public function up()
    {
        // 1. WILAYAH
        $this->forge->addField([
            'id'        => ['type' => 'VARCHAR', 'constraint' => 15],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'level'     => ['type' => 'ENUM', 'constraint' => ['provinsi', 'kota', 'kecamatan', 'kelurahan']],
            'parent_id' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_regions_id');

        // 2. TEMPAT KERJA (WORKPLACES)
        $this->forge->addField([
            'id_workplace' => ['type' => 'VARCHAR', 'constraint' => 20],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'type'         => ['type' => 'ENUM', 'constraint' => ['pusat', 'wilayah', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'sppg']],
            'parent_id'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'region_id'    => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'address'      => ['type' => 'TEXT', 'null' => true],
            'is_active'    => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addKey('id_workplace', true);
        $this->forge->createTable('db_workplaces');

        // 3. PEGAWAI (STAFF)
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
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
            'created_at'       => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_employees');

        // 4. RELAWAN (VOLUNTEERS)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nik_ktp'      => ['type' => 'VARCHAR', 'constraint' => 16, 'unique' => true],
            'full_name'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'specialty'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'phone_number' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'username'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'password'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'   => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_volunteers');

        // 5. DOKUMEN PEGAWAI & RELAWAN (DIPISAH)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'employee_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'doc_label'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'uploaded_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_employee_documents');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'volunteer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'doc_label'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'file_path'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'uploaded_at'  => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_volunteer_documents');

        // 6. PENUGASAN (ASSIGNMENTS)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'employee_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'workplace_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'role_level'   => ['type' => 'INT', 'constraint' => 2],
            'status'       => ['type' => 'ENUM', 'constraint' => ['active', 'expired'], 'default' => 'active'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_assignments');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'volunteer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'workplace_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'status'       => ['type' => 'ENUM', 'constraint' => ['active', 'mutated', 'resigned'], 'default' => 'active'],
            'moved_to_unit'=> ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'assigned_at'  => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_volunteer_assignments');

        // 7. YAYASAN (SAAS)
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'pic_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'    => ['type' => 'VARCHAR', 'constraint' => 15],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_foundations');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'foundation_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'workplace_id'  => ['type' => 'VARCHAR', 'constraint' => 20],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_affiliations');

        // 8. PENERIMA & GIZI (GELONDONGAN)
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'type'              => ['type' => 'ENUM', 'constraint' => ['sekolah', 'balita', 'bumil', 'umum']],
            'workplace_id'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'region_id'         => ['type' => 'VARCHAR', 'constraint' => 15],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 150],
            'unique_code'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'qty_cat_a'         => ['type' => 'INT', 'default' => 0],
            'qty_cat_b'         => ['type' => 'INT', 'default' => 0],
            'delivery_distance' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'pic_contact'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_beneficiaries');

        // 9. STANDAR & BAHAN
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'gender'      => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
            'age_range'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'energy_kcal' => ['type' => 'INT'],
            'protein_g'   => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'fat_g'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'carbo_g'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_nutrition_standards_id');

        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'unit'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'gram'],
            'kcal_per_100g'    => ['type' => 'INT'],
            'protein_per_100g' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'fat_per_100g'     => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'carbo_per_100g'   => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ingredients');

        // 10. MENU
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'menu_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'created_by'=> ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_menu_blueprints');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'menu_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'ingredient_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'weight_grams'  => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_menu_items');

        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'menu_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'target_age_group'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'portion_multiplier'=> ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 1.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_nutrition_mapping');

        // 11. KEUANGAN (LEDGERS)
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'workplace_id'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'balance_bahan'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'balance_ops'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'balance_intensif'=> ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers');

        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'ledger_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category'    => ['type' => 'ENUM', 'constraint' => ['bahan', 'ops', 'intensif']],
            'type'        => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
            'amount'      => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers_details');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'employee_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'total_savings' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers_private');

        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'private_ledger_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'amount'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'type'              => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_ledgers_private_details');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'workplace_id' => ['type' => 'VARCHAR', 'constraint' => 20],
            'week_number'  => ['type' => 'INT'],
            'start_date'   => ['type' => 'DATE'],
            'end_date'     => ['type' => 'DATE'],
            'max_limit'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'current_spent'=> ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'status'       => ['type' => 'ENUM', 'constraint' => ['open', 'locked'], 'default' => 'open'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_weekly_budgets');

        // 12. SUPPLIER & LOGISTIK
        $this->forge->addField([
            'id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'    => ['type' => 'VARCHAR', 'constraint' => 150],
            'address' => ['type' => 'TEXT', 'null' => true],
            'phone'   => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_suppliers');

        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'ingredient_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'price'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'product_snapshot' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'updated_at'       => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_supplier_prices');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'workplace_id'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'ingredient_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'stock_qty'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('db_inventory');

        $this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'inventory_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type'         => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
            'qty'          => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'created_at'   => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_inventory_logs');

        // 13. DISTRIBUSI & AUDIT
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'beneficiary_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'courier_name'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'proof_of_delivery' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'geo_location'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'            => ['type' => 'ENUM', 'constraint' => ['packing', 'on_way', 'delivered'], 'default' => 'packing'],
            'arrival_time'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_delivery_orders');

        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'action'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'table_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'old_value'  => ['type' => 'JSON', 'null' => true],
            'new_value'  => ['type' => 'JSON', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_audit_logs');
    }

    public function down()
    {
        // Drop tables in reverse order to avoid FK issues
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
    }
}