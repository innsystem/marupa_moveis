<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Site\AuthController;
use App\Http\Controllers\Site\WebhookController;

// Group Site
Route::controller(SiteController::class)->group(function () {
    Route::get('/', 'index')->name('site.index');

    Route::get('/pages/{slug}', 'pageShow')->name('site.pages.show');
    Route::get('/services/{slug}', 'serviceShow')->name('site.services.show');
    // Rotas de projetos
    Route::get('/projetos', 'projectsIndex')->name('site.projects.index');
    Route::get('/projetos/{slug}', 'projectsShow')->name('site.projects.show');
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login/post', 'post')->name('login.post');
    Route::post('/logout', 'logout')->name('logout');

    Route::get('/recuperar-senha', 'passwordRecovery')->name('auth.passwordRecovery');
    Route::post('/recuperar-senha/post', 'passwordRecoveryPost')->name('auth.passwordRecovery.post');

    Route::get('/redefinir-senha', 'passwordReset')->name('auth.passwordReset');
    Route::post('/redefinir-senha/post', 'passwordResetPost')->name('auth.passwordReset.post');
});

Route::controller(WebhookController::class)->prefix('webhook')->group(function () {
    Route::post('/mercadopago', 'mercadopago')->name('webhook.mercadopago');
    Route::get('/invoices/{invoice_id}/check-payment-status', 'checkPaymentStatus')->name('webhook.invoices.checkPaymentStatus');
    Route::post('/integration/shopee/webhook', 'shopeeWebhook')->name('webhook.integrations.shopee.webhook');
    Route::post('/integration/mercado-livre/webhook', 'mercadoLivreWebhook')->name('webhook.integrations.mercadoLivre.webhook');
});

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Controllers\Admin\CommanderAdminController;
use App\Http\Controllers\Admin\StatusesController;
use App\Http\Controllers\Admin\UserGroupsController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\PortfoliosController;
use App\Http\Controllers\Admin\IntegrationsController;
use App\Http\Controllers\Admin\TestimonialsController;
use App\Http\Controllers\Admin\SlidersController;
use App\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Admin\TransactionsController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\IntegrationCategoriesController;
use App\Http\Controllers\Admin\IntegrationsPlaygroundController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProductsController;

