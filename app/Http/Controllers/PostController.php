<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        // return response()->json(['data' => $post]);
        return PostDetailResource::collection($posts->loadMissing('writer:id,username', 'comments:id,post_id,user_id,commnets_content')); //gunakan loadmissing untuk menampilkan data tertentu yang ada di table, searah dengan whenLoaded()
    }

    public function show($id){
        $post = Post::with('writer:id,username', 'comments:id,post_id,user_id,commnets_content')->findOrFail($id);
        return new PostDetailResource($post);
    }

    public function store(Request $request){
        $request -> validate([
            'title' => 'required|max:255',
            'news_content' => 'required',

        ]);


        $image = null;

        if ($request -> file) {
            $fileName = $this -> generateRandomString();
            $extension = $request -> file -> extension();

            $image = $fileName. '.' .$extension;
            Storage::putFileAs('image', $request->file, $image);


        }

        // return response()->json('sudah dapat digunakan')
        $request['image'] = $image;
        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id){
        $request -> validate([
            'title' => 'required|max:255',
            'news_content' => 'required',

        ]);

        if ($request -> file) {
            $fileName = $this -> generateRandomString();
            $extension = $request -> file -> extension();

            $image = $fileName. '.' .$extension;
            Storage::putFileAs('image', $request->file, $image);


        }

        $request['image'] = $image;

        $post = Post::findOrFail($id);
        $post->update($request -> all());

        // // return response()->json('sudah dapat diigunakan');
        // $request['author'] = Auth::user()->id;
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function delete($id){
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
           'message' => "keapus broski"
        ]);
    }

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
