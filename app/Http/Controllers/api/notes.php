<?php

namespace App\Http\Controllers\api;
use DB;
use App\note;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class notes extends Controller
{
    const pageSize = 6;
   public function getAllByAll($offset = 1){
        $notes = Note::select('notes.id as id', 'title', 'body','users.name as username')->skip(($offset-1)*self::pageSize)->take(self::pageSize)
            ->join('users','notes.user_id', '=','users.id')->get();
        foreach($notes as $note){
            $note['body'] = substr($note['body'],0,65);
        }
          return response()->json($notes);
    }
    public function getAllByUser($username, $pageNumber){
$notes=User::select('users.name as name','notes.body as body','notes.id as id','notes.title as title')
    ->where('name',$username)
    ->join('notes','users.id','=','notes.user_id')
    ->skip(($pageNumber-1)*self::pageSize)
    ->take(self::pageSize)->get();
        foreach($notes as $note){
            $note['body'] = substr($note['body'],0,65);
        }
return response()->json($notes,200);
    }
    public function getDetailedNote($id){
        $note= Note::select('notes.id as id','title','body','users.name as username')->where('notes.id',$id)
            ->join('users','notes.user_id','=','users.id')
            ->get()
            ->first();
        return response()->json($note,200);
    }
    public function getPagesCount() {
        $count =  DB::table('notes')->select(DB::raw("COUNT(id)/".self::pageSize." as count" ))->get()->first();
       $count = ceil($count->count);
        return response()->json($count);
}
public function searchService($searchTerm) {
$response = Note::select('title','id')->where('title','LIKE', $searchTerm.'%')->get();
return response()->json($response);
}
public function noteCreate(Request $request) {
    $this->validate($request, ['title' =>'required', 'body' => 'required']);
    $input = $request->all();
    $input['user_id'] = Auth::user()['id'];
    note::create($input);
   return response()->json($request,200);
}
function noteUpdate(Request $request){
    $this->validate($request, ['title' =>'required', 'body' => 'required','id'=>'required']);
    $input = $request->all();
    $userId = Auth::user()['id'];
    $noteUserId =  Note::where('id','=', $input['id'])->pluck('user_id')->first();
    if($userId == $noteUserId){
        DB::table('notes')->where('id',$input['id'] )->update(['title' => $input['title'], 'body'=>$input['body'] ]);
    }else return response('This note don\'t belongs to you',401);
}
    function noteDelete(Request $request){
        $this->validate($request, ['id'=>'required']);
        $id= $request->id;
        $userId = Auth::user()['id'];
        $noteUserId =  Note::where('id','=', $id)->pluck('user_id')->first();
        if($userId == $noteUserId){
            DB::table('notes')->where('id', $id)->delete();
            DB::table('comments')->where('note_id', $id)->delete();
        }else return response('This note don\'t belongs to you',401);
    }


}
