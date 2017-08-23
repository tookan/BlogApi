<?php

namespace App\Http\Controllers\api;


use App\comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class comments extends Controller
{
    function sendComment(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, ['body' => 'required|max:700',
            'note_id' => 'required']);
        $input = $request->only('body', 'note_id');
        if ($user['status'] !== 'admin') $input['user_id'] = Auth::user()['id'];
        else $input['user_id'] = $request->user_id;
        comment::create($input);
        return $this->getComments($request['note_id']);
    }

    function getComments($noteId)
    {
        $forResponse = comment::select('body', 'date', 'users.name')
            ->where('note_id', $noteId)
            ->join('users', 'comments.user_id', 'users.id')->get();
        return response()->json($forResponse, 200);
    }

    function getCommentsForUser(Request $request)
    {
        $offset = $request->offset;
        $userId = $request->id;
        $count = comment::where('user_id', $userId)->count();
        $count = ceil($count / 10);
        $comments = comment::where('user_id', $userId)
            ->skip(($offset - 1) * 10)
            ->take(10)
            ->get();
        return response()->json(['comments'=> $comments, 'count' => $count], 200);
    }

    function updateComment(Request $request)
    {
        $status = Auth::user()['status'];
        if ($status !== 'admin') return response('You are not allowed to do this', 401);
        else {
            $comment = $request->all();
            comment::where('id', $comment['id'])->update($comment);
            return response('Updated!', 200);
        }
    }

    function deleteComment(Request $request)
    {
        $status = Auth::user()['status'];
        if ($status !== 'admin') return response('You are not allowed to do this', 401);
        else {
            $comment = $request->all();
            comment::where('id', $comment['id'])->first()->delete();
            return response('Deleted!', 200);
        }
    }
}
