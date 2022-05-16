<?php

namespace App\Controllers;
use App\Models\Comment;

class Home extends BaseController
{
	public function index()
	{
    // return redirect()->to('/comments');
		return view('welcome_message');
	}
}
