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
    $router->resource('orders', OrderController::class);
    $router->resource('users', UserController::class);
    $router->resource('supplier/goods/detail','SupplierController@goodsdetail');
    $router->resource('/goodssale',SaleInfoController::class);
    $router->get('supplier/{id}/goods','SupplierController@goodsmuster');
});
