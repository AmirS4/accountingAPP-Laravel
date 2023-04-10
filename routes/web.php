<?php

use App\Http\Controllers\CustomersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TurnoversController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('admin/home', [\App\Http\Controllers\HomeController::class, 'adminHome'])->name('admin.home')->middleware('is_admin');
Route::get('admin/orders', [\App\Http\Controllers\HomeController::class, 'orders'])->name('orders')->middleware('is_admin');
Route::get('admin/products', [\App\Http\Controllers\HomeController::class, 'products'])->name('products')->middleware('is_admin');
Route::get('admin/turnovers', [App\Http\Controllers\HomeController::class, 'turnovers'])->name('turnovers')->middleware('is_admin');

Route::resource('customers-ajax-crud', CustomersController::class);
Route::resource('orders-ajax-crud', OrdersController::class);
Route::resource('products-ajax-crud', ProductsController::class);
Route::resource('turnovers-ajax-crud', TurnoversController::class);


Route::get('/autocomplete/customers', [OrdersController::class, 'autocompleteCustomers'])->name('orders-ajax-crud.autocompleteCustomers');
Route::get('/autocomplete/products', [OrdersController::class, 'autocompleteProducts'])->name('orders-ajax-crud.autocompleteProducts');








