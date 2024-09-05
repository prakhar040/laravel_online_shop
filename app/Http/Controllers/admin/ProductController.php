<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{

    public function index(Request $request){
        $products=Product::latest('id');

        if($request->get('keyword')!=""){
            $products=$products->where('title','like','%'.$request->keyword.'%');

        }
        $products=$products->paginate(10);
        $data['products']=$products;
        return view('admin.products.list', $data);
    }
    public function create(){
        $data=[];
        $categories=Category::orderBy('name','ASC')->get();
        $brands=Brand::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$categories;
        return view('admin.products.create', $data);
    }

    public function edit($id, Request $request){

        $product=Product::find($id);

        $subCategories=SubCategory::where('category_id',$product->category_id)->get();

        $data=[];
        $data['product']=$product;
        $data['subCategories']=$subCategories;
        $categories=Category::orderBy('name','ASC')->get();
        $brands=Brand::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$categories;
        return view('admin.products.edit', $data);
    }

    public function update($id, Request $request){
        $product=Product::find($id);
        $rules=[
            'title'=> 'required',
            'slug'=> 'required|unique:products,slug,'.$product->id.',id',
            'price'=> 'required|numeric',
            'sku'=> 'required|unique:products,sku,'.$product->id.',id',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ];

        if(!empty($request->track_qty) && $request->track_qty=='Yes'){
            $rules['qty']='required|numeric';
        }

        $validator=Validator::make($request->all(),$rules);

        if($validator->passes()){

            $product->title=$request->title;
            $product->slug=$request->slug;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->compare_price=$request->compare_price;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->track_qty=$request->track_qty;
            $product->qty=$request->qty;
            $product->status=$request->status;
            $product->category_id=$request->category;
            $product->sub_category_id= $request->sub_category;
            $product->brand_id=$request->brand;
            $product->is_featured=$request->is_featured;
            $product->shipping_returns=$request->shipping_returns;
            $product->short_description=$request->short_description;
            $product->save();

            $request->session()->flash('success','Product Updated Successfully');

            return response()->json([
                'status'=>true,
                'message'=>'Product Updated Successfully'
            ]);
            

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request){
        $product=Product::find($id);

        if(empty($product)){
            $request->session()->flash('error','Product not found');
            return response()->json([
                'status'=>false,
                'notFound'=>true
            ]);
        }
        $product->delete();

        $request->session()->flash('success','Product Deleted Succcessfully');

            return response()->json([
                'status'=>true,
                'message'=>'Product Deleted Succcessfully'
            ]);
 
    }

    public function store(Request $request){
        $rules=[
            'title'=> 'required',
            'slug'=> 'required|unique:products',
            'price'=> 'required|numeric',
            'sku'=> 'required|unique:products',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ];

        if(!empty($request->track_qty) && $request->track_qty=='Yes'){
            $rules['qty']='required|numeric';
        }

        $validator=Validator::make($request->all(),$rules);

        if($validator->passes()){

            $product=new Product();
            $product->title=$request->title;
            $product->slug=$request->slug;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->compare_price=$request->compare_price;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->track_qty=$request->track_qty;
            $product->qty=$request->qty;
            $product->status=$request->status;
            $product->category_id=$request->category;
            $product->sub_category_id= $request->sub_category;
            $product->brand_id=$request->brand;
            $product->is_featured=$request->is_featured;
            $product->shipping_returns=$request->shipping_returns;
            $product->short_description=$request->short_description;
            $product->save();

            $request->session()->flash('success','Product Added Successfully');

            return response()->json([
                'status'=>true,
                'message'=>'Product Added Successfully'
            ]);
            

        }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
}
