<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Permission::create(['name' => 'add_to_box', 'description' => 'إضافة إلى الصندوق']);
    Permission::create(['name' => 'show_boxes', 'description' => 'عرض الصناديق']);
    Permission::create(['name' => 'add_currencies', 'description' => 'إضافة عملات']);
    Permission::create(['name' => 'show_currencies', 'description' => 'عرض العملات']);
    Permission::create(['name' => 'add_import_ainiats', 'description' => 'إضافة عينيات واردة']);
    Permission::create(['name' => 'show_import_ainiats', 'description' => 'عرض عينيات واردة']);
    Permission::create(['name' => 'add_customers', 'description' => 'إضافة مستفيدين']);
    Permission::create(['name' => 'show_customers', 'description' => 'عرض مستفيدين']);
    Permission::create(['name' => 'add_mosques', 'description' => 'اضافة مسجد']);
    Permission::create(['name' => 'show_mosques', 'description' => 'عرض المساجد']);
    Permission::create(['name' => 'add_expenses', 'description' => 'إضافة مصاريف']);
    Permission::create(['name' => 'show_expenses', 'description' => 'عرض مصاريف']);
    Permission::create(['name' => 'add_products', 'description' => 'إضافة عينيات']);
    Permission::create(['name' => 'show_products', 'description' => 'عرض عينيات']);
    Permission::create(['name' => 'edit_products', 'description' => 'تعديل عينيات']);
    Permission::create(['name' => 'add_providers', 'description' => 'إضافة داعمون']);
    Permission::create(['name' => 'show_providers', 'description' => 'عرض داعمون']);
    Permission::create(['name' => 'add_salaries', 'description' => 'إضافة رواتب']);
    Permission::create(['name' => 'show_salaries', 'description' => 'عرض رواتب']);
    Permission::create(['name' => 'add_sanadat_qapds', 'description' => 'إضافة سندات قبض']);
    Permission::create(['name' => 'show_sanadat_qapds', 'description' => 'عرض سندات قبض']);
    Permission::create(['name' => 'add_sanadat_sarfs', 'description' => 'إضافة سندات صرف']);
    Permission::create(['name' => 'show_sanadat_sarfs', 'description' => 'عرض سندات صرف']);
    Permission::create(['name' => 'add_export_ainiats', 'description' => 'إضافة عينيات صادرة']);
    Permission::create(['name' => 'show_export_ainiats', 'description' => 'عرض عينيات صادرة']);
    Permission::create(['name' => 'add_workers', 'description' => 'إضافة موظفين']);
    Permission::create(['name' => 'show_workers', 'description' => 'عرض موظفين']);
    Permission::create(['name' => 'add_selectives', 'description' => 'اضافة مرشحين']);
    Permission::create(['name' => 'show_selectives', 'description' => 'عرض مرشحين']);
  }
}
