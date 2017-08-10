<?php

namespace App\Http\Controllers\api;

use DB;
use App\Http\Controllers\api\images;
use App\note;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class notes extends Controller
{
    const pageSize = 6;

    public function getPagesCount()
    {
        $count = Note::count();
        $count = ceil($count / self::pageSize);
        return response()->json($count);
    }

    public function getUserPagesCount($username)
    {
        $userId = User::select('id')->where('name','=',$username)->pluck('id');
       $count = Note::where('user_id','=', $userId)->count();
       $count = ceil($count / self::pageSize);
       return $count;
    }

    public function searchService($searchTerm)
    {
        $response = Note::select('title', 'id')
            ->where('title', 'LIKE', $searchTerm . '%')
            ->get();
        return response()->json($response);
    }

    public function noteCreate(Request $request)
    {
        $this->validate($request, ['title' => 'required', 'body' => 'required']);
        $input = $request->all();
        $input['user_id'] = Auth::user()['id'];
        $currentNote = note::create($input);
        if ($request->hasFile('photo')) {
            $image = $request->photo;
            $imageExten = $image->getClientOriginalExtension();
            $noteId = $currentNote->id;
            Storage::disk('public')->putFileAs('photo/n' . $noteId . '/', $image, 'big.' . $imageExten);
            images::imageCrop($noteId, $imageExten);
        }
        return response('ok', 200);
    }

    public function getAllByAll($offset = 1)
    {
        $notes = Note::select('notes.id as id', 'title', 'body', 'users.name as username')
            ->skip(($offset - 1) * self::pageSize)->take(self::pageSize)
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->orderBy('notes.created_at','desc')
            ->get();
        foreach ($notes as $note) {
            $note['body'] = substr($note['body'], 0, 120);
            if (images::getImageUrl($note['id'], 1)) $note['img'] = images::getImageUrl($note['id'], 1);
        }
        $pagesCount = $this->getPagesCount();
        return response()->json(['notes' => $notes, 'pagesCount' => $pagesCount]);
    }

    public function getAllByUser($username, $pageNumber)
    {
        $notes = User::select('users.name as username', 'notes.body as body', 'notes.id as id', 'notes.title as title')
            ->where('name', $username)
            ->join('notes', 'users.id', '=', 'notes.user_id')
            ->skip(($pageNumber - 1) * self::pageSize)
            ->take(self::pageSize)
            ->orderBy('notes.created_at','desc')
            ->get();
        foreach ($notes as $note) {
            $note['body'] = substr($note['body'], 0, 120);
            if (images::getImageUrl($note['id'], 1)) $note['img'] = images::getImageUrl($note['id'], 1);
        }
        $pagesCount = $this->getUserPagesCount($username);
        return response()->json(['notes'=> $notes,'pagesCount'=> $pagesCount], 200);
    }

    public function getDetailedNote($id)
    {
        $note = Note::select('notes.id as id', 'title', 'body', 'users.name as username')->where('notes.id', $id)
            ->join('users', 'notes.user_id', '=', 'users.id')
            ->get()
            ->first();
        if (images::getImageUrl($note['id'])) $note['img'] = images::getImageUrl($note['id']);
        return response()->json($note, 200);
    }

    function noteUpdate(Request $request)
    {
        $this->validate($request, ['title' => 'required', 'body' => 'required', 'id' => 'required']);
        $input = $request->all();
        $userId = Auth::user()['id'];
        $noteId = $input['id'];
        $noteUserId = Note::where('id', '=', $input['id'])->pluck('user_id')->first();
        if ($userId == $noteUserId) {
            DB::table('notes')->where('id', $input['id'])->update(['title' => $input['title'], 'body' => $input['body']]);
            if ($request->hasFile('photo')) {
                $image = $request->photo;
                $imageExten = $image->getClientOriginalExtension();
                images::deleteImages($noteId);
                Storage::disk('public')->putFileAs('photo/n' . $noteId . '/', $image, 'big.' . $imageExten);
                images::imageCrop($noteId, $imageExten);
            }
            return $this->getDetailedNote($noteId);
        } else return response('This note don\'t belongs to you', 401);
    }

    function noteDelete(Request $request)
    {
        $this->validate($request, ['id' => 'required']);
        $id = $request->id;
        $userId = Auth::user()['id'];
        $noteUserId = Note::where('id', '=', $id)->pluck('user_id')->first();
        if ($userId == $noteUserId) {
            DB::table('notes')->where('id', $id)->delete();
            DB::table('comments')->where('note_id', $id)->delete();
            images::deleteImages($id);
        } else return response('This note don\'t belongs to you', 401);
    }
}
