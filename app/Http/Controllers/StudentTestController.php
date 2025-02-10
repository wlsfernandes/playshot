<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\StudentTest;
use App\Models\Test;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentTestController extends Controller
{

    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        $test = Test::where('resource_id', $id)->first();
        // start count student time
        $studentTest = StudentTest::firstOrCreate(
            ['test_id' => $test->id, 'student_id' => Auth::user()->id, 'answer' => ' '],
            ['start_time' => now()]
        );
        return view('tests.edit', compact('resource', 'test', 'studentTest'));
    }


    public function submitTest(Request $request)
    {
        try {
            $studentTest = StudentTest::where('student_id', Auth::id())
                ->where('test_id', $request->test_id)
                ->firstOrFail();
            $studentTest->answer = $request['answer'];
            $submittedAt = now();
            $startTime = $studentTest->start_time;
            $isWithinTime = $startTime->diffInMinutes($submittedAt) <= 50;
            $studentTest->submitted_at = $submittedAt;
            $studentTest->submitted_within_time = $isWithinTime;
            $studentTest->save();
            DB::commit();
            session()->flash('success', 'Test submitted successfully!');
            Log::info('Test submitted successfully for student ID: ' . Auth::id() . ' within time: ' . ($isWithinTime ? 'Yes' : 'No'));

            return redirect()->back()->with('success', 'Test submitted successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create task file: ' . $e->getMessage());
            Log::error('Error uploading file: ' . $e->getMessage());
            return redirect()->route('courses.myCourses');
        }
    }

}
