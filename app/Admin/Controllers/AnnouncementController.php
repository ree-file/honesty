<?php

namespace App\Admin\Controllers;

use App\Announcement;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AnnouncementController extends Controller
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

            $content->header('店铺优惠列表');
            $content->description('所有店铺的所有优惠');

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

            $content->header('修改');
            $content->description('修改优惠信息');

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

            $content->header('添加');
            $content->description('添加优惠内容');

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
        return Admin::grid(Announcement::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->suppliers()->supplier_name('店铺名');
            $grid->content('优惠内容');
            $grid->type('优惠类型')->display(function ($type){
              if ($type=='supplier_favorable') {
                return '店铺优惠';
              }
              else {
                return '商品优惠';
              }
            });
            $grid->actions(function ($actions) {
              ;
              $actions->prepend('<a href="/admin/suppliergoods"><i class="fa fa-paper-plane"></i></a>');
              $actions->prepend('<a href="/admin/supplierfavorable/create"><i class="fa fa-apple"></i></a>');
            });
            $grid->starttime('开始时间');
            $grid->deadline('结束时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Announcement::class, function (Form $form) {

            $form->select('supplier_id','选择店铺')->options('/admin/api/supplier');
            $form->radio('type',"优惠类型")->options(["goods_favorable"=>"商品优惠","supplier_favorable"=>"店铺优惠"]);
            $form->text('content','优惠内容');
            $form->datetimeRange("starttime", "deadline", '选择开始结束时间');
        });
    }
    protected function editform()
    {
      return Admin::form(Announcement::class, function (Form $form) {

          $form->display('suppliers.supplier_name','店铺');
          $form->radio('type',"优惠类型")->options(["goods_favorable"=>"商品优惠","supplier_favorable"=>"店铺优惠"]);
          $form->text('content','优惠内容');
          $form->datetimeRange("starttime", "deadline", '选择开始结束时间');
      });
    }
}
