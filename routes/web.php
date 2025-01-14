<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Management\BrandProductSpecificationController;
use App\Http\Controllers\Management\ItemManagementController;
use App\Http\Controllers\MaterialRequest\ProjectManagementController;
use App\Http\Controllers\MaterialRequest\WarehouseLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AccountManagementController,
    RoleManagementController
};
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Management\{
    BrandManagementController,
    ProductManagementController,
    SpecificationManagementController,
    SubTypeManagementController,
    TypeManagementController
};
use App\Http\Controllers\MaterialRequest\MaterialRequestController;

Auth::routes();

Route::get('/', function () {
    return auth()->check() ? redirect()->route('material-request.index') : redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/months/{year}', [DashboardController::class, 'getAvailableMonths']);
    Route::get('/dashboard/chart/{year}/{month}', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard/monthly-status-chart/{year}', [DashboardController::class, 'getMonthlyStatusChart']);
    Route::get('/dashboard/top-items/{year}/{month}', [DashboardController::class, 'getTopItems']);
    Route::get('/dashboard/top-projects/{year}/{month}', [DashboardController::class, 'getTopProjects']);


    Route::prefix('item-management')->middleware('permission:view_items_page')->group(function () {
        Route::get('/', [ItemManagementController::class, 'index'])->name('item-management.index');
        Route::get('/create', [ItemManagementController::class, 'create'])
            ->middleware('permission:add_item')->name('item-management.create');
        Route::post('/', [ItemManagementController::class, 'store'])
            ->middleware('permission:add_item')->name('item-management.store');
        Route::get('/{item}/edit', [ItemManagementController::class, 'edit'])
            ->middleware('permission:edit_item')->name('item-management.edit');
        Route::put('/{item}', [ItemManagementController::class, 'update'])
            ->middleware('permission:edit_item')->name('item-management.update');
        Route::put('/{item}/toggle-status', [ItemManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_item_status')->name('item-management.toggle-status');
    });

    // Brand-Product-Specification Management
    Route::prefix('brand-product-specification')->middleware('permission:view_brand_product_specification')->group(function () {
        Route::get('/', [BrandProductSpecificationController::class, 'index'])
            ->name('brand-product-specification.index');
        Route::get('/create', [BrandProductSpecificationController::class, 'create'])
            ->middleware('permission:add_brand_product_specification')
            ->name('brand-product-specification.create');
        Route::post('/', [BrandProductSpecificationController::class, 'store'])
            ->middleware('permission:add_brand_product_specification')
            ->name('brand-product-specification.store');
        Route::get('/{id}/edit', [BrandProductSpecificationController::class, 'edit'])
            ->middleware('permission:edit_brand_product_specification')
            ->name('brand-product-specification.edit');
        Route::put('/{id}', [BrandProductSpecificationController::class, 'update'])
            ->middleware('permission:edit_brand_product_specification')
            ->name('brand-product-specification.update');
        Route::put('/{id}/toggle-status', [BrandProductSpecificationController::class, 'toggleStatus'])
            ->name('brand-product-specification.toggle-status')
            ->middleware('permission:manage_brand_product_specification_status');
    });

    // Specification Management
    Route::prefix('specification-management')->middleware('permission:view_specifications_page')->group(function () {
        Route::get('/', [SpecificationManagementController::class, 'index'])->name('specification-management.index');
        Route::get('/create', [SpecificationManagementController::class, 'create'])
            ->middleware('permission:add_specification')->name('specification-management.create');
        Route::post('/', [SpecificationManagementController::class, 'store'])
            ->middleware('permission:add_specification')->name('specification-management.store');
        Route::get('/{specification}/edit', [SpecificationManagementController::class, 'edit'])
            ->middleware('permission:edit_specification')->name('specification-management.edit');
        Route::put('/{specification}', [SpecificationManagementController::class, 'update'])
            ->middleware('permission:edit_specification')->name('specification-management.update');
        Route::put('/{specification}/toggle-status', [SpecificationManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_specification_status')->name('specification-management.toggleStatus');
    });

    // Type Management
    Route::prefix('type-management')->middleware('permission:view_types_page')->group(function () {
        Route::get('/', [TypeManagementController::class, 'index'])->name('type-management.index');
        Route::get('/create', [TypeManagementController::class, 'create'])
            ->middleware('permission:add_type')->name('type-management.create');
        Route::post('/', [TypeManagementController::class, 'store'])
            ->middleware('permission:add_type')->name('type-management.store');
        Route::get('/{type}/edit', [TypeManagementController::class, 'edit'])
            ->middleware('permission:edit_type')->name('type-management.edit');
        Route::put('/{type}', [TypeManagementController::class, 'update'])
            ->middleware('permission:edit_type')->name('type-management.update');
        Route::put('/{type}/toggle-status', [TypeManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_type_status')->name('type-management.toggle-status');
    });

    // SubType Management
    Route::prefix('sub-type-management')->middleware('permission:view_subtypes_page')->group(function () {
        Route::get('/', [SubTypeManagementController::class, 'index'])->name('subtype-management.index');
        Route::get('/create', [SubTypeManagementController::class, 'create'])
            ->middleware('permission:add_subtype')->name('subtype-management.create');
        Route::post('/', [SubTypeManagementController::class, 'store'])
            ->middleware('permission:add_subtype')->name('subtype-management.store');
        Route::get('/{subType}/edit', [SubTypeManagementController::class, 'edit'])
            ->middleware('permission:edit_subtype')->name('subtype-management.edit');
        Route::put('/{subType}', [SubTypeManagementController::class, 'update'])
            ->middleware('permission:edit_subtype')->name('subtype-management.update');
        Route::put('/{subType}/toggle-status', [SubTypeManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_subtype_status')->name('subtype-management.toggle-status');
        Route::delete('/{subType}', [SubTypeManagementController::class, 'destroy'])
            ->middleware('permission:delete_subtype')->name('subtype-management.destroy');
    });

    // Product Management
    Route::prefix('product-management')->middleware('permission:view_products_page')->group(function () {
        Route::get('/', [ProductManagementController::class, 'index'])->name('product-management.index');
        Route::get('/create', [ProductManagementController::class, 'create'])
            ->middleware('permission:add_product')->name('product-management.create');
        Route::post('/', [ProductManagementController::class, 'store'])
            ->middleware('permission:add_product')->name('product-management.store');
        Route::get('/{product}/edit', [ProductManagementController::class, 'edit'])
            ->middleware('permission:edit_product')->name('product-management.edit');
        Route::put('/{product}', [ProductManagementController::class, 'update'])
            ->middleware('permission:edit_product')->name('product-management.update');
        Route::put('/{product}/toggle-status', [ProductManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_product_status')->name('product-management.toggle-status');
    });

    // Brand Management
    Route::prefix('brand-management')->middleware('permission:view_brands_page')->group(function () {
        Route::get('/', [BrandManagementController::class, 'index'])->name('brand-management.index');
        Route::get('/create', [BrandManagementController::class, 'create'])
            ->middleware('permission:add_brand')->name('brand-management.create');
        Route::post('/', [BrandManagementController::class, 'store'])
            ->middleware('permission:add_brand')->name('brand-management.store');
        Route::get('/{brand}/edit', [BrandManagementController::class, 'edit'])
            ->middleware('permission:edit_brand')->name('brand-management.edit');
        Route::put('/{brand}', [BrandManagementController::class, 'update'])
            ->middleware('permission:edit_brand')->name('brand-management.update');
        Route::put('/{brand}/toggle-status', [BrandManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_brand_status')->name('brand-management.toggle-status');
    });

    // Role Management
    Route::prefix('role-management')->middleware('permission:view_roles_page')->group(function () {
        Route::get('/', [RoleManagementController::class, 'index'])->name('role-management.index');
        Route::get('/create', [RoleManagementController::class, 'create'])
            ->middleware('permission:add_roles')->name('role-management.create');
        Route::post('/', [RoleManagementController::class, 'store'])
            ->middleware('permission:add_roles')->name('role-management.store');
        Route::get('/{role}/edit', [RoleManagementController::class, 'edit'])
            ->middleware('permission:edit_roles')->name('role-management.edit');
        Route::put('/{role}', [RoleManagementController::class, 'update'])
            ->middleware('permission:edit_roles')->name('role-management.update');
        Route::delete('/{role}', [RoleManagementController::class, 'destroy'])
            ->middleware('permission:delete_roles')->name('role-management.destroy');
    });

    // Account Management
    Route::prefix('account-management')->middleware('permission:view_accounts_page')->group(function () {
        Route::get('/', [AccountManagementController::class, 'index'])->name('account-management.index');
        Route::get('/create', [AccountManagementController::class, 'create'])
            ->middleware('permission:add_account')->name('account-management.create');
        Route::post('/', [AccountManagementController::class, 'store'])
            ->middleware('permission:add_account')->name('account-management.store');
        Route::get('/{user}/edit', [AccountManagementController::class, 'edit'])
            ->middleware('permission:edit_account')->name('account-management.edit');
        Route::put('/{user}', [AccountManagementController::class, 'update'])
            ->middleware('permission:edit_account')->name('account-management.update');
        Route::put('/{user}/toggle-status', [AccountManagementController::class, 'toggleStatus'])
            ->middleware('permission:manage_account_status')->name('account-management.toggle-status');
        Route::get('/{user}/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])
            ->middleware('can-change-password')->name('account-management.change-password');
        Route::post('/{user}/change-password', [ChangePasswordController::class, 'changePassword'])
            ->middleware('can-change-password')->name('account-management.update-password');
    });

    Route::prefix('material-requests')->middleware('permission:view_material_requests_page')->group(function () {
        Route::get('/', [MaterialRequestController::class, 'index'])->name('material-request.index');
        Route::get('/{mr_code}/items', [MaterialRequestController::class, 'getItems'])
            ->middleware('permission:view_material_request_items');
        Route::get('/create', [MaterialRequestController::class, 'create'])
            ->middleware('permission:add_material_request')->name('material-request.create');
        Route::post('/store', [MaterialRequestController::class, 'store'])
            ->middleware('permission:add_material_request')->name('material-request.store');
        Route::get('/{mrCode}/edit', [MaterialRequestController::class, 'edit'])
            ->middleware('permission:edit_material_request')->name('material-request.edit');
        Route::put('/{mrCode}/update', [MaterialRequestController::class, 'update'])
            ->middleware('permission:edit_material_request')->name('material-request.update');
        Route::post('/{mr_code}/items/{item_id}/cancel', [MaterialRequestController::class, 'cancelItem'])
            ->middleware('permission:cancel_material_request_item')->name('material-request.cancel-item');
        Route::post('/{mrCode}/cancel', [MaterialRequestController::class, 'cancel'])
            ->middleware('permission:cancel_material_request')->name('material-request.cancel');
        Route::post('/{mrCode}/items/{item_id}/release', [MaterialRequestController::class, 'release'])
            ->middleware('permission:release_material_request_items')->name('material-request.release');
        Route::post('/{mrCode}/approve', [MaterialRequestController::class, 'approve'])
            ->middleware('permission:approve_material_request')->name('material-request.approve');
    });

    Route::prefix('project-management')->middleware('permission:view_projects_page')->group(function () {
        Route::get('/', [ProjectManagementController::class, 'index'])->name('project-management.index');
        Route::get('/create', [ProjectManagementController::class, 'create'])
            ->middleware('permission:add_project')->name('project-management.create');
        Route::post('/', [ProjectManagementController::class, 'store'])
            ->middleware('permission:add_project')->name('project-management.store');
        Route::get('/{project}/edit', [ProjectManagementController::class, 'edit'])
            ->middleware('permission:edit_project')->name('project-management.edit');
        Route::put('/{project}', [ProjectManagementController::class, 'update'])
            ->middleware('permission:edit_project')->name('project-management.update');
        Route::delete('/{project}', [ProjectManagementController::class, 'destroy'])
            ->middleware('permission:delete_project')->name('project-management.destroy');
    });


    Route::prefix('warehouse-logs')->middleware('auth')->group(function () {
        Route::get('/', [WarehouseLogController::class, 'index'])->name('warehouse-logs.index');
    });

});
