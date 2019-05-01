<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use App\CoffeeBranch;
use App\CoffeeFrame;
use App\Batch;
use App\CoffeeTree;;
use App\Log;
use App\Device;

class ApiController extends Controller
{
	public function postBranch(Request $request)
	{
		$tree = CoffeeTree::where('id', '=', $request->TreeID)->first();
		if ($tree != null) {
			$batch = Batch::where('id', '=', $tree->BatchID)->first();
			if ($batch != null) {
				$file = $request->file('file');
				$filename = 'sensors.'.$file->getClientOriginalExtension();
				$branch = new CoffeeBranch;
				$branch->TreeID = $request->TreeID;
				$branch->Type = $request->Type;
				$branch->Index = $request->Index;
				$branch->Date = $request->Date;
				$branch->StemID = $request->StemID;
				$branch->FolderURL = "";
				$branch->save();
				$branch->FolderURL = "Device".$batch->Device."/Batch".$tree->BatchID.'/Tree'.$branch->TreeID.'/Branch'.$branch->id;
				$branch->save();

				$log = new Log;
				$log->Device = $batch->Device;
				$log->Log = "Se crea la rama #".$request->Index;
				$log->save();

				Storage::disk('local')->putFileAs($branch->FolderURL,$file,$filename, 'private');

				return response()->json([
					'id' => $branch->id
				]);
			} else {
				return response()->json([
					'error' => 'error02',
					'error_description' => 'El lote no existe'
				]);
			}
		} else {
			return response()->json([
				'error' => 'error03',
				'error_description' => 'El árbol no existe'
			]);
		}
	}
	
	public function postBatch(Request $request)
	{
		$batch = new Batch;
		$batch->Age = $request->Age;
		$batch->Trees = $request->Trees;
		$batch->Branches = $request->Branches;
		$batch->Name = $request->Name;
		$batch->Stems = $request->Stems;
		$batch->Device = $request->Device;
		$id = $batch->save();
		$log = new Log;
		$log->Device = $request->Device;
		$log->Log = "Se crea el lote con nombre ".$request->Name;
		$log->save();
    	return response()->json([
    		'id' => $batch->id
		]);
	}
	
	public function postTree(Request $request)
	{
		$batch = Batch::where('id', '=', $request->BatchID)->first();
		if ($batch !== null) {
			$tree = new CoffeeTree;
			$tree->BatchID = $request->BatchID;
			$tree->Lat = $request->Lat;
			$tree->Lng = $request->Lng;
			$tree->Index = $request->Index;
			$id = $tree->save();
			$log = new Log;
			$log->Device = $batch->Device;
			$log->Log = "Se crea el arbol #".$request->Index;
			$log->save();
			return response()->json([
				'id' => $tree->id
			]);
		} else {
			return response()->json([
				'error' => 'error02',
				'error_description' => 'El lote no existe'
			]);
		}
	}
	public function postFrame(Request $request)
	{
		$branch = CoffeeBranch::find($request->BranchID);
		if($branch != null) {
			$tree = CoffeeTree::where('id', '=', $branch->TreeID)->first();
			if ($tree != null) {
				$batch = Batch::where('id', '=', $tree->BatchID)->first();
				if ($batch != null) {
					
					$file = $request->file('file');
					$filename = $request->Time.'.'.$file->getClientOriginalExtension();
					$frame = new CoffeeFrame;
					$frame->BranchID = $request->BranchID;
					$frame->Time = $request->Time;
					$frame->Factor = $request->Factor;
					$frame->FileURL = $branch->FolderURL.'/'.$filename;
					$id = $frame->save();
			
			
					Storage::disk('local')->putFileAs($branch->FolderURL,$file,$filename, 'private');
					return response()->json([
						'id' => $frame->id
					]);

					$log = new Log;
					$log->Device = $batch->Device;
					$log->Log = "Se crea el frame del segundo ".$request->Time." de la rama #".$branch->Index;
					$log->save();

					Storage::disk('local')->putFileAs("Batch".$request->BatchID.'/Tree'.$branch->TreeID.'/Branch'.$branch->id,$file,$filename, 'private');

					return response()->json([
						'id' => $branch->id
					]);
				} else {
					return response()->json([
						'error' => 'error02',
						'error_description' => 'El lote no existe'
					]);
				}
			} else {
				return response()->json([
					'error' => 'error03',
					'error_description' => 'El árbol no existe'
				]);
			}
		} else {
			return response()->json([
				'error' => 'error04',
				'error_description' => 'La rama no existe'
			]);
		}


	}



	public function postDevice(Request $request)
	{
		$device = Device::where('Udid', '=', $request->Udid)->first();
		if ($device === null) {
			$device = new Device;
			$device->Udid = $request->Udid;
			$device->Model = $request->Model;
			$device->Device = $request->Device;
			$device->Product = $request->Product;
			$device->save();
			return response()->json([
				
			]);
		}
		return response()->json([
			'error' => 'error01',
			'error_description' => 'Dispositivo ya registrado'
		]);
	}
}