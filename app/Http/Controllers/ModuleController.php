<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ModuleController extends Controller
{
    // Display a list of modules
    public function index()
    {
        $modules = Module::where('institution_id', Auth::user()->institution_id)
            ->orderBy('name')
            ->get();
        return view('modules.index', compact('modules'));
    }

    // Show form to create a new module
    public function create()
    {
        return view('modules.create');
    }
    // Show form to edit a module
    public function edit($id)
    {
        $module = Module::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('modules.edit', compact('module'));
    }

    // Show details of a specific module
    public function show($id)
    {
        $module = Module::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('modules.show', compact('module'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $module = Module::where('institution_id', Auth::user()->institution_id)
                ->findOrFail($id);

            $module->delete();
            DB::commit();
            return redirect()->route('modules.index')->with('success', 'Module and User deleted successfully!');

        } catch (Exception $e) {
            Log::error('Error deleting module and user: ' . $e->getMessage());
            return redirect()->route('modules.index')->with('error', 'An error occurred while deleting the module and user. Please try again.');
        }
    }

    // Store a new module in the database
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            Module::create([
                'name' => $request->name,
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
            ]);
            DB::commit();
            return redirect()->route('modules.index')->with('success', 'Module created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating module and user: ' . $e->getMessage(), [
                'exception' => $e,
                'module_name' => $request->name,
                'module_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the module. Please try again.']);
        }
    }

    // Update module details in the database
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $module = Module::findOrFail($id);

            $module->update([
                'name' => $request->name,
                'institution_id' => Auth::user()->institution_id,
            ]);
            DB::commit();
            return redirect()->route('modules.index')->with('success', 'Module updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating module and user: ' . $e->getMessage(), [
                'exception' => $e,
                'module_id' => $id,
                'module_name' => $request->name,
                'module_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to Update module: ']);
        }
    }
}
