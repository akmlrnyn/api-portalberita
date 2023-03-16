<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request){
        $request -> validate([
            'post_id' => 'required|exists:posts,id',
            'commnets_content' => 'required'
        ]);
        $request['user_id'] = auth()->user()->id;
        $comment = Comment::create($request->all());

        return response()->json($comment);
    }
}
