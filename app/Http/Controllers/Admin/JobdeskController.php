<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Jobdesk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobdeskController extends Controller
{
    public function index()
    {
        $jobdesks = Jobdesk::with('role')->get();
        return view('admin.jobdesks.index', compact('jobdesks'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.jobdesks.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);

        Jobdesk::create($validated);

        return redirect()->route('jobdesks.index')
            ->with('success', 'Jobdesk created successfully.');
    }

    public function edit(Jobdesk $jobdesk)
    {
        $roles = Role::all();
        return view('admin.jobdesks.edit', compact('jobdesk', 'roles'));
    }

    public function update(Request $request, Jobdesk $jobdesk)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);

        $jobdesk->update($validated);

        return redirect()->route('jobdesks.index')
            ->with('success', 'Jobdesk updated successfully.');
    }

    public function destroy(Jobdesk $jobdesk)
    {
        $jobdesk->delete();

        return redirect()->route('jobdesks.index')
            ->with('success', 'Jobdesk deleted successfully.');
    }
}