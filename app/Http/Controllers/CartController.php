<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product=Product::find($request->id);

        if($product==null){
            return response()->json([
                'status'=>false,
                'message'=> 'Product Not found'
            ]) ;
        }

        if(Cart::count()>0){

            //Products found in Cart
            //Check if this product already in the cart
            //Return as message that product already added in your cart
            //if product not found in the cart, then add product in the cart

            $cartContent=Cart::content();
            $productAlreadyExist=false;

            foreach($cartContent as $item){
                if($item->id==$product->id){
                    $productAlreadyExist=true;
                }
            }

            if( $productAlreadyExist==false ){
                Cart::add($product->id,$product->title,1,$product->price);
                $status=true;
            $message=$product->title.' added in cart ';
            } else{
                $status=false;
            $message=$product->title.' already added in cart ';
            }

        } else{
            //Cart is empty
            Cart::add($product->id,$product->title,1,$product->price);
            $status=true;
            $message=$product->title.' added in cart ';
        }

        return response()->json([
            'status'=>$status,
            'message'=> $message
        ]) ;

        //Cart::add('293ad','Product 1',1,9.99);
    }
    public function cart(){
        $cartContent=Cart::content();
        $data['cartContent']=$cartContent;
        return view('front.cart',$data);
    }

    public function updateCart(Request $request){
        $rowId=$request->rowid;
        $qty=$request->qty;
        Cart::update($rowId, $qty);

        $message='Cart updated successfully';

        session()->flash('success',$message);

        return response()->json([
            'status'=>true,
            'message'=> $message
        ]);
    }
}
