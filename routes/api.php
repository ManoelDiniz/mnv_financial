<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Transaction\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Rotas Públicas
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Tipos e enums públicos
    Route::get('/account-types', [AccountController::class, 'types']);
    Route::get('/transaction-types', [TransactionController::class, 'types']);
    Route::get('/category-types', [CategoryController::class, 'types']);

    // Rotas Protegidas
    Route::middleware(['auth:sanctum'])->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // User Profile
        Route::get('/user', [UserController::class, 'show']);
        Route::put('/user', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);
        Route::post('/user/profile-image', [UserController::class, 'updateProfileImage']);
        Route::delete('/user/profile-image', [UserController::class, 'deleteProfileImage']);

        // Accounts
        Route::apiResource('accounts', AccountController::class);
        Route::get('accounts/{account}/balance-history', [AccountController::class, 'balanceHistory']);

        // Transactions
        Route::apiResource('transactions', TransactionController::class);
        Route::post('transactions/bulk', [TransactionController::class, 'bulkStore']);
        Route::get('transactions/recent', [TransactionController::class, 'recent']);
        Route::get('accounts/{account}/transactions', [TransactionController::class, 'byAccount']);
        Route::get('categories/{category}/transactions', [TransactionController::class, 'byCategory']);
        Route::get('transactions/export', [TransactionController::class, 'export']);

        // Categories
        Route::apiResource('categories', CategoryController::class);
        Route::get('categories/{type}/list', [CategoryController::class, 'byType']);

        // Reports
        Route::get('reports/balance-summary', [ReportController::class, 'balanceSummary']);
        Route::get('reports/cashflow', [ReportController::class, 'cashFlow']);
        Route::get('reports/expenses-by-category', [ReportController::class, 'expensesByCategory']);
        Route::get('reports/income-vs-expenses', [ReportController::class, 'incomeVsExpenses']);
        Route::get('reports/monthly-summary', [ReportController::class, 'monthlySummary']);

        // Dashboard
        Route::get('/dashboard/stats', [ReportController::class, 'dashboardStats']);
        Route::get('/dashboard/upcoming-bills', [ReportController::class, 'upcomingBills']);
    });
});
