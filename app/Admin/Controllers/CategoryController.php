<?php

namespace App\Admin\Controllers;

use App\Goodscategory;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CategoryController extends Controller
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

            $content->header('商品分类');
            $content->description('可以进行添加、修改、删除');

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

            $content->header('分类修改');
            $content->description('修改分类名称');

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

            $content->header('新建分类');
            $content->description('创建一个新的分类');

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
        return Admin::grid(Goodscategory::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->cate_name('分类名称');
            $grid->created_at( '创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Goodscategory::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('cate_name', '分类名称');
            $form->display('updated_at', '修改时间');
        });
    }

    public function content()
    {
      $result = DB::table('goodscategory')->select("id","cate_name as text")->get();
      return $result;
    }
}
