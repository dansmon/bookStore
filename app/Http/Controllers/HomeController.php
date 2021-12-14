<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //  $this->middleware('auth');
    }


    public function getResRen()
    {
        if (auth()->user() == null) {
            return redirect('/home');
        } else {
            if (auth()->user()->is_user) {
                return view('user.userResRen');
            } else {
                return view('admin.adminResRen');
            }
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user() == null) {
            return view('home');
        } else {
            if (auth()->user()->is_admin) {
                $books = Book::orderBy('naslov', 'desc')->get();
                return view('admin.home');
            } else if (auth()->user()->is_user) {
                return view('user.home');
            }
        }
    }

    public function indexBlank()
    {
        return redirect('/home');
    }
}
