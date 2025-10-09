<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatkulController extends Controller
{
    public function createMatkul(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required'  
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['user_id'] = auth()->id();
        Post::create($incomingFields);
    }
}
