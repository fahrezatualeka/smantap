<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class IndexAdminController extends Controller
{
    public function index()
{
    // Logic untuk admin
    return view('admin.index');
}
}