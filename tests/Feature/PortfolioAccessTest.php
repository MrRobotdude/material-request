<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_the_dashboard()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_manage_accounts()
    {
        $user = User::create([
            'user_id' => 'DEMO001', 'name' => 'Demo User',
            'username' => 'demo', 'password' => bcrypt('demo-password'),
        ]);
        $this->actingAs($user)->get('/account-management')->assertForbidden();
    }

    public function test_demo_seed_preserves_fulfilment_bounds()
    {
        $this->seed();
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->assertTrue($admin->hasRole('Admin'));
        $this->actingAs($admin)->get('/account-management')->assertOk();
        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->get('/dashboard/months/' . now()->year)->assertOk();
        $this->get('/dashboard/monthly-status-chart/' . now()->year)->assertOk()
            ->assertJsonCount(12, 'labels')->assertJsonCount(5, 'datasets');
        $this->get('/dashboard/monthly-status-chart/invalid')->assertStatus(400);
        $this->assertDatabaseCount('material_requests', 200);
        $this->assertSame(0, \Illuminate\Support\Facades\DB::table('material_request_items')
            ->whereColumn('fulfilled_quantity', '>', 'quantity')->count());
        $this->assertSame(0, \Illuminate\Support\Facades\DB::table('material_request_items')
            ->where('status', 'partial')->where(function ($query) {
                $query->where('fulfilled_quantity', '<=', 0)
                    ->orWhereColumn('fulfilled_quantity', '>=', 'quantity');
            })->count());
    }
}
