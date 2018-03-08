<?php

namespace App\Admin\Controllers;

use App\SupplierGoods;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SupplierGoodsController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('店铺商品列表');
            $content->description('所有店铺的所有商品');
            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('修改店铺商品');
            $content->description('更改折扣，打折期限，每日上货量');

            $content->body($this->editform()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('添加店铺商品');
            $content->description('需要填写店铺，商品，及打折信息');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(SupplierGoods::class, function (Grid $grid) {

          $grid->filter(function($filter){

              // 去掉默认的id过滤器
              $filter->disableIdFilter();

              // 在这里添加字段过滤器
              $filter->like('supplier.supplier_name', '店铺名称');
            });
            $grid->supplier()->supplier_name("店铺名称");
            $grid->goods()->goods_name('食品名称');
            $grid->goods()->price('食品价格');
            $grid->discount('食品折扣');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        return Admin::form(SupplierGoods::class, function (Form $form) {
            $form->select('supplier_id','选择店铺')->options('/admin/api/supplier')->load('goods_id', '/admin/api/goods');
            $form->select('goods_id','选择商品')->load('goods.price','/admin/api/goodsprice');
            $form->select('goods.price','商品价格');
            $form->text('supplier_num',"商品数量");
            $form->text('shipments',"上货数量");
            $form->text('discount','商品折扣')->default('1');
            $form->radio('is_discount','是否打折')->options(['0'=>'不打折','1'=>'打折'])->default('0');
            $form->dateRange('starttime', 'deadline', '选择打折时间');
            $form->ignore(['goods.price']);
            // $form->setAction('/admin/suppliergoods/update');
        });
    }
    protected function editform()
    {
      return Admin::form(SupplierGoods::class, function (Form $form) {

          $form->display('supplier.supplier_name', '店铺名称');
          $form->display('goods.goods_name',"商品名称");
          $form->display('goods.price','商品价格');
          $form->text('discount','商品折扣');
          $form->text('shipments','今日上货量');
          $form->text('supplier_num','店铺库存');
          $form->radio('is_discount','是否打折')->options(['0'=>'不打折','1'=>'打折']);
          $form->dateRange('starttime', 'deadline', '选择打折时间');
      });
    }

}
