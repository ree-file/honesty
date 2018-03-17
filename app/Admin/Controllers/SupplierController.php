<?php

namespace App\Admin\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\SupplierGoods;
use App\Goods;

class SupplierController extends Controller
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

            $content->header('店铺');
            $content->description('店铺列表');

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
            $content->description('修改店铺信息');

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

            $content->header('创建');
            $content->description('创建店铺');

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
        return Admin::grid(Supplier::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->supplier_name('店铺名称');
            $grid->honesty_rate('诚信率')->sortable();
            $grid->created_at('创建时间');
            $grid->actions(function ($actions) {

              // append一个操作
              $actions->append('<a href="supplier/'.$actions->getKey().'/goods/detail"><i class="fa fa-eye"></i></a>');

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
        return Admin::form(Supplier::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('supplier_name','店铺名');
            $form->display("honesty_rate","诚信率")->default(0);
            $form->image('img',"店铺图片");
        });
    }
    protected function goodsgrid()
    {
      return Admin::grid(Supplier::class, function (Grid $grid) {
        $grid->supplier_name('店铺名');
        $grid->tools(function ($tools) {
          $tools->batch(function ($batch) {
            $batch->disableDelete();
          });
        });
        $grid->actions(function ($actions) {
          $actions->disableDelete();
          $actions->disableEdit();
          $actions->append('<a href="/admin/supplier/'.$actions->getKey().'/goods/detail"><i class="fa fa-eye"></i></a>');
        });
        $grid->disableExport();
        $grid->disableCreateButton();
        $grid->goods("店铺商品")->display(function ($goods) {
        $goods = array_map(function ($role) {
            return "<span class='label label-success'>{$role['goods_name']}</span>";
        }, $goods);

        return join('&nbsp;', $goods);
    });
      });
    }
    public function content()
    {
      $supplier = Supplier::all('id','supplier_name as text');
      return $supplier;
    }
    //有待提高
    public function goods()
    {
      return Admin::content(function (Content $content) {

          $content->header('店铺商品');
          $content->description('店铺商品列表');

          $content->body($this->goodsgrid());
      });
    }
    public function goodsform($id)
    {
      return Admin::form(Supplier::class, function (Form $form) use($id) {

          $form->display('id', 'ID');
          $form->display('supplier_name','店铺名');
          $form->display("honesty_rate","诚信率")->default(0);
          $form->multipleSelect('goods','店铺商品')->options(Goods::all()->pluck('goods_name', 'id'));
          $form->setAction('/admin/api/supplier/'.$id.'/goods');
      });
    }
    public function goodsdetail($id)
    {
      return Admin::content(function (Content $content) use ($id) {

          $content->header('修改');
          $content->description('修改店铺商品');

          $content->body($this->goodsform($id)->edit($id));
      });
    }
    public function goodsmuster(Request $request)
    {
      $goods = Goods::all();
      return $this->success($goods);
    }
    public function goodsupdate($id)
    {
      return response()->json(['status'=>$id]);
    }
}
