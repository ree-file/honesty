<?php

namespace App\Admin\Controllers;

use App\Supplierfavorable;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SupplierfavorableController extends Controller
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

            $content->header('header');
            $content->description('description');

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

            $content->header('满减优惠');
            $content->description('创建满减优惠');

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
        return Admin::grid(Supplierfavorable::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Supplierfavorable::class, function (Form $form) {

            $form->select('supplier_id','选择店铺')->options('/admin/api/supplier');
            $form->text('limit',"满减限制");
            $form->text("discountmoney","满减额度");

            $form->dateRange('starttime', 'deadline',"优惠时间");
            $form->radio("is_active","激活")->options([0=>'否',1=>'是'])->default(1);
            $form->saving(function (Form $form) {
              // 跳转页面
              return redirect('/admin/announcement');
            });

        });
    }
}
