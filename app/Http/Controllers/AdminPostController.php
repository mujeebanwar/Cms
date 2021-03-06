<?php

namespace App\Http\Controllers;

use App\Category;
use App\Photo;
use App\Post;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class AdminPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $dir ='photos';
    protected $data=[];
    public function index()
    {
        //
        $posts = Post::paginate(10);


        return view('admin.posts.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::pluck('name','id')->all();
        return view('admin.posts.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $this->data = Validator::make($request->all(), [

            'title' => 'required||max:256',
            'photo_id'=>'required|image',
            'category_id'=>'required',
            'body' => 'required',

            ],

            [
                'title.required'=>'Title Required',
                'title.max'=>'Maximum 256 Character Allowed',
                'category_id.required' =>'Category Required',
                'body.required' =>'Description Required',
                'photo_id.required' =>'Photo Required',
                'photo_id.image' =>'Only Image Allowed',

            ]
        )->validate();


       if ($file=$request->file('photo_id'))
       {
           $this->uploadImage($file);
       }

        if ($request->category_id!=null)
        {
            $this->data['category_id'] = $request->category_id;
        }

        Auth::user()->posts()->create($this->data);
        return redirect('admin/posts');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {

        $categories =Category::pluck('name','id')->all();

        return view('admin.posts.edit',compact('post','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->data = Validator::make($request->all(), [

            'title' => 'required||max:256',
            'photo_id'=>'image',
            'category_id'=>'required',
            'body' => 'required',


        ],

            [
                'title.required'=>'Title Required',
                'category_id.required' =>'Category Required',
                'title.max'=>'Maximum 256 Character Allowed',
                'body.required' =>'Description Required',
                'photo_id.image' =>'Only Image Allowed',

            ]
        )->validate();


//        dd($post);


        if ($file=$request->file('photo_id'))
        {
           $this->uploadImage($file);
           unlink(public_path().$post->photo->path);
           $post->photo->delete();
        }

        $post->update($this->data);

        return redirect( URL::to('admin/posts'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //

        unlink(public_path().$post->photo->path);
        $post->delete();
        return redirect('admin/posts');

    }

    public function uploadImage($file)
    {
        $photo_name =  Carbon::now()->format('Y-m-d').'_post_'.$file->getClientOriginalName();

        $file->move($this->dir,$photo_name);

        $photo = Photo::create(['path'=>$photo_name]);

        $this->data['photo_id'] = $photo->id;
    }

    public function post(Post $post)

    {


        $categories = Category::all();
        $comments = $post->comments()->whereIsActive(1)->get();
        return view('public.post',compact('post','categories','comments'));
    }
}
