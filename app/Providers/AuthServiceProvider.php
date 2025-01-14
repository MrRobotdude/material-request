<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Array izin dengan nama Gate dan izin yang sesuai
        $permissions = [
            'change_password' => 'change_password',
            'manage_all_accounts' => 'manage_all_accounts',
            'view_accounts_page' => 'view_accounts_page',
            'manage_all_roles' => 'manage_all_roles',
            'view_roles_page' => 'view_roles_page',
            'manage_all_brand' => 'manage_all_brand',
            'view_brands_page' => 'view_brands_page',
            'manage_all_product' => 'manage_all_product',
            'view_products_page' => 'view_products_page',
            'manage_all_types' => 'manage_all_types',
            'view_types_page' => 'view_types_page',
            'manage_all_subtypes' => 'manage_all_subtypes',
            'view_subtypes_page' => 'view_subtypes_page',
            'manage_all_specifications' => 'manage_all_specifications',
            'view_specifications_page' => 'view_specifications_page',
            'manage_all_items' => 'manage_all_items',
            'view_items_page' => 'view_items_page',
            'manage_all_material_requests' => 'manage_all_material_requests',
            'view_material_requests_page' => 'view_material_requests_page',
            'manage_all_brand_product_specification' => 'manage_all_brand_product_specification',
            'view_brand_product_specification' => 'view_brand_product_specification',
            'manage_all_projects' => 'manage_all_projects',
            'view_projects_page' => 'view_projects_page',
        ];

        // Iterasi untuk mendefinisikan Gate
        foreach ($permissions as $gate => $permission) {
            Gate::define($gate, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
