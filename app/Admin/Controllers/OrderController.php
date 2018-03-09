<?php

namespace App\Admin\Controllers;

use App\Order;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class OrderController extends Controller
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

            $content->header('订单总览');
            $content->description('订单列表');

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

            $content->header('订单信息');
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
        return Admin::grid(Order::class, function (Grid $grid) {
            $grid->supplier()->supplier_name('小铺名');
            $grid->user_id("下单用户");
            $grid->order_payway('支付方式')->display(function($type){
              if ($type=="alipay") {
                return '支付宝';
              }
              elseif ($type=='weixinpay') {
                return '微信';
              }
              else {
                return '无';
              }
            });
            $grid->order_pay('支付价格');
            $grid->ordergoods()->goods_content("商品")->display(function($goods){
              $goods = (array)unserialize($goods);
              $goods = array_map(function ($goods) {
                  return "<span class='label label-success'>{$goods['goods_name']}</span>";
              }, $goods);

              return join('&nbsp;', $goods);
            });
            $grid->created_at('时间');
            $grid->order_status('支付状态');
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
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
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display("supplier.supplier_name","小铺名");
            $form->display("user_id","用户");
            $form->display("order_pay","支付金额¥");
            $form->display('created_at', '时间');
        });
    }
}