// Group Admin
Route::prefix('admin')->group(function () {
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::controller(BaseAdminController::class)->group(function () {
            Route::get('/', 'index')->name('admin.index')->middleware('permission:admin.index');
            Route::get('/settings', 'settings')->name('admin.settings')->middleware('permission:admin.settings');
            Route::post('/settings/update', 'settingsUpdate')->name('admin.settings.update')->middleware('permission:admin.settings.update');
            Route::post('/settings/update-images', 'updateImages')->name('admin.settings.updateImages')->middleware('permission:admin.settings.updateImages');
        });

        Route::controller(CommanderAdminController::class)->group(function () {
            Route::get('/commander', 'index')->name('admin.commander')->middleware('permission:admin.commander');
            Route::post('/commander/create', 'create')->name('admin.commander.create')->middleware('permission:admin.commander.create');
            Route::post('/commander/migrate', 'migrate')->name('admin.commander.migrate')->middleware('permission:admin.commander.migrate');
        });

        Route::prefix('statuses')->controller(StatusesController::class)->group(function () {
            Route::get('/', 'index')->name('admin.statuses.index')->middleware('permission:admin.statuses.index');
            Route::get('/load', 'load')->name('admin.statuses.load')->middleware('permission:admin.statuses.load');
            Route::get('/create', 'create')->name('admin.statuses.create')->middleware('permission:admin.statuses.create');
            Route::post('/store', 'store')->name('admin.statuses.store')->middleware('permission:admin.statuses.store');
            Route::get('/{id}/edit', 'edit')->name('admin.statuses.edit')->middleware('permission:admin.statuses.edit');
            Route::post('/{id}/update', 'update')->name('admin.statuses.update')->middleware('permission:admin.statuses.update');
            Route::post('/{id}/delete', 'delete')->name('admin.statuses.delete')->middleware('permission:admin.statuses.delete');
        });

        Route::prefix('user_groups')->controller(UserGroupsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.user_groups.index')->middleware('permission:admin.user_groups.index');
            Route::get('/load', 'load')->name('admin.user_groups.load')->middleware('permission:admin.user_groups.load');
            Route::get('/create', 'create')->name('admin.user_groups.create')->middleware('permission:admin.user_groups.create');
            Route::post('/store', 'store')->name('admin.user_groups.store')->middleware('permission:admin.user_groups.store');
            Route::get('/{id}/edit', 'edit')->name('admin.user_groups.edit')->middleware('permission:admin.user_groups.edit');
            Route::post('/{id}/update', 'update')->name('admin.user_groups.update')->middleware('permission:admin.user_groups.update');
            Route::post('/{id}/delete', 'delete')->name('admin.user_groups.delete')->middleware('permission:admin.user_groups.delete');
        });

        Route::prefix('permissions')->controller(PermissionsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.permissions.index')->middleware('permission:admin.permissions.index');
            Route::get('/load', 'load')->name('admin.permissions.load')->middleware('permission:admin.permissions.load');
            Route::get('/create', 'create')->name('admin.permissions.create')->middleware('permission:admin.permissions.create');
            Route::post('/store', 'store')->name('admin.permissions.store')->middleware('permission:admin.permissions.store');
            Route::get('/{id}/edit', 'edit')->name('admin.permissions.edit')->middleware('permission:admin.permissions.edit');
            Route::post('/{id}/update', 'update')->name('admin.permissions.update')->middleware('permission:admin.permissions.update');
            Route::post('/{id}/delete', 'delete')->name('admin.permissions.delete')->middleware('permission:admin.permissions.delete');
        });

        Route::prefix('users')->controller(UsersController::class)->group(function () {
            Route::get('/', 'index')->name('admin.users.index')->middleware('permission:admin.users.index');
            Route::get('/load', 'load')->name('admin.users.load')->middleware('permission:admin.users.load');
            Route::get('/create', 'create')->name('admin.users.create')->middleware('permission:admin.users.create');
            Route::post('/store', 'store')->name('admin.users.store')->middleware('permission:admin.users.store');
            Route::get('/{id}/edit', 'edit')->name('admin.users.edit')->middleware('permission:admin.users.edit');
            Route::post('/{id}/update', 'update')->name('admin.users.update')->middleware('permission:admin.users.update');
            Route::post('/{id}/delete', 'delete')->name('admin.users.delete')->middleware('permission:admin.users.delete');
        });

        Route::prefix('customers')->controller(CustomersController::class)->group(function () {
            Route::get('/', 'index')->name('admin.customers.index')->middleware('permission:admin.customers.index');
            Route::get('/load', 'load')->name('admin.customers.load')->middleware('permission:admin.customers.load');
            Route::get('/create', 'create')->name('admin.customers.create')->middleware('permission:admin.customers.create');
            Route::post('/store', 'store')->name('admin.customers.store')->middleware('permission:admin.customers.store');
            Route::get('/{id}/show', 'show')->name('admin.customers.show')->middleware('permission:admin.customers.show');
            Route::get('/{id}/edit', 'edit')->name('admin.customers.edit')->middleware('permission:admin.customers.edit');
            Route::post('/{id}/update', 'update')->name('admin.customers.update')->middleware('permission:admin.customers.update');
            Route::post('/{id}/delete', 'delete')->name('admin.customers.delete')->middleware('permission:admin.customers.delete');

            // Rotas para endereços de clientes
            Route::get('/{id}/addresses', 'getAddresses')->name('admin.customers.addresses');
            Route::get('/{id}/addresses/create', 'createAddress')->name('admin.customers.addresses.create');
            Route::post('/{id}/addresses/store', 'storeAddress')->name('admin.customers.addresses.store');
            Route::get('/{id}/addresses/{addressId}/edit', 'editAddress')->name('admin.customers.addresses.edit');
            Route::post('/{id}/addresses/{addressId}/update', 'updateAddress')->name('admin.customers.addresses.update');
            Route::post('/{id}/addresses/{addressId}/delete', 'deleteAddress')->name('admin.customers.addresses.delete');
            Route::post('/{id}/addresses/{addressId}/set-default', 'setDefaultAddress')->name('admin.customers.addresses.setDefault');
            Route::post('/{id}/preferences/update', 'updatePreferences')->name('admin.customers.preferences.update');
        });

        // UserServices Routes - Serviços dos Clientes
        Route::get('/users/{userId}/services', [App\Http\Controllers\Admin\UserServicesController::class, 'index'])->name('admin.users.services');
        Route::get('/users/{userId}/services/load', [App\Http\Controllers\Admin\UserServicesController::class, 'load'])->name('admin.users.services.load');
        Route::get('/users/{userId}/services/create', [App\Http\Controllers\Admin\UserServicesController::class, 'create'])->name('admin.users.services.create');
        Route::post('/users/{userId}/services/store', [App\Http\Controllers\Admin\UserServicesController::class, 'store'])->name('admin.users.services.store');
        Route::get('/users/{userId}/services/{id}/edit', [App\Http\Controllers\Admin\UserServicesController::class, 'edit'])->name('admin.users.services.edit');
        Route::post('/users/{userId}/services/{id}/update', [App\Http\Controllers\Admin\UserServicesController::class, 'update'])->name('admin.users.services.update');
        Route::post('/users/{userId}/services/{id}/delete', [App\Http\Controllers\Admin\UserServicesController::class, 'delete'])->name('admin.users.services.delete');
        Route::post('/users/{userId}/services/{id}/generate-invoice', [App\Http\Controllers\Admin\UserServicesController::class, 'generateInvoice'])->name('admin.users.services.generate-invoice');

        // Nova rota para exibir todos os serviços de todos os clientes
        Route::get('/users/services', [App\Http\Controllers\Admin\UserServicesController::class, 'indexAll'])->name('admin.users.services.index')->middleware('permission:admin.users.services.index');

        // Nova rota para carregar os serviços de todos os clientes via AJAX
        Route::get('/users/services/load', [App\Http\Controllers\Admin\UserServicesController::class, 'loadAll'])->name('admin.users.services.loadAll')->middleware('permission:admin.users.services.index');

        Route::prefix('pages')->controller(PagesController::class)->group(function () {
            Route::get('/', 'index')->name('admin.pages.index')->middleware('permission:admin.pages.index');
            Route::get('/load', 'load')->name('admin.pages.load')->middleware('permission:admin.pages.load');
            Route::get('/create', 'create')->name('admin.pages.create')->middleware('permission:admin.pages.create');
            Route::post('/store', 'store')->name('admin.pages.store')->middleware('permission:admin.pages.store');
            Route::get('/{id}/edit', 'edit')->name('admin.pages.edit')->middleware('permission:admin.pages.edit');
            Route::post('/{id}/update', 'update')->name('admin.pages.update')->middleware('permission:admin.pages.update');
            Route::post('/{id}/delete', 'delete')->name('admin.pages.delete')->middleware('permission:admin.pages.delete');
        });

        Route::prefix('services')->controller(ServicesController::class)->group(function () {
            Route::get('/', 'index')->name('admin.services.index')->middleware('permission:admin.services.index');
            Route::get('/load', 'load')->name('admin.services.load')->middleware('permission:admin.services.load');
            Route::get('/create', 'create')->name('admin.services.create')->middleware('permission:admin.services.create');
            Route::post('/store', 'store')->name('admin.services.store')->middleware('permission:admin.services.store');
            Route::get('/{id}/edit', 'edit')->name('admin.services.edit')->middleware('permission:admin.services.edit');
            Route::post('/{id}/update', 'update')->name('admin.services.update')->middleware('permission:admin.services.update');
            Route::post('/{id}/delete', 'delete')->name('admin.services.delete')->middleware('permission:admin.services.delete');
        });

        Route::prefix('portfolios')->controller(PortfoliosController::class)->group(function () {
            Route::get('/', 'index')->name('admin.portfolios.index')->middleware('permission:admin.portfolios.index');
            Route::get('/load', 'load')->name('admin.portfolios.load')->middleware('permission:admin.portfolios.load');
            Route::get('/create', 'create')->name('admin.portfolios.create')->middleware('permission:admin.portfolios.create');
            Route::post('/store', 'store')->name('admin.portfolios.store')->middleware('permission:admin.portfolios.store');
            Route::get('/{id}/edit', 'edit')->name('admin.portfolios.edit')->middleware('permission:admin.portfolios.edit');
            Route::post('/{id}/update', 'update')->name('admin.portfolios.update')->middleware('permission:admin.portfolios.update');
            Route::post('/{id}/delete', 'delete')->name('admin.portfolios.delete')->middleware('permission:admin.portfolios.delete');
            Route::post('/{id}/delete-image', 'deleteImage')->name('admin.portfolios.delete.image')->middleware('permission:admin.portfolios.delete.image');
            Route::post('/{id}/define-image-thumb', 'defineImageThumb')->name('admin.portfolios.define.image')->middleware('permission:admin.portfolios.define.image');
        });

        Route::prefix('integrations')->controller(IntegrationsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.integrations.index')->middleware('permission:admin.integrations.index');
            Route::get('/load', 'load')->name('admin.integrations.load')->middleware('permission:admin.integrations.load');
            Route::get('/create', 'create')->name('admin.integrations.create')->middleware('permission:admin.integrations.create');
            Route::post('/store', 'store')->name('admin.integrations.store')->middleware('permission:admin.integrations.store');
            Route::get('/{id}/edit', 'edit')->name('admin.integrations.edit')->middleware('permission:admin.integrations.edit');
            Route::post('/{id}/update', 'update')->name('admin.integrations.update')->middleware('permission:admin.integrations.update');
            Route::post('/{id}/delete', 'delete')->name('admin.integrations.delete')->middleware('permission:admin.integrations.delete');

            Route::prefix('{slug}/playground')->controller(IntegrationsPlaygroundController::class)->group(function () {
                Route::get('/', 'index')->name('admin.integrations.playground.index');
                Route::get('/load', 'load')->name('admin.integrations.playground.load');
                Route::post('/createProduct', 'createProduct')->name('admin.integrations.playground.createProduct');
            });
        });

        Route::prefix('integration_categories')->controller(IntegrationCategoriesController::class)->group(function () {
            Route::get('/', 'index')->name('admin.integration_categories.index');
            Route::get('/load', 'load')->name('admin.integration_categories.load');
            Route::get('/create', 'create')->name('admin.integration_categories.create');
            Route::post('/store', 'store')->name('admin.integration_categories.store');
            Route::get('/{id}/edit', 'edit')->name('admin.integration_categories.edit');
            Route::post('/{id}/update', 'update')->name('admin.integration_categories.update');
            Route::post('/{id}/delete', 'delete')->name('admin.integration_categories.delete');
        });

        Route::prefix('testimonials')->controller(TestimonialsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.testimonials.index')->middleware('permission:admin.testimonials.index');
            Route::get('/load', 'load')->name('admin.testimonials.load')->middleware('permission:admin.testimonials.load');
            Route::get('/create', 'create')->name('admin.testimonials.create')->middleware('permission:admin.testimonials.create');
            Route::post('/store', 'store')->name('admin.testimonials.store')->middleware('permission:admin.testimonials.store');
            Route::get('/{id}/edit', 'edit')->name('admin.testimonials.edit')->middleware('permission:admin.testimonials.edit');
            Route::post('/{id}/update', 'update')->name('admin.testimonials.update')->middleware('permission:admin.testimonials.update');
            Route::post('/{id}/delete', 'delete')->name('admin.testimonials.delete')->middleware('permission:admin.testimonials.delete');
        });

        Route::prefix('sliders')->controller(SlidersController::class)->group(function () {
            Route::get('/', 'index')->name('admin.sliders.index')->middleware('permission:admin.sliders.index');
            Route::get('/load', 'load')->name('admin.sliders.load')->middleware('permission:admin.sliders.load');
            Route::get('/create', 'create')->name('admin.sliders.create')->middleware('permission:admin.sliders.create');
            Route::post('/store', 'store')->name('admin.sliders.store')->middleware('permission:admin.sliders.store');
            Route::get('/{id}/edit', 'edit')->name('admin.sliders.edit')->middleware('permission:admin.sliders.edit');
            Route::post('/{id}/update', 'update')->name('admin.sliders.update')->middleware('permission:admin.sliders.update');
            Route::post('/{id}/delete', 'delete')->name('admin.sliders.delete')->middleware('permission:admin.sliders.delete');
        });

        Route::prefix('invoices')->controller(InvoicesController::class)->group(function () {
            Route::get('/', 'index')->name('admin.invoices.index')->middleware('permission:admin.invoices.index');
            Route::get('/load', 'load')->name('admin.invoices.load')->middleware('permission:admin.invoices.load');
            Route::get('/create', 'create')->name('admin.invoices.create')->middleware('permission:admin.invoices.create');
            Route::post('/store', 'store')->name('admin.invoices.store')->middleware('permission:admin.invoices.store');
            Route::get('/{id}/show', 'show')->name('admin.invoices.show')->middleware('permission:admin.invoices.show');
            Route::get('/{id}/edit', 'edit')->name('admin.invoices.edit')->middleware('permission:admin.invoices.edit');
            Route::post('/{id}/update', 'update')->name('admin.invoices.update')->middleware('permission:admin.invoices.update');
            Route::post('/{id}/delete', 'delete')->name('admin.invoices.delete')->middleware('permission:admin.invoices.delete');
            Route::post('/{id}/cancel', 'cancel')->name('admin.invoices.cancel')->middleware('permission:admin.invoices.cancel');
            Route::post('/{id}/confirm-payment', 'confirmPayment')->name('admin.invoices.confirmPayment')->middleware('permission:admin.invoices.confirmPayment');
            Route::post('/{id}/send-reminder', 'sendReminder')->name('admin.invoices.sendReminder')->middleware('permission:admin.invoices.sendReminder');
            Route::post('/{id}/generate-payment', 'generatePayment')->name('admin.invoices.generatePayment')->middleware('permission:admin.invoices.generatePayment');
            Route::get('/{id}/load-installments', 'loadInstallments')->name('admin.invoices.loadInstallments')->middleware('permission:admin.invoices.loadInstallments');
            Route::get('/{id}/latest-webhook', 'getLatestWebhook')->name('admin.invoices.latest-webhook');
        });

        Route::prefix('transactions')->controller(TransactionsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.transactions.index')->middleware('permission:admin.transactions.index');
            Route::get('/load', 'load')->name('admin.transactions.load')->middleware('permission:admin.transactions.load');
            Route::get('/create', 'create')->name('admin.transactions.create')->middleware('permission:admin.transactions.create');
            Route::post('/store', 'store')->name('admin.transactions.store')->middleware('permission:admin.transactions.store');
            Route::get('/{id}/edit', 'edit')->name('admin.transactions.edit')->middleware('permission:admin.transactions.edit');
            Route::post('/{id}/update', 'update')->name('admin.transactions.update')->middleware('permission:admin.transactions.update');
            Route::post('/{id}/delete', 'delete')->name('admin.transactions.delete')->middleware('permission:admin.transactions.delete');
        });

        Route::prefix('categories')->controller(CategoriesController::class)->group(function () {
            Route::get('/', 'index')->name('admin.categories.index')->middleware('permission:admin.categories.index');
            Route::get('/load', 'load')->name('admin.categories.load')->middleware('permission:admin.categories.load');
            Route::get('/create', 'create')->name('admin.categories.create')->middleware('permission:admin.categories.create');
            Route::post('/store', 'store')->name('admin.categories.store')->middleware('permission:admin.categories.store');
            Route::get('/{id}/edit', 'edit')->name('admin.categories.edit')->middleware('permission:admin.categories.edit');
            Route::post('/{id}/update', 'update')->name('admin.categories.update')->middleware('permission:admin.categories.update');
            Route::post('/{id}/delete', 'delete')->name('admin.categories.delete')->middleware('permission:admin.categories.delete');
        });

        Route::prefix('products')->controller(ProductsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.products.index')->middleware('permission:admin.products.index');
            Route::get('/load', 'load')->name('admin.products.load')->middleware('permission:admin.products.load');
            Route::get('/create', 'create')->name('admin.products.create')->middleware('permission:admin.products.create');
            Route::post('/store', 'store')->name('admin.products.store')->middleware('permission:admin.products.store');
            Route::get('/{id}/edit', 'edit')->name('admin.products.edit')->middleware('permission:admin.products.edit');
            Route::post('/{id}/update', 'update')->name('admin.products.update')->middleware('permission:admin.products.update');
            Route::post('/{id}/delete', 'delete')->name('admin.products.delete')->middleware('permission:admin.products.delete');
            Route::post('/{id}/generate-image', 'generateImageStory')->name('admin.products.generateImageStory');
            Route::post('/{id}/generate-image-feed', 'generateImageFeed')->name('admin.products.generateImageFeed');
            Route::post('/{id}/facebook-catalog', 'facebookCatalog')->name('admin.products.facebookCatalog');
            Route::post('/{id}/publish-product-group', 'publishProductGroup')->name('admin.products.publishProductGroup');
            Route::get('/generateSuggestions', 'generateSuggestions')->name('admin.products.generateSuggestions');
        });

        // Log Viewer
        Route::prefix('logs')->controller(LogsController::class)->group(function () {
            Route::get('/', 'index')->name('admin.logs.index')->middleware('permission:admin.logs.index');
            Route::post('/clear', 'clear')->name('admin.logs.clear')->middleware('permission:admin.logs.clear');
            Route::get('/download', 'download')->name('admin.logs.download')->middleware('permission:admin.logs.download');
        });

        Route::prefix('bank_accounts')->controller(BankAccountController::class)->group(function () {
            Route::get('/', 'index')->name('admin.bank_accounts.index')->middleware('permission:admin.bank_accounts.index');
            Route::get('/load', 'load')->name('admin.bank_accounts.load')->middleware('permission:admin.bank_accounts.load');
            Route::get('/create', 'create')->name('admin.bank_accounts.create')->middleware('permission:admin.bank_accounts.create');
            Route::post('/store', 'store')->name('admin.bank_accounts.store')->middleware('permission:admin.bank_accounts.store');
            Route::get('/{id}/edit', 'edit')->name('admin.bank_accounts.edit')->middleware('permission:admin.bank_accounts.edit');
            Route::post('/{id}/update', 'update')->name('admin.bank_accounts.update')->middleware('permission:admin.bank_accounts.update');
            Route::post('/{id}/delete', 'delete')->name('admin.bank_accounts.delete')->middleware('permission:admin.bank_accounts.delete');
            Route::get('/users-search', 'usersSearch')->name('admin.bank_accounts.users_search');
        });

        // Import Tool
        Route::prefix('import')->controller(ImportController::class)->group(function () {
            Route::get('/', 'index')->name('admin.import.index')->middleware('permission:admin.import.index');
            Route::post('/data', 'importData')->name('admin.import.data')->middleware('permission:admin.import.data');
        });
    });
});

Route::fallback(function () {
    $url = request()->url();

    // Ignorar arquivos .map
    if (str_ends_with($url, '.map')) {
        abort(404);
    }

    Log::info('Página não encontrada - ' . $url);
    return redirect('/');
});
