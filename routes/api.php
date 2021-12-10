<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function(){
	 Route::post('login', 'ApiAuthController@login');
	 
	 Route::group(['middleware' => 'auth:api'], function(){
	 	Route::post('logout', 'ApiAuthController@logout');
	 	Route::post('getUser', 'ApiAuthController@getUser');

	 	// user
	 	Route::post('user/list', 'UserController@userlist')->name('User.list');
	 	Route::post('user/create', 'UserController@usercreate')->name('User.create');
	 	Route::post('user/edit', 'UserController@useredit')->name('User.edit');
	 	Route::post('user/register', 'UserController@register');
	 	Route::post('user/update', 'UserController@userupdate');
	 	Route::post('user/delete', 'UserController@userdelete')->name('User.delete');
	 	
	 	// accesscontrol
	 	Route::post('accesscontrol/list', 'AclController@accesscontrollist')->name('AccessControl.list');
	 	Route::post('accesscontrol/create', 'AclController@accesscontrolcreate')->name('AccessControl.create');
	 	Route::post('accesscontrol/edit', 'AclController@accesscontroledit')->name('AccessControl.edit');
	 	Route::post('accesscontrol/insert', 'AclController@accesscontrolinsert');
	 	Route::post('accesscontrol/update', 'AclController@accesscontrolupdate');
	 	Route::post('accesscontrol/delete', 'AclController@accesscontroldelete')->name('AccessControl.delete');

	 	// master PO
	 	Route::post('po/list', 'MasterpoController@polist')->name('PO.list');
	 	Route::post('po/create', 'MasterpoController@pocreate')->name('PO.create');
	 	Route::post('po/edit', 'MasterpoController@poedit')->name('PO.edit');
	 	Route::post('po/insert', 'MasterpoController@poinsert');
	 	Route::post('po/update', 'MasterpoController@poupdate');
	 	Route::post('po/delete', 'MasterpoController@podelete')->name('PO.delete');

	 	// master BUS
	 	Route::post('bus/list', 'MasterbusController@buslist')->name('bus.list');
	 	Route::post('bus/create', 'MasterbusController@buscreate')->name('bus.create');
	 	Route::post('bus/edit', 'MasterbusController@busedit')->name('bus.edit');
	 	Route::post('bus/insert', 'MasterbusController@businsert');
	 	Route::post('bus/update', 'MasterbusController@busupdate');
	 	Route::post('bus/delete', 'MasterbusController@busdelete')->name('bus.delete');

	 	// TIPEKURSI
	 	Route::post('tipekursi/list', 'TipekursiController@tipekursilist')->name('tipekursi.list');
	 	Route::post('tipekursi/create', 'TipekursiController@tipekursicreate')->name('tipekursi.create');
	 	Route::post('tipekursi/edit', 'TipekursiController@tipekursiedit')->name('tipekursi.edit');
	 	Route::post('tipekursi/insert', 'TipekursiController@tipekursiinsert');
	 	Route::post('tipekursi/update', 'TipekursiController@tipekursiupdate');
	 	Route::post('tipekursi/delete', 'TipekursiController@tipekursidelete')->name('tipekursi.delete');

	 	// CUSTOMER
	 	Route::post('customer/list', 'CustomerController@customerlist')->name('customer.list');
	 	Route::post('customer/create', 'CustomerController@customercreate')->name('customer.create');
	 	Route::post('customer/edit', 'CustomerController@customeredit')->name('customer.edit');
	 	Route::post('customer/insert', 'CustomerController@customerinsert');
	 	Route::post('customer/update', 'CustomerController@customerupdate');
	 	Route::post('customer/delete', 'CustomerController@customerdelete')->name('customer.delete');

	 	// TRANSAKSI
	 	Route::post('transaksi/list', 'TransaksiController@transaksilist')->name('transaksi.list');
	 	Route::post('transaksi/create', 'TransaksiController@transaksicreate')->name('transaksi.create');
	 	Route::post('transaksi/edit', 'TransaksiController@transaksiedit')->name('transaksi.edit');
	 	Route::post('transaksi/insert', 'TransaksiController@transaksiinsert');
	 	Route::post('transaksi/update', 'TransaksiController@transaksiupdate');
	 	Route::post('transaksi/delete', 'TransaksiController@transaksidelete')->name('transaksi.delete');
	 	Route::post('transaksi/bayar', 'TransaksiController@transaksibayar')->name('transaksi.bayar');

	 	Route::post('transaksi/notifikasi', 'TransaksiController@transaksinotifikasi')->name('transaksi.notifikasi');


	 });
});