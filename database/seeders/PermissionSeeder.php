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
    $permissions = [
      // boxes
      ['name' => 'add_to_box', 'description' => 'إضافة إلى الصندوق'],
      ['name' => 'show_boxes', 'description' => 'عرض الصناديق'],
      // currencies
      ['name' => 'add_currencies', 'description' => 'إضافة عملات'],
      ['name' => 'show_currencies', 'description' => 'عرض العملات'],
      // import ainiat
      ['name' => 'add_import_ainiats', 'description' => 'إضافة عينيات واردة'],
      ['name' => 'show_import_ainiats', 'description' => 'عرض عينيات واردة'],
      // customers
      ['name' => 'add_customers', 'description' => 'إضافة زبونين'],
      ['name' => 'show_customers', 'description' => 'عرض زبونين'],
      // mosques
      ['name' => 'add_mosques', 'description' => 'اضافة مسجد'],
      ['name' => 'show_mosques', 'description' => 'عرض المساجد'],
      // expenses
      ['name' => 'add_expenses', 'description' => 'إضافة مصاريف'],
      ['name' => 'show_expenses', 'description' => 'عرض مصاريف'],
      // products
      ['name' => 'add_products', 'description' => 'إضافة عينيات'],
      ['name' => 'show_products', 'description' => 'عرض عينيات'],
      ['name' => 'edit_products', 'description' => 'تعديل عينيات'],
      // providers
      ['name' => 'add_providers', 'description' => 'إضافة داعمون'],
      ['name' => 'show_providers', 'description' => 'عرض داعمون'],
      // salaries
      ['name' => 'add_salaries', 'description' => 'إضافة رواتب'],
      ['name' => 'show_salaries', 'description' => 'عرض رواتب'],
      // sanadat qapd
      ['name' => 'add_sanadat_qapds', 'description' => 'إضافة سندات قبض'],
      ['name' => 'show_sanadat_qapds', 'description' => 'عرض سندات قبض'],
      // sanadat sarf
      ['name' => 'add_sanadat_sarfs', 'description' => 'إضافة سندات صرف'],
      ['name' => 'show_sanadat_sarfs', 'description' => 'عرض سندات صرف'],
      // export ainiat
      ['name' => 'add_export_ainiats', 'description' => 'إضافة عينيات صادرة'],
      ['name' => 'show_export_ainiats', 'description' => 'عرض عينيات صادرة'],
      // workers
      ['name' => 'add_workers', 'description' => 'إضافة موظفين'],
      ['name' => 'show_workers', 'description' => 'عرض موظفين'],
      // selectives
      ['name' => 'add_selectives', 'description' => 'اضافة مرشحين'],
      ['name' => 'show_selectives', 'description' => 'عرض مرشحين'],
      // kafeels
      ['name' => 'add_kafeels', 'description' => 'اضافة كفيل'],
      ['name' => 'show_kafeels', 'description' => 'عرض كفيل'],
      // orphans
      ['name' => 'add_orphans', 'description' => 'اضافة يتامى'],
      ['name' => 'show_orphans', 'description' => 'عرض يتامى'],
      // wasis
      ['name' => 'add_wasis', 'description' => 'اضافة وصي'],
      ['name' => 'show_wasis', 'description' => 'عرض وصي'],
      // sms
      ['name' => 'add_sms', 'description' => 'اضافة رسائل'],
      ['name' => 'show_sms', 'description' => 'عرض رسائل'],
      // movements
      ['name' => 'show_movements', 'description' => 'عرض الحركات المالية'],
    ];

    foreach ($permissions as $permission) {
      Permission::firstOrCreate(['name' => $permission['name']], $permission);
    }
  }
}
