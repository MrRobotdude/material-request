<?php

namespace App\Http\Controllers\MaterialRequest;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectManagementController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('pages.project-management.index', compact('projects'));
    }

    public function create()
    {
        return view('pages.project-management.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($validated);

        return redirect()->route('project-management.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        return view('pages.project-management.form', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('project-management.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        // Periksa apakah project terkait dengan material requests
        if ($project->materialRequests()->exists()) {
            return redirect()->route('project-management.index')->withErrors([
                'error' => 'Project tidak dapat dihapus karena terkait dengan Material Requests.',
            ]);
        }

        $project->delete();

        return redirect()->route('project-management.index')->with('success', 'Project berhasil dihapus.');
    }
}
