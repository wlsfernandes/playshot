<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\StudentTask;
use App\Models\Task;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentTaskController extends Controller
{

    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        $task = Task::where('resource_id', $id)->first();
        return view('tasks.edit', compact('resource', 'task'));
    }


    public function addTask(Request $request)
    {
        try {
            DB::beginTransaction();
            $requestData = $request->all();
            $requestData['student_id'] = Auth::user()->id;
            StudentTask::create($requestData);
            DB::commit();
            session()->flash('success', 'Task added successfully.');
            Log::info('Task uploaded successfully.');
            return redirect()->back()->with('success', 'Task answered successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create task file: ' . $e->getMessage());
            Log::error('Error uploading file: ' . $e->getMessage());
            return redirect()->route('courses.myCourses');
        }
    }

}
