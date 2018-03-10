<?php

namespace App\Admin\Controllers;

use App\Goods;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use App\SupplierGoods;
class GoodsController extends Controller
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

            $content->header('商品列表');
            $content->description('显示所有商品');

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

            $content->header('商品编辑');
            $content->description('修改商品属性');

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

            $content->header('添加商品');
            $content->description('填写商品详情');

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
        return Admin::grid(Goods::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->goods_name('商品名称');
            $grid->img('商品图片')->image();
            $grid->price('商品价格')->sortable();
            $grid->goodscategory()->cate_name('分类名称');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Goods::class, function (Form $form) {

            $form->text('goods_name',"商品名称")->rules('required',[
              'required' => '不能为空'
            ]);
            $form->text('price',"商品价格")->rules('required|regex:/^[0-9]+(.[0-9]{2})?$/',[
              'required' => '不能为空',
              'regex' => '请输入拥有两位小数的正实数',
            ]);
            $form->text('num',"库存")->rules('required|regex:/^\d+$/',[
              'required' => '不能为空',
              'regex' => '请输入正整数',
            ]);
            $form->radio('is_active','上架')->options([0 => '否', 1=> '是']);
            $form->display('goodscategory.cate_name','当前分类');
            $form->select('category_id',"修改商品种类")->options("/admin/api/category")->rules('required',[
              'required' => '不能为空'
            ]);
            $form->image('img',"商品图片")->uniqueName()->rules('required',[
              'required' => '不能为空'
            ]);

        });
    }
    public function content(Request $request)
    {
      $supplier_id = $request->get('q');
      $goods_ids = DB::table('supplier_goods')->select('goods_id')->where('supplier_id','=',$supplier_id)->get();
      $ids = [];
      for ($i=0; $i < count($goods_ids); $i++) {
          $ids[$i] = $goods_ids[$i]->goods_id;
      }
      $goods = DB::table('goods')->select('id','goods_name as text')->whereNotIn('id', $ids)->get();
      return $goods;
    }
    public function price(Request $request)
    {
      $goods_id = $request->get('q');
      $price = DB::table('goods')->select('id','price as text')->where('id', $goods_id)->get();
      return $price;
    }
}
