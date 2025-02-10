<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\StorageS3;
use Exception;

class ResourceController extends Controller
{
    // Display a list of resourcess

    // Show form to edit a resources
    public function edit($id)
    {
        $resource = Resource::findOrFail($id);
        $resource_types = Resource::getResourceTypes();
        $types = Resource::getTypes();
        return view('resources.edit', compact('resource', 'resource_types', 'types'));
    }
    public function docs($id)
    {
        $resources = Resource::where('discipline_id', $id)
            ->where('resource_type', 'documento')
            ->get();

        return view('resources.docs', compact('resources'));
    }

    public function tasks($id)
    {
        $resources = Resource::where('discipline_id', $id)
            ->where('resource_type', 'tarefa')
            ->get();

        return view('resources.tasks', compact('resources'));
    }

    public function tests($id)
    {
        $resources = Resource::where('discipline_id', $id)
            ->where('resource_type', 'prova')
            ->get();

        return view('resources.tests', compact('resources'));
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $resources = Resource::findOrFail($id);
            $resources->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Resources deleted successfully!');
        } catch (Exception $e) {
            Log::error('Error deleting resources and user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the resources and user. Please try again.');
        }
    }



    // Update resources details in the database
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $resources = Resource::findOrFail($id);
            $file = $request->file('document');
            $url = $file ? StorageS3::uploadToS3($file) : null;

            $updateData = [
                'name' => $request->input('name'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'type' => $request->input('type'),
                'resource_type' => $request->input('resource_type'),
            ];

            if ($url) {
                $updateData['url'] = $url;
            }
            $resources->update($updateData);
            DB::commit();
            return redirect()->back()->with('success', 'resources updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating resources and user: ' . $e->getMessage(), [
                'exception' => $e,
                'resources_id' => $id,
                'resources_name' => $request->name,
                'resources_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to Update resources: ']);
        }
    }
}
