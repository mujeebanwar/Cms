<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentReply;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $comments = Comment::paginate(10);
        return view('admin.comments.index',compact('comments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       $user = Auth::user();


       $data = [

           'post_id' => $request->post_id,
           'author'  => $user->name,
           'photo'   => $user->photo?$user->photo->id:0,
           'email'   => $user->email,
           'body'    => $request->body,
       ];

       Comment::create($data);

       $request->session()->flash('comment_massage','Your massage has been submitted and its waiting moderation');

       return redirect()->back();
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

        $post = Post::findOrFail($id);

        $comments = $post->comments;

        return view('admin.comments.show',compact('comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
     $comment->update($request->all());

     return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function singleReplyComment($id)
    {
        $reply = CommentReply::findOrFail($id);

        return view('admin.comments.single',compact('reply'));
    }
}
