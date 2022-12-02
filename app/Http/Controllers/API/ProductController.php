<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Validator;
use App\Http\Resources\Product as ProductResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;



class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return $this->sendResponse(ProductResource::collection($products), 'All products sent');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input , [
        'name'=> 'required',
        'detail'=> 'required',
        'price'=> 'required'
        ]  );

        if ($validator->fails()) {
        return $this->sendError('Please validate error' ,$validator->errors() );
        }
        $user = Auth::user();
        $input['user_id'] = $user->id;
        $product = Product::create($input);
        return $this->sendResponse($product ,'Product created successfully' );

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        $errorMessage = [];

        if ( is_null($product) )
        {
            return $this->sendError('Product not found',$errorMessage );
        }

        if (Auth::user()->can('view', $product))
        {
            return $this->sendResponse($product ,'Product found successfully' );
        }
        else
        {
            return $this->sendError('you dont have rights' , $errorMessage);
        }


    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $errorMessage = [];
        $input = $request->all();
        if ( is_null($product) )
        {
            return $this->sendError('Product not found',$errorMessage );
        }
        // else if ( $product->user_id != Auth::id() && $user->type_user != 'admin' )
        // {
        //     return $this->sendError('you dont have rights' , $errorMessage);
        // }
        if (Auth::user()->can('update', $product))
        {
            $validator = Validator::make($input , [
                'name'=> 'required',
                'detail'=> 'required',
                'price'=> 'required'
                ]);
                if ($validator->fails()) {
                    return $this->sendError('Validation error' , $validator->errors());
                }

            $product->name = $input['name'];
            $product->detail = $input['detail'];
            $product->price = $input['price'];
            $product->save();
            return $this->sendResponse($product ,'Product updated successfully' );
        }
        else
        {
            return $this->sendError('you dont have rights' , $errorMessage);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $errorMessage = [] ;
        if ( is_null($product) )
        {
            return $this->sendError('Product not found',$errorMessage );
        }
        //
        if (Auth::user()->can('delete', $product))
        {
            $product->delete();
            return $this->sendResponse(new ProductResource($product) ,'Product deleted successfully' );
        }
        else
        {
            return $this->sendError('you dont have rights' , $errorMessage);
        }
    }
}
