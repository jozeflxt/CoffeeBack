<?php

use Illuminate\Http\Request;

use App\CoffeeBranch;
use App\CoffeeFrame;
use App\CoffeeTree;
use App\Batch;
use App\Device;
use App\Log;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/getDevices',function(){
    return Device::all(); 
});
Route::get('/getTrees/{batchID}',function($batchID){
     return CoffeeTree::where('BatchID',$batchID)->get(); 
});
Route::get('/getBranchs/{treeID}',function($treeId){
     return CoffeeBranch::where('TreeId',$treeId)->get(); 
});
Route::get('/getBatches/{device}',function($name){
     return Batch::where('Device',$name)->get(); 
});
Route::get('/getLogs/{device}',function($name){
     return Log::where('Device',$name)->get(); 
});
Route::get('/getFrames/{branchId}',function($branchId){
     return CoffeeFrame::where('BranchID',$branchId)->get(); 
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/postBatch', ['as'=>'postBatch','uses'=>'ApiController@postBatch']);
Route::post('/postTree', ['as'=>'postTree','uses'=>'ApiController@postTree']);
Route::post('/postBranch', ['as'=>'postBranch','uses'=>'ApiController@postBranch']);
Route::post('/postFrame', ['as'=>'postFrame','uses'=>'ApiController@postFrame']);
Route::post('/postDevice', ['as'=>'postDevice','uses'=>'ApiController@postDevice']);