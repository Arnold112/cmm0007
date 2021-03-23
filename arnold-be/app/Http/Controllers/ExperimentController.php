<?php

namespace App\Http\Controllers;

use App\Models\Experiment;
use App\Models\ExperimentFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExperimentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user does not exist'
            ], 401);
        }

        if ($user->type == 'student') {
            $experiments = Experiment::whereUserId($user->id)->get();

            return response()->json([
                'status' => true,
                'message' => 'student\'s experiments retrieved successfully',
                'data' => $experiments
            ], 200);
        } else if ($user->type == 'eao') {
            $experiments = $user->assigned_experiments;

            return response()->json([
                'status' => true,
                'message' => 'eao\'s experiments retrieved successfully',
                'data' => $experiments
            ], 200);
        } else if ($user->type == 'admin') {
            $experiments = Experiment::all();

            return response()->json([
                'status' => true,
                'message' => 'all experiments retrieved successfully',
                'data' => $experiments
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'something went wrong',
            'data' => null
        ], 400);
    }


    public function store(Request $request)
    {
        //
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'user does not exist'
            ], 401);
        }

        $experiment = new Experiment();
        $experiment->name = $request->name;
        $experiment->description = $request->description;
        $experiment->user_id = $user->id;
        $experiment->save();



        if ($request->hasfile('additional_files')) {


            foreach ($request->file('additional_files') as $file) {

                $extension = $file->extension();
                $file->storeAs('/public/files', "files_$user->id." . $extension);
                $url = Storage::url("/public/files/files_$user->id." . $extension);

                $name = time() . '.' . $file->getClientOriginalName();

                $file = new ExperimentFiles();
                $file->name = $name;
                $file->type = $extension;
                $file->file_path = $url;
                $file->experiment_id = $experiment->id;
                $file->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'experiment created successfully',
            'data' => $experiment
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Experiment  $experiment
     * @return \Illuminate\Http\Response
     */
    public function show(Experiment $experiment)
    {
        //
        $experiment = Experiment::whereId($experiment->id)->first();

        if (!$experiment) {
            return response()->json([
                'status' => false,
                'message' => 'experiment does not exist',
                'data' => null
            ], 200);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'experiment retrieved successfully',
                'data' => $experiment
            ], 200);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Experiment  $experiment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Experiment $experiment)
    {
        //
    }

    public function handleExperiment(Request $request)
    {
        //
        $user = auth()->user();

        if (!$user || $user->type == 'student') {
            return response()->json([
                'status' => false,
                'message' => 'unauthorized action'
            ], 401);
        }

        $experiment = Experiment::whereId($request->experiment_id)->first();

        if (!$experiment) {
            return response()->json([
                'status' => false,
                'message' => 'experiment does not exist',
                'data' => null
            ], 200);
        } else {
            $experiment->status = $request->status == false ? 0 : 2;
            $experiment->save();
        }
    }

    public function assignEao(Request $request)
    {
        $user = auth()->user();

        if (!$user || ($user->type != 'admin')) {
            return response()->json([
                'status' => false,
                'message' => 'unauthorized action'
            ], 401);
        }

        $experiment = Experiment::whereId($request->experiment_id)->first();

        if (!$experiment) {
            return response()->json([
                'status' => false,
                'message' => 'experiment does not exist',
                'data' => null
            ], 200);
        }

        $eao_ids = json_decode($request->eao_ids, true);

        if (empty($eao_ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No EAO\'s selected'
            ], 200);
        }

        $experiment->eaos()->sync($eao_ids);

        return response()->json([
            'status' => true,
            'message' => 'eaos assigned to experiment successfully',
            'data' => $experiment
        ], 200);
    }
}
