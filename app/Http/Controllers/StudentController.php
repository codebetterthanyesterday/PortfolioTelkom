<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Expertise;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'expertises'])
            ->paginate(12);

        return view('students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load([
            'user',
            'expertises',
            'educationInfo',
            'projects' => function ($query) {
                $query->published()->with(['media', 'categories'])->latest();
            }
        ]);

        return view('students.show', compact('student'));
    }

    public function showProfile()
    {
        $student = auth()->user()->student;
        $student->load(['user', 'expertises', 'educationInfo']);
        $expertises = Expertise::all();
        
        // Data for project creation modals
        $categories = Category::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $students = Student::with('user')->where('id', '!=', $student->id)->get();

        return view('pages.student.profile', compact('student', 'expertises', 'categories', 'subjects', 'teachers', 'students'));
    }

    public function edit(Student $student)
    {
        $this->authorize('update', $student);
        
        $student->load(['user', 'expertises', 'educationInfo']);
        $expertises = Expertise::all();

        return view('students.edit', compact('student', 'expertises'));
    }

    public function update(Request $request)
    {
        $student = auth()->user()->student;

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'avatar' => 'nullable|image|max:2048',
            'short_about' => 'nullable|string|max:500',
            'about' => 'nullable|string',
            'expertises' => 'nullable|array',
            'expertises.*' => 'exists:expertises,id',
            'education' => 'nullable|array',
            'education.*.institution_name' => 'nullable|string|max:255',
            'education.*.degree' => 'nullable|string|max:50',
            'education.*.field_of_study' => 'nullable|string|max:255',
            'education.*.start_date' => 'nullable|date',
            'education.*.end_date' => 'nullable|date',
            'education.*.is_current' => 'nullable|boolean',
            'education.*.description' => 'nullable|string',
            'education.*.id' => 'nullable|exists:education_info,id',
        ]);

        // Update user data
        $userData = [
            'full_name' => $validated['full_name'],
            'phone_number' => $validated['phone_number'],
            'short_about' => $validated['short_about'] ?? null,
            'about' => $validated['about'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($student->user->avatar) {
                Storage::disk('public')->delete($student->user->avatar);
            }

            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $student->user->update($userData);

        // Update student data
        $student->update([
            'student_id' => $validated['student_id'],
        ]);

        // Sync expertises
        if (isset($validated['expertises'])) {
            $student->expertises()->sync($validated['expertises']);
        } else {
            $student->expertises()->sync([]);
        }

        // Handle education data
        if (isset($validated['education'])) {
            // Get current education IDs that should be kept
            $keepEducationIds = collect($validated['education'])
                ->filter(function ($edu) {
                    return isset($edu['id']) && !empty($edu['institution_name']);
                })
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete education records that are not in the new data
            $student->educationInfo()->whereNotIn('id', $keepEducationIds)->delete();

            // Process each education entry
            foreach ($validated['education'] as $eduData) {
                // Skip empty entries
                if (empty($eduData['institution_name'])) {
                    continue;
                }

                $educationData = [
                    'student_id' => $student->id,
                    'institution_name' => $eduData['institution_name'],
                    'degree' => $eduData['degree'] ?? null,
                    'field_of_study' => $eduData['field_of_study'] ?? null,
                    'start_date' => $eduData['start_date'] ?? null,
                    'end_date' => ($eduData['is_current'] ?? false) ? null : ($eduData['end_date'] ?? null),
                    'is_current' => $eduData['is_current'] ?? false,
                    'description' => $eduData['description'] ?? null,
                ];

                if (isset($eduData['id']) && $eduData['id']) {
                    // Update existing record
                    $student->educationInfo()->where('id', $eduData['id'])->update($educationData);
                } else {
                    // Create new record
                    $student->educationInfo()->create($educationData);
                }
            }
        } else {
            // If no education data sent, don't delete existing records
        }

        return redirect()->route('student.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function dashboard()
    {
        $student = auth()->user()->student;
        $student->load([
            'projects' => function ($query) {
                $query->latest()->with(['media', 'categories']);
            }
        ]);

        $stats = [
            'total_projects' => $student->projects()->count(),
            'published_projects' => $student->projects()->published()->count(),
            'draft_projects' => $student->projects()->draft()->count(),
            'total_views' => $student->projects()->sum('view_count'),
        ];

        return view('students.dashboard', compact('student', 'stats'));
    }
}

