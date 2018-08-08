<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
  public function index() {
    $title = "Aleph Cluster Inventory Tool";
    return view('pages.index', compact('title'));
  }
  
  public function dashboard() {
    return view('pages.dashboard');
  }
}
