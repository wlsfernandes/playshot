<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentController extends Controller
{
    // Display a list of students
    public function index()
    {
        $students = Student::where('institution_id', Auth::user()->institution_id)->get();
        return view('students.index', compact('students'));
    }

    // Show form to create a new student
    public function create()
    {
        return view('students.create');
    }
    // Show form to edit a student
    public function edit($id)
    {
        $student = Student::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('students.edit', compact('student'));
    }

    // Show details of a specific student
    public function show($id)
    {
        $student = Student::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);
        return view('students.show', compact('student'));
    }

    public function destroy($id)
    {
        try {
            $student = Student::where('institution_id', Auth::user()->institution_id)
                ->findOrFail($id);
            $user = $student->user;
            DB::transaction(function () use ($student, $user) {
                $student->delete();
                $user->delete();
            });
            return redirect()->route('students.index')->with('success', 'Student and User deleted successfully!');

        } catch (Exception $e) {
            Log::error('Error deleting student and user: ' . $e->getMessage());
            return redirect()->route('students.index')->with('error', 'An error occurred while deleting the student and user. Please try again.');
        }
    }

    // Store a new student in the database
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $emailExists = User::where('email', $request->email)->exists();

            if ($emailExists) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email has already been taken.']);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
            ]);
            
            $role = Role::where('name', 'student')->first();
            $user->roles()->attach($role->id);
            
            Student::create([
                'user_id' => $user->id,
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
            ]);

            DB::commit();

            return redirect()->route('students.index')->with('success', 'Student created successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating student and user: ' . $e->getMessage(), [
                'exception' => $e,
                'student_name' => $request->name,
                'student_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the student. Please try again.']);
        }
    }

    // Update student details in the database
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $student = Student::findOrFail($id);
            $user = $student->user;
            $emailExists = User::where('email', $request->email)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($emailExists) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email has already been taken.']);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password, // Update password only if provided
            ]);

            $role = Role::where('name', 'student')->first();
            $user->roles()->sync([$role->id]);

            $student->update([
                'institution_id' => Auth::user()->institution_id,
            ]);

            DB::commit();

            return redirect()->route('students.index')->with('success', 'Student updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating student and user: ' . $e->getMessage(), [
                'exception' => $e,
                'student_id' => $id,
                'student_name' => $request->name,
                'student_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to Update student: ']);
        }
    }


}
