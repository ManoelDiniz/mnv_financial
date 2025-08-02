<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $account->user);
        $this->assertEquals($user->id, $account->user->id);
    }

    public function test_account_has_formatted_balance(): void
    {
        $account = Account::factory()->create(['balance' => 1234.56]);

        $this->assertEquals('R$ 1.234,56', $account->formatted_balance);
    }

    public function test_account_has_type_name(): void
    {
        $account = Account::factory()->create(['type' => 'checking']);

        $this->assertEquals('Conta Corrente', $account->type_name);
    }

    public function test_active_scope_works(): void
    {
        Account::factory()->create(['is_active' => true]);
        Account::factory()->create(['is_active' => false]);

        $activeAccounts = Account::active()->get();

        $this->assertCount(1, $activeAccounts);
        $this->assertTrue($activeAccounts->first()->is_active);
    }
}
