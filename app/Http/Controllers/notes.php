<?php

namespace App\Http\Controllers;
use DB;
use App\note;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;


class notes extends Controller
{
    const pageSize = 6;
   public function getAllByAll($offset = 1){
        $notes = Note::select(DB::raw('id, title, SUBSTRING(body,1,45) as body'))->skip(($offset-1)*self::pageSize)->take(self::pageSize)->get();
          return response()->json($notes);
    }
    public function getAllByUser($username){
$user = User::where('user',$username)->first();
$notes = $user->notes()->select('title','body')->get()->all();
    }
    public function getDetailedNote($id){
        $note= Note::where('id',$id)->get()->first();
        return response()->json($note);
    }
    public function getPagesCount() {
        $count =  DB::table('notes')->select(DB::raw("COUNT(id)/".self::pageSize." as count" ))->get()->first();
       $count = ceil($count->count);
        return response()->json($count);
}
public function getUser(){
        $user = Auth::user();
       return response()->json($user);
}

public function searchService($searchTerm){

$response = Note::select('title','id')->where('title','LIKE', $searchTerm.'%')->get();
return response()->json($response);

}
}
