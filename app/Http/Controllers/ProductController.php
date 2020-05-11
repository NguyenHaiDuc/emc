<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Http\Requests\CommentRequest;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->middleware('auth');
        $this->productRepo = $productRepo;
    }

    public function detail($id)
    {
        try {
            $product = $this->productRepo->findOrFail($id);

            return view('products.detail', ['product' => $product]);
        } catch (ModelNotFoundException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function comment(CommentRequest $request, $id)
    {
        try {
            $this->productRepo->findOrFail($id);
            Comment::create([
                'content' => $request->content,
                'user_id' => Auth::user()->id,
                'product_id' => $id,
                'status' => Comment::ACTIVE,
            ]);

            return redirect()->back();
        } catch (ModelNotFoundException $e) {
            throw new Exception($e->getMessages());
        }
    }
}
