<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ProductController extends Controller
{
    public function show($id)
    {
        return view('product', ['id' => $id]);
    }
}
