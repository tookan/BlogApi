<?php

namespace App\Http\Controllers\api;

use DB;
use App\Http\Controllers\api\images;
use App\note;
use App\User;
use App\comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
//use PhpParser\Comment;


class notes extends Controller
{
    const pageSize = 6;

    protected function getPagesCount()
    {
        $count = Note::count();
        $count = ceil($count / self::pageSize);
        return response()->json($count);
    }

    protected function getUserPagesCount($username)
    {
        $userId = User::select('id')->where('name','=',$username)->pluck('id');
       $count = Note::where('user_id','=', $userId)->count();
       $count = ceil($count / self::pageSize);
       return $count;
    }

    protected function searchService($searchTerm)
    {
        $offset = 1;
        $count = Note::where('title', 'LIKE', $searchTerm . '%')->count();
        $notes = Note::select('users.name as username', 'notes.body as body',
            'notes.id as id', 'notes.title as title','small_img as img', 'notes.created_at as date')
            ->where('title', 'LIKE', $searchTerm . '%')
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->skip(($offset - 1)*10)
            ->take(10)
            ->get();
        foreach ($notes as $note) {
            $note['body'] = html_entity_decode($note['body']);
        }
        return response()->json(['notes'=> $notes, 'count' => $count ]);
    }

    protected function noteCreate(Request $request)
    {
        $this->validate($request, ['title' => 'required| max: 120', 'body' => 'required'],['body' => 'Please fill your article contents']);
        $input = $request->all();
        $input['body'] = htmlentities($input['body']);
        $input['user_id'] = Auth::user()['id'];
        $currentNote = Note::create($input);
        if ($request->hasFile('photo')) {
            $image = $request->photo;
            $noteId = $currentNote->id;
            $pathway = Storage::disk('public')->put('photo/n' . $noteId , $image);
            Storage::disk('public')->makeDirectory('small_photo/n'.$noteId);
            $imgs['big_img'] = Storage::disk('public')->url($pathway);
            $imgs['small_img'] = Storage::disk('public')->url('small_'.$pathway);
           images::imageCrop( $pathway);
           Note::where('id',$noteId)->update($imgs);
        }
        return response('Created successfully', 200);
    }

    protected function getAllByAll($offset = 1)
    {
        $notes = Note::select('notes.id as id', 'title', 'body', 'users.name as username','small_img as img')
            ->skip(($offset - 1) * self::pageSize)->take(self::pageSize)
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->orderBy('notes.created_at','desc')
            ->get();
        foreach ($notes as $note) {
            $note['body'] = substr(html_entity_decode($note['body']), 0, 120);
        }
        $pagesCount = $this->getPagesCount();

        return response()->json(['notes' => $notes, 'pagesCount' => $pagesCount]);
    }

    protected function getAllByUser($username, $pageNumber)
    {
        $notes = User::select('users.name as username', 'notes.body as body', 'notes.id as id',
            'notes.created_at as date','notes.title as title','small_img as img')
            ->where('name', $username)
            ->join('notes', 'users.id', '=', 'notes.user_id')
            ->skip(($pageNumber - 1) * self::pageSize)
            ->take(self::pageSize)
            ->orderBy('notes.created_at','desc')
            ->get();
        foreach ($notes as $note) {
            $note['body'] = substr(html_entity_decode($note['body']), 0, 120);
        }
        $pagesCount = $this->getUserPagesCount($username);
        $profile = users::getUserProfile($username);
        return response()->json(['notes'=> $notes,'pagesCount'=> $pagesCount, 'profile'=>$profile], 200);
    }

    protected function getDetailedNote($id)
    {
        $note = Note::select('notes.id as id', 'title', 'body', 'users.name as username','notes.created_at as date','big_img as img')
            ->where('notes.id', $id)
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->get()
            ->first();
        $note['body'] = html_entity_decode($note['body']);
        return response()->json($note, 200);
    }

    function noteUpdate(Request $request)
    {
        $this->validate($request, ['title' => 'required', 'body' => 'required', 'id' => 'required'],
            ['body.required' => 'Please fill your article contents']);
        $input = $request->all();
        $input['body'] = htmlentities($input['body']);
        $user = Auth::user();
        $noteId = $input['id'];
        $noteUserId = Note::where('id', '=', $input['id'])->pluck('user_id')->first();
        if ($user['id'] == $noteUserId || $user['status'] == 'admin') {
           Note::where('id', $input['id'])->update(['title' => $input['title'], 'body' => $input['body']]);
            if ($request->hasFile('photo')) {
                $image = $request->photo;
                images::deleteImages($noteId);
                $pathway = Storage::disk('public')->put('photo/n' . $noteId , $image);
                Storage::disk('public')->makeDirectory('small_photo/n'.$noteId);
                $imgs['big_img'] = Storage::disk('public')->url($pathway);
                $imgs['small_img'] = Storage::disk('public')->url('small_'.$pathway);
                images::imageCrop( $pathway);
                Note::where('id',$noteId)->update($imgs);
            }
            return $this->getDetailedNote($noteId);
        } else return response('This note don\'t belongs to you', 401);
    }

    protected function noteDelete(Request $request)
    {
        $this->validate($request, ['id' => 'required']);
        $id = $request->id;
        $user = Auth::user();
        $noteUserId = Note::where('id', '=', $id)->pluck('user_id')->first();
        if ($user['id'] == $noteUserId || $user['status'] == 'admin') {
            Note::where('id', $id)->delete();
            comment::where('note_id', $id)->delete();
            images::deleteImages($id);
        } else return response('This note don\'t belongs to you', 401);
    }
}
