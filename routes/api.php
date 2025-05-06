<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui você pode registrar as rotas da API para sua aplicação. Essas rotas
| são carregadas pelo RouteServiceProvider e todas serão atribuídas ao grupo
| de middleware "api". Faça algo incrível!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Status
use App\Http\Controllers\Api\StatusController;
Route::apiResource('statuses', StatusController::class);

// User
use App\Http\Controllers\Api\UserController;
Route::apiResource('users', UserController::class);

// Page
use App\Http\Controllers\Api\PageController;
Route::apiResource('pages', PageController::class);

// Service
use App\Http\Controllers\Api\ServiceController;
Route::apiResource('services', ServiceController::class);

// Portfolio
use App\Http\Controllers\Api\PortfolioController;
Route::apiResource('portfolios', PortfolioController::class);

// Integration
use App\Http\Controllers\Api\IntegrationController;
Route::apiResource('integrations', IntegrationController::class);

// Testimonial
use App\Http\Controllers\Api\TestimonialController;
Route::apiResource('testimonials', TestimonialController::class);

// Slider
use App\Http\Controllers\Api\SliderController;
Route::apiResource('sliders', SliderController::class);

// UserGroup
use App\Http\Controllers\Api\UserGroupController;
Route::apiResource('usergroups', UserGroupController::class);

// Permission
use App\Http\Controllers\Api\PermissionController;
Route::apiResource('permissions', PermissionController::class);

// Invoice
use App\Http\Controllers\Api\InvoiceController;
Route::apiResource('invoices', InvoiceController::class);

// Transaction
use App\Http\Controllers\Api\TransactionController;
Route::apiResource('transactions', TransactionController::class);

// BankAccount
use App\Http\Controllers\Api\BankAccountController;
Route::apiResource('bank_account', BankAccountController::class);

// Customer
use App\Http\Controllers\Api\CustomerController;
Route::get('customers/{id}/addresses', [CustomerController::class, 'getAddresses']);
Route::post('customers/{id}/addresses', [CustomerController::class, 'storeAddress']);
Route::put('customers/{id}/addresses/{addressId}', [CustomerController::class, 'updateAddress']);
Route::delete('customers/{id}/addresses/{addressId}', [CustomerController::class, 'destroyAddress']);
Route::post('customers/{id}/addresses/{addressId}/default', [CustomerController::class, 'setDefaultAddress']);
Route::get('customers/{id}/preferences', [CustomerController::class, 'getPreferences']);
Route::put('customers/{id}/preferences', [CustomerController::class, 'updatePreferences']);
Route::apiResource('customers', CustomerController::class);

// UserService
use App\Http\Controllers\Api\UserServiceController;
Route::post('user-services/{id}/generate-invoice', [UserServiceController::class, 'generateInvoice']);
Route::apiResource('user-services', UserServiceController::class);

// Log
use App\Http\Controllers\Api\LogController;
Route::get('logs', [LogController::class, 'index']);
Route::post('logs/clear', [LogController::class, 'clear']);
Route::get('logs/download', [LogController::class, 'download']);

// Dashboard e configurações
use App\Http\Controllers\Api\DashboardController;
Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('settings', [DashboardController::class, 'getSettings']);
Route::post('settings', [DashboardController::class, 'updateSettings']);
Route::post('settings/update-images', [DashboardController::class, 'updateImages']);

// Commander para gerenciamento de CRUD
use App\Http\Controllers\Api\CommanderController;
Route::post('commander/create', [CommanderController::class, 'create']);
Route::post('commander/migrate', [CommanderController::class, 'migrate']);

// Importação de dados
use App\Http\Controllers\Api\ImportController;
Route::post('import/data', [ImportController::class, 'importData']);

// Category
use App\Http\Controllers\Api\CategoryController;

Route::apiResource('categories', CategoryController::class);

// Product
use App\Http\Controllers\Api\ProductController;

Route::get('products/search', [ProductController::class, 'search']);
Route::get('products/recent', [ProductController::class, 'recent']);
Route::get('products/promotions', [ProductController::class, 'promotions']);
Route::post('products/format-whatsapp', [ProductController::class, 'formatWhatsAppMessage']);
Route::apiResource('products', ProductController::class);

// Facebook Catalog
use App\Http\Controllers\Api\FacebookCatalogController;

Route::post('facebook/sync-catalog', [FacebookCatalogController::class, 'syncCatalog']);
Route::post('facebook/sync-product/{productId}', [FacebookCatalogController::class, 'syncProduct']);
