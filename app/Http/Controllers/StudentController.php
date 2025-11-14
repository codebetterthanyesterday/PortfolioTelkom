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
            'education.*.id' => 'nullable', // Remove exists validation since IDs can be "new_xxx"
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
            // Get current education IDs that should be kept (only numeric IDs, not "new_xxx")
            $keepEducationIds = collect($validated['education'])
                ->filter(function ($edu) {
                    return isset($edu['id']) 
                        && !empty($edu['institution_name'])
                        && is_numeric($edu['id']); // Only keep numeric IDs
                })
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete education records that are not in the new data
            if (!empty($keepEducationIds)) {
                $student->educationInfo()->whereNotIn('id', $keepEducationIds)->delete();
            } else {
                // If no existing records to keep, delete all
                $student->educationInfo()->delete();
            }

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

                // Check if this is an existing record (numeric ID) or new record (starts with "new_")
                if (isset($eduData['id']) && is_numeric($eduData['id'])) {
                    // Update existing record
                    $student->educationInfo()->where('id', $eduData['id'])->update($educationData);
                } else {
                    // Create new record (ID is either missing or starts with "new_")
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

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Kategori berhasil ditambahkan'
        ]);
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'success' => true,
            'subject' => $subject,
            'message' => 'Mata kuliah berhasil ditambahkan'
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:teachers,nip',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'institution' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json([
            'success' => true,
            'teacher' => $teacher,
            'message' => 'Dosen/Guru berhasil ditambahkan'
        ]);
    }

    public function storeExpertise(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expertises,name',
        ]);

        $expertise = Expertise::create($validated);

        return response()->json([
            'success' => true,
            'expertise' => $expertise,
            'message' => 'Keahlian berhasil ditambahkan'
        ]);
    }
}

