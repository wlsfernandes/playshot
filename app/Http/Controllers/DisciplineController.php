<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discipline;
use App\Models\Module;
use App\Models\Certification;
use App\Models\Task;
use App\Models\Test;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\StorageS3;
use Exception;

class DisciplineController extends Controller
{
    // Display a list of disciplines
    public function index()
    {
        $disciplines = Discipline::where('institution_id', Auth::user()->institution_id)
            ->orderBy('title')
            ->get();
        return view('disciplines.index', compact('disciplines'));
    }
    public function listDisciplines()
    {
        $disciplines = Discipline::where('institution_id', Auth::user()->institution_id)->get();
        return view('disciplines.listdisciplines', compact('disciplines'));
    }

    public function myDisciplines()
    {
        $disciplines = Discipline::where('institution_id', Auth::user()->institution_id)
            ->whereHas('students') // Ensures there are associated students in the discipline_student table
            ->get();

        return view('disciplines.mydisciplines', compact('disciplines'));
    }

    // Show form to create a new discipline
    public function create()
    {
        $modules = Module::where('institution_id', Auth::user()->institution_id)->get();
        $certifications = Certification::where('institution_id', Auth::user()->institution_id)->get();
        return view('disciplines.create', compact('modules', 'certifications'));
    }
    // Show form to edit a discipline
    public function edit($id)
    {
        $discipline = Discipline::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);
        $modules = Module::where('institution_id', Auth::user()->institution_id)->get();
        $certifications = Certification::where('instituions_id', Auth::user()->institution_id)->get();
        return view('disciplines.edit', compact('discipline', 'modules', 'certifications'));
    }



    // Show details of a specific discipline
    public function show($id)
    {
        $discipline = Discipline::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);
        $modules = Module::where('institution_id', Auth::user()->institution_id)->get();
        return view('disciplines.show', compact('discipline', 'modules'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $discipline = Discipline::where('institution_id', Auth::user()->institution_id)->findOrFail($id);
            $discipline->delete();
            DB::commit();
            return redirect()->route('disciplines.index')->with('success', 'Discipline deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting discipline: ' . $e->getMessage());

            return redirect()->route('disciplines.index')->with('error', 'An error occurred while deleting the discipline. Please try again.');
        }
    }


    // Store a new discipline in the database
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Discipline::create([
                'title' => $request->title,
                'description' => $request->description,
                'small_description' => $request->small_description,
                'module_id' => $request->module,
                'certification_id' => $request->certification,
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
                'amount' => $request->amount ?? 0.00,
                'currency' => 'BRL',
            ]);
            DB::commit();
            return redirect()->route('disciplines.index')->with('success', 'Discipline created successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating discipline and user: ' . $e->getMessage(), [
                'exception' => $e,
                'title' => $request->title,
                'description' => $request->description,
                'small_description' => $request->small_description,
                'module_id' => $request->module,
                'institution_id' => Auth::user()->institution_id,
                'amount' => $request->amount ?? 0.00,
                'currency' => 'BRL',
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the discipline. Please try again.']);
        }
    }

    // Update discipline details in the database
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $discipline = Discipline::findOrFail($id);

            $discipline->update([
                'title' => $request->title,
                'description' => $request->description,
                'small_description' => $request->small_description,
                'module_id' => $request->module,
                'certification_id' => $request->certification,
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
                'amount' => $request->amount ?? 0.00,
                'currency' => 'BRL',
            ]);
            DB::commit();
            return redirect()->route('disciplines.index')->with('success', 'Discipline updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating discipline and user: ' . $e->getMessage(), [
                'exception' => $e,
                'title' => $request->title,
                'description' => $request->description,
                'small_description' => $request->small_description,
                'module_id' => $request->module,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to Update discipline: ']);
        }
    }

    public function resources($id)
    {
        $discipline = Discipline::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);
        $resources = $discipline->resources;
        $resource_types = Resource::getResourceTypes();
        $types = Resource::getTypes();
        return view('disciplines.resources', compact('discipline', 'resources', 'resource_types', 'types'));
    }

    public function addResource(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the discipline by ID
            $discipline = Discipline::findOrFail($id);

            // Handle file upload
            $file = $request->file('document');
            $url = StorageS3::uploadToS3($file);

            if ($url) {
                $resourceData = [
                    'discipline_id' => $discipline->id,
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'type' => $request->input('type'),
                    'resource_type' => $request->input('resource_type'),
                    'url' => $url,
                ];

                $resource = Resource::create($resourceData);

                $resourceType = $request->input('resource_type');
                if ($resourceType === 'tarefa') {
                    Task::create([
                        'resource_id' => $resource->id, // Set the resource ID
                    ]);
                } elseif ($resourceType === 'prova') {
                    Test::create([
                        'resource_id' => $resource->id, // Set the resource ID
                    ]);
                }

                DB::commit();
                session()->flash('success', 'Resource added successfully.');
                Log::info('Resource uploaded successfully.');
                return redirect()->back()->with('success', 'Resources updated successfully!');
            } else {
                DB::rollBack();
                return back()->withErrors(['document' => 'File upload error: ' . $file->getError()]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to upload file: ' . $e->getMessage());
            Log::error('Error uploading file: ' . $e->getMessage());
            return redirect()->route('disciplines.index');
        }
    }



    public function enroll($id)
    {
        $discipline = Discipline::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('disciplines.enroll', compact('discipline'));
    }
}
