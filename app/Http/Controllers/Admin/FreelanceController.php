<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Http\Controllers\Controller;
use App\Models\Freelance;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FreelanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('freelance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $freelances = Freelance::all();
        return view('admin.freelances.index', compact('freelances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('freelance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::all();
        return view('admin.freelances.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $freelance = Freelance::create($request->all());
        return redirect()->route('freelances.index')->with(['success'=>'Data telah berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Freelance $freelance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Freelance $freelance)
    {
        abort_if(Gate::denies('freelance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::all();
        return view('admin.freelances.edit', compact('freelance','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Freelance $freelance)
    {
        $freelance->update($request->all());
        return redirect()->route('freelances.index')->with(['success'=>'Data telah berhasil diubah.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Freelance $freelance)
    {
        $freelance->delete();
        return back();
    }
}
