<?php

namespace App\Admin\Controllers;

use App\Log;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class LogController extends Controller
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

            $content->header('出货记录表');
            $content->description('出货记录');

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

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(Log::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->supplier()->supplier_name("店铺名");
            $grid->goods()->goods_name("商品名");
            $grid->created_at("添加时间")->sortable();
            $grid->actions(function ($actions) {
              $actions->disableDelete();
              $actions->disableEdit();
            });
            $grid->disableCreateButton();
            $grid->filter(function($filter){

                  // 去掉默认的id过滤器
                  $filter->disableIdFilter();

                  // 在这里添加字段过滤器
                  $filter->date('created_at','日期');
                  $filter->equal('supplier_id','店铺')->select('/admin/api/supplier');
                  $filter->equal('goods_id','食品')->select('/admin/api/goods');
                });
        });

    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Log::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
