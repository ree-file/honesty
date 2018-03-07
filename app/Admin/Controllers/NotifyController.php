<?php

namespace App\Admin\Controllers;

use App\Notify;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class NotifyController extends Controller
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

            $content->header('消息');
            $content->description('消息列表');

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

            $content->header('通知修改');
            $content->description('修改为已读或者未读');

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
        return Admin::grid(Notify::class, function (Grid $grid) {
            $grid->created_at('日期')->sortable();
            $grid->content('内容');
            $grid->operator('操作者');
            $grid->disableCreateButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Notify::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('content',"内容");
            $form->display('operator',"操作人");
            $states = [
              'off' => ['value' => 0, 'text' => '未读', 'color' => 'danger'],
              'on'  => ['value' => 1, 'text' => '已读', 'color' => 'success'],
            ];

            $form->switch("delete",'消息')->states($states);
            $form->display('created_at', '通知时间');

        });
    }
}
