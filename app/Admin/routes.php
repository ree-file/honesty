<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('goods', GoodsController::class);
    $router->resource('supplier', SupplierController::class);
    $router->resource('goodscategory', CategoryController::class);
    $router->resource('suppliergoods', SupplierGoodsController::class);
    $router->resource('supplierfavorable', SupplierfavorableController::class);
    $router->resource('announcement', AnnouncementController::class);
    $router->resource('notifiy', NotifyController::class);
    $router->resource('shippers', ShipperController::class);
    $router->get('api/category','CategoryController@content');
    $router->get('api/supplier','SupplierController@content');
    $router->get('api/goods','GoodsController@content');
    $router->get('api/goodsprice','GoodsController@price');
    $router->post('/suppliergoods/update','SupplierGoodsController@update');
    //有待提高
    $router->get('supplier/all/goods','SupplierController@goods');
    $router->get('supplier/{id}/goods/detail','SupplierController@goodsdetail');
    $router->get('supplier/{id}/goods','SupplierController@goodsmuster');
});
