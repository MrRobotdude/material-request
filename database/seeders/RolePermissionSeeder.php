<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat Role
        $roles = [
            'Admin',
            'Purchasing',
            'Operational',
            'Marketing',
            'Warehouse',
            'Finance',
            'User',
        ];

        $roleInstances = [];
        foreach ($roles as $role) {
            $roleInstances[$role] = Role::create(['name' => $role]);
        }

        // Buat Permissions
        $permissions = [
            'change_password',
            'view_accounts_page',
            'add_account',
            'edit_account',
            'manage_account_status',
            'manage_all_accounts',
            'view_roles_page',
            'add_roles',
            'edit_roles',
            'delete_roles',
            'manage_all_roles',
            'view_brands_page',
            'add_brand',
            'edit_brand',
            'manage_brand_status',
            'manage_all_brand',
            'view_products_page',
            'add_product',
            'edit_product',
            'manage_product_status',
            'manage_all_product',
            'manage_all_types',
            'view_types_page',
            'add_type',
            'edit_type',
            'manage_type_status',
            'manage_all_subtypes',
            'view_subtypes_page',
            'add_subtype',
            'edit_subtype',
            'manage_subtype_status',
            'view_specifications_page',
            'add_specification',
            'edit_specification',
            'manage_specification_status',
            'manage_all_specifications',
            'manage_all_brand_product_specification',
            'view_brand_product_specification',
            'add_brand_product_specification',
            'edit_brand_product_specification',
            'manage_brand_product_specification_status',
            'view_items_page',
            'add_item',
            'edit_item',
            'manage_item_status',
            'manage_all_items',
            'view_material_requests_page',
            'add_material_request',
            'edit_material_request',
            'approve_material_request',
            'cancel_material_request',
            'view_material_request_items',
            'manage_all_material_requests',
            'manage_all_projects',
            'view_projects_page',
            'add_project',
            'edit_project',
            'delete_project',
        ];

        $permissionInstances = [];
        foreach ($permissions as $permission) {
            $permissionInstances[$permission] = Permission::create(['name' => $permission]);
        }

        // Distribusikan Permission ke Role
        // Admin
        $roleInstances['Admin']->permissions()->attach([
            $permissionInstances['change_password']->id,
            $permissionInstances['manage_all_accounts']->id,
            $permissionInstances['manage_all_roles']->id,
            $permissionInstances['manage_all_brand']->id,
            $permissionInstances['manage_all_product']->id,
            $permissionInstances['manage_all_types']->id,
            $permissionInstances['manage_all_subtypes']->id,
            $permissionInstances['manage_all_specifications']->id,
            $permissionInstances['manage_all_brand_product_specification']->id,
            $permissionInstances['manage_all_items']->id,
            $permissionInstances['manage_all_material_requests']->id,
            $permissionInstances['manage_all_projects']->id,
        ]);

        // Purchasing
        $roleInstances['Purchasing']->permissions()->attach([
            $permissionInstances['view_material_requests_page']->id,
            $permissionInstances['add_material_request']->id,
            $permissionInstances['edit_material_request']->id,
            $permissionInstances['approve_material_request']->id,
            $permissionInstances['cancel_material_request']->id,
            $permissionInstances['view_material_request_items']->id,
        ]);

        // Operational
        $roleInstances['Operational']->permissions()->attach([
            $permissionInstances['view_material_requests_page']->id,
            $permissionInstances['add_material_request']->id,
            $permissionInstances['edit_material_request']->id,
            $permissionInstances['approve_material_request']->id,
            $permissionInstances['cancel_material_request']->id,
            $permissionInstances['view_material_request_items']->id,
        ]);

        // Marketing
        $roleInstances['Marketing']->permissions()->attach([
            $permissionInstances['view_brands_page']->id,
            $permissionInstances['add_brand']->id,
            $permissionInstances['edit_brand']->id,
            $permissionInstances['view_products_page']->id,
            $permissionInstances['add_product']->id,
            $permissionInstances['edit_product']->id,
        ]);

        // Warehouse
        $roleInstances['Warehouse']->permissions()->attach([
            $permissionInstances['view_material_requests_page']->id,
            $permissionInstances['add_material_request']->id,
            $permissionInstances['edit_material_request']->id,
            $permissionInstances['approve_material_request']->id,
            $permissionInstances['cancel_material_request']->id,
            $permissionInstances['view_material_request_items']->id,
        ]);

        // Finance
        $roleInstances['Finance']->permissions()->attach([
            $permissionInstances['view_material_requests_page']->id,
            $permissionInstances['manage_all_material_requests']->id,
            $permissionInstances['approve_material_request']->id,
        ]);

        // User
        $roleInstances['User']->permissions()->attach([
            $permissionInstances['view_material_requests_page']->id,
            $permissionInstances['add_material_request']->id,
            $permissionInstances['view_material_request_items']->id,
        ]);

        echo "Permissions successfully distributed to roles.\n";
    }
}
