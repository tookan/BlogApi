<?php

namespace App\Http\Controllers;
use DB;
use App\note;
use App\User;
use Illuminate\Http\Request;


class notes extends Controller
{
   public function getAll(){
        $notes = DB::table('notes')->select('id','title','body')->get()->all();
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

}
