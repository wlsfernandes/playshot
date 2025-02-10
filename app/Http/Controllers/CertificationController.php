<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CertificationController extends Controller
{
    // Display a list of certifications
    public function index()
    {
        $certifications = Certification::where('institution_id', Auth::user()->institution_id)
            ->orderBy('name')
            ->get();
        return view('certifications.index', compact('certifications'));
    }
    public function listCertifications()
    {
        $certifications = Certification::where('institution_id', Auth::user()->institution_id)->get();
        return view('certifications.list-certifications', compact('certifications'));
    }

    public function myCertifications()
    {
        $certifications = Certification::where('institution_id', Auth::user()->institution_id)
            ->whereHas('students') // Ensures there are associated students in the discipline_student table
            ->get();

        return view('certifications.mycertifications', compact('certifications'));
    }

    // Show form to create a new certification
    public function create()
    {
        return view('certifications.create');
    }
    // Show form to edit a certification
    public function edit($id)
    {
        $certification = Certification::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('certifications.edit', compact('certification'));
    }

    // Show details of a specific certification
    public function show($id)
    {
        $certification = Certification::where('institution_id', Auth::user()->institution_id)
            ->findOrFail($id);

        return view('certifications.show', compact('certification'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $certification = Certification::where('institution_id', Auth::user()->institution_id)
                ->findOrFail($id);

            $certification->delete();
            DB::commit();
            return redirect()->route('certifications.index')->with('success', 'Certification and User deleted successfully!');

        } catch (Exception $e) {
            Log::error('Error deleting certification and user: ' . $e->getMessage());
            return redirect()->route('certifications.index')->with('error', 'An error occurred while deleting the certification and user. Please try again.');
        }
    }

    // Store a new certification in the database
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            Certification::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'institution_id' => Auth::user()->institution_id, // Automatically set the institution ID
            ]);
            DB::commit();
            return redirect()->route('certifications.index')->with('success', 'Certification created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating certification and user: ' . $e->getMessage(), [
                'exception' => $e,
                'certification_name' => $request->name,
                'certification_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while creating the certification. Please try again.']);
        }
    }

    // Update certification details in the database
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $certification = Certification::findOrFail($id);

            $certification->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'institution_id' => Auth::user()->institution_id,
            ]);
            DB::commit();
            return redirect()->route('certifications.index')->with('success', 'Certification updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating certification and user: ' . $e->getMessage(), [
                'exception' => $e,
                'certification_id' => $id,
                'certification_name' => $request->name,
                'certification_email' => $request->email,
                'institution_id' => Auth::user()->institution_id,
            ]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to Update certification: ']);
        }
    }
}
