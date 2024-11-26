<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class IndexPetugasPenagihanController extends Controller
{
    public function index()
{
    // Logic untuk admin
    return view('petugas_penagihan.index');
}
}