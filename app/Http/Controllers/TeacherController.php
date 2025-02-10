<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TeacherController extends Controller
{
    // Display a list of teachers
    public function index()
    {
        $teachers = Teacher::where('institution_id', Auth::user()->institution_id)->get();
        return view('teachers.index', compact('teachers'));
    }

    // Show form to create a new teacher
    public function create()
    {
        return view('teachers.create');
    }
    // Show form to edit a teacher
    public function edit($id)
    {
        $teacher = Teacher::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('teachers.edit', compact('teacher'));
    }

    // Show details of a specific teacher
    public function show($id)
    {
        $teacher = Teacher::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('teachers.show', compact('teacher'));
    }

    public function destroy($id)
    {
        try {
            $teacher = Teacher::where('institution_id', Auth::user()->institution_id)
                ->findOrFail($id);
            $user = $teacher->user;
            DB::transaction(function () use ($teacher, $user) {
                $teacher->delete();
                $user->delete();
            });
            return redirect()->route('teachers.index')->with('success', 'Teacher and User deleted successfully!');

        } catch (Exception $e) {
            Log::error('Error deleting teacher and user: ' . $e->getMessage());
            return redirect()->route('teachers.index')->with('error', 'An error occurred while deleting the teacher and user. Please try again.');
        }
    }

    // Store a new teacher in the database
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Check if the email already exists
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email has already been taken.']);
            }
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
            ]);
            $role = Role::where('name', 'teacher')->first();
            $user->roles()->attach($role->id);

            Teacher::create([
                'user_id' => $user->id,
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
            ]);
            DB::commit();
            return redirect()->route('teachers.index')->with('success', 'Teacher created successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating teacher and user: ' . $e->getMessage(), [
                'exception' => $e,
                'teacher_name' => $request->name,
                'teacher_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the teacher. Please try again.']);
        }
    }

    // Update teacher details in the database
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Find the teacher and associated user
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;

            // Check if the email already exists (except for the current user)
            $emailExists = User::where('email', $request->email)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($emailExists) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The email has already been taken.']);
            }

            // Update the user information
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
            ]);


            $role = Role::where('name', 'teacher')->first();
            $user->roles()->sync([$role->id]);

            $teacher->update([
                'institution_id' => Auth::user()->institution_id,
            ]);

            // Commit the transaction
            DB::commit();

            return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating teacher and user: ' . $e->getMessage(), [
                'exception' => $e,
                'teacher_id' => $id,
                'teacher_name' => $request->name,
                'teacher_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update teacher. Please try again.']);
        }
    }

}
