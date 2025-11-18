<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Project;
use App\Models\Investor;
use App\Models\User;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Expertise;

class SearchController extends Controller
{
    public function liveSearch(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);
        
        // Return empty results if query is too short
        if (strlen(trim($query)) < 1) {
            return response()->json([
                'success' => true,
                'results' => [
                    'students' => [],
                    'projects' => [],
                    'investors' => []
                ],
                'counts' => [
                    'students' => 0,
                    'projects' => 0,
                    'investors' => 0,
                    'total' => 0
                ]
            ]);
        }

        $searchTerm = '%' . $query . '%';
        
        try {
            // Search Students with optimized query
            $students = $this->searchStudents($searchTerm, $limit);
            
            // Search Projects with optimized query  
            $projects = $this->searchProjects($searchTerm, $limit);
            
            // Search Investors with optimized query
            $investors = $this->searchInvestors($searchTerm, $limit);

            $counts = [
                'students' => $students->count(),
                'projects' => $projects->count(),
                'investors' => $investors->count(),
                'total' => $students->count() + $projects->count() + $investors->count()
            ];

            return response()->json([
                'success' => true,
                'results' => [
                    'students' => $students,
                    'projects' => $projects,
                    'investors' => $investors
                ],
                'counts' => $counts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Search failed. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Search students by username, full name, email, student ID, and expertise
     */
    private function searchStudents($searchTerm, $limit)
    {
        return Student::select([
                'students.id',
                'students.student_id',
                'students.created_at',
                'users.username',
                'users.full_name',
                'users.email',
                'users.avatar'
            ])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('student_expertise', 'students.id', '=', 'student_expertise.student_id')
            ->leftJoin('expertises', 'student_expertise.expertise_id', '=', 'expertises.id')
            ->where(function ($query) use ($searchTerm) {
                $query->where('users.username', 'like', $searchTerm)
                      ->orWhere('users.full_name', 'like', $searchTerm)
                      ->orWhere('users.email', 'like', $searchTerm)
                      ->orWhere('students.student_id', 'like', $searchTerm)
                      ->orWhere('expertises.name', 'like', $searchTerm);
            })
            ->groupBy([
                'students.id',
                'students.student_id', 
                'students.created_at',
                'users.username',
                'users.full_name',
                'users.email',
                'users.avatar'
            ])
            ->orderBy('users.username')
            ->limit($limit)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'username' => $student->username,
                    'full_name' => $student->full_name,
                    'email' => $student->email,
                    'avatar' => $student->avatar ? asset('storage/' . $student->avatar) : null,
                    'profile_url' => route('detail.student', ['student' => $student->username]),
                    'created_at' => $student->created_at
                ];
            });
    }

    /**
     * Search projects by title, category, subjects, teachers, and student (owner/contributor)
     */
    private function searchProjects($searchTerm, $limit)
    {
        return Project::select([
                'projects.id',
                'projects.title',
                'projects.slug',
                'projects.price',
                'projects.type',
                'projects.view_count',
                'projects.created_at',
                'students.student_id',
                'users.username',
                'users.full_name'
            ])
            ->join('students', 'projects.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('project_category', 'projects.id', '=', 'project_category.project_id')
            ->leftJoin('categories', 'project_category.category_id', '=', 'categories.id')
            ->leftJoin('project_subject', 'projects.id', '=', 'project_subject.project_id')
            ->leftJoin('subjects', 'project_subject.subject_id', '=', 'subjects.id')
            ->leftJoin('project_teacher', 'projects.id', '=', 'project_teacher.project_id')
            ->leftJoin('teachers', 'project_teacher.teacher_id', '=', 'teachers.id')
            ->leftJoin('project_members', 'projects.id', '=', 'project_members.project_id')
            ->leftJoin('students as contributor_students', 'project_members.student_id', '=', 'contributor_students.id')
            ->leftJoin('users as contributor_users', 'contributor_students.user_id', '=', 'contributor_users.id')
            ->where('projects.status', 'published')
            ->where(function ($query) use ($searchTerm) {
                $query->where('projects.title', 'like', $searchTerm)
                      ->orWhere('categories.name', 'like', $searchTerm)
                      ->orWhere('subjects.name', 'like', $searchTerm)
                      ->orWhere('subjects.code', 'like', $searchTerm)
                      ->orWhere('teachers.name', 'like', $searchTerm)
                      ->orWhere('users.full_name', 'like', $searchTerm)
                      ->orWhere('users.username', 'like', $searchTerm)
                      ->orWhere('contributor_users.full_name', 'like', $searchTerm)
                      ->orWhere('contributor_users.username', 'like', $searchTerm);
            })
            ->groupBy([
                'projects.id',
                'projects.title',
                'projects.slug',
                'projects.price',
                'projects.type',
                'projects.view_count',
                'projects.created_at',
                'students.student_id',
                'users.username',
                'users.full_name'
            ])
            ->orderBy('projects.view_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($project) {
                // Get thumbnail
                $thumbnail = DB::table('project_media')
                    ->where('project_id', $project->id)
                    ->orderBy('order')
                    ->first();

                // Get categories for this project
                $categories = DB::table('project_category')
                    ->join('categories', 'project_category.category_id', '=', 'categories.id')
                    ->where('project_category.project_id', $project->id)
                    ->pluck('categories.name')
                    ->take(3);

                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'price' => $project->price,
                    'formatted_price' => 'Rp ' . number_format($project->price, 0, ',', '.'),
                    'type' => $project->type,
                    'view_count' => $project->view_count,
                    'owner' => [
                        'student_id' => $project->student_id,
                        'username' => $project->username,
                        'full_name' => $project->full_name
                    ],
                    'categories' => $categories,
                    'thumbnail' => $thumbnail ? asset('storage/' . $thumbnail->file_path) : null,
                    'url' => route('projects.show', ['project' => $project->slug]),
                    'created_at' => $project->created_at
                ];
            });
    }

    /**
     * Search investors by email, company, industry, username, and full name
     */
    private function searchInvestors($searchTerm, $limit)
    {
        return Investor::select([
                'investors.id',
                'investors.company_name',
                'investors.industry',
                'investors.created_at',
                'users.username',
                'users.full_name',
                'users.email',
                'users.avatar'
            ])
            ->join('users', 'investors.user_id', '=', 'users.id')
            ->where(function ($query) use ($searchTerm) {
                $query->where('users.email', 'like', $searchTerm)
                      ->orWhere('investors.company_name', 'like', $searchTerm)
                      ->orWhere('investors.industry', 'like', $searchTerm)
                      ->orWhere('users.username', 'like', $searchTerm)
                      ->orWhere('users.full_name', 'like', $searchTerm);
            })
            ->orderBy('investors.company_name')
            ->limit($limit)
            ->get()
            ->map(function ($investor) {
                // Get wishlist count for this investor
                $wishlistCount = DB::table('wishlists')
                    ->where('investor_id', $investor->id)
                    ->count();

                return [
                    'id' => $investor->id,
                    'username' => $investor->username,
                    'full_name' => $investor->full_name,
                    'email' => $investor->email,
                    'company_name' => $investor->company_name,
                    'industry' => $investor->industry,
                    'avatar' => $investor->avatar ? asset('storage/' . $investor->avatar) : null,
                    'wishlist_count' => $wishlistCount,
                    'created_at' => $investor->created_at
                ];
            });
    }

    /**
     * Advanced search with filters (optional future implementation)
     */
    public function advancedSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:students,projects,investors',
            'category_id' => 'nullable|exists:categories,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'expertise_id' => 'nullable|exists:expertises,id',
            'project_type' => 'nullable|in:individual,team',
            'industry' => 'nullable|string',
            'sort' => 'nullable|in:relevance,name,date,views',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        $query = $request->input('query');
        $type = $request->input('type', 'all');
        $perPage = $request->input('per_page', 20);
        
        $results = [];
        
        if ($type === 'all' || $type === 'students') {
            $results['students'] = $this->advancedSearchStudents($request);
        }
        
        if ($type === 'all' || $type === 'projects') {
            $results['projects'] = $this->advancedSearchProjects($request);
        }
        
        if ($type === 'all' || $type === 'investors') {
            $results['investors'] = $this->advancedSearchInvestors($request);
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'query' => $query,
            'filters' => $request->only(['type', 'category_id', 'subject_id', 'teacher_id', 'expertise_id', 'project_type', 'industry'])
        ]);
    }

    private function advancedSearchStudents(Request $request)
    {
        $query = Student::with(['user', 'expertises'])
            ->whereHas('user', function ($q) use ($request) {
                $searchTerm = '%' . $request->input('query') . '%';
                $q->where('full_name', 'like', $searchTerm)
                  ->orWhere('username', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });

        if ($request->filled('expertise_id')) {
            $query->whereHas('expertises', function ($q) use ($request) {
                $q->where('expertise_id', $request->input('expertise_id'));
            });
        }

        return $query->paginate($request->input('per_page', 20));
    }

    private function advancedSearchProjects(Request $request)
    {
        $query = Project::with(['student.user', 'categories', 'subjects', 'teachers', 'media'])
            ->published();

        $searchTerm = '%' . $request->input('query') . '%';
        $query->where('title', 'like', $searchTerm);

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            });
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->where('subject_id', $request->input('subject_id'));
            });
        }

        if ($request->filled('teacher_id')) {
            $query->whereHas('teachers', function ($q) use ($request) {
                $q->where('teacher_id', $request->input('teacher_id'));
            });
        }

        if ($request->filled('project_type')) {
            $query->where('type', $request->input('project_type'));
        }

        $sort = $request->input('sort', 'relevance');
        switch ($sort) {
            case 'name':
                $query->orderBy('title');
                break;
            case 'date':
                $query->latest();
                break;
            case 'views':
                $query->orderBy('view_count', 'desc');
                break;
            default:
                $query->orderBy('view_count', 'desc')->latest();
        }

        return $query->paginate($request->input('per_page', 20));
    }

    private function advancedSearchInvestors(Request $request)
    {
        $query = Investor::with(['user'])
            ->whereHas('user', function ($q) use ($request) {
                $searchTerm = '%' . $request->input('query') . '%';
                $q->where('full_name', 'like', $searchTerm)
                  ->orWhere('username', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });

        $searchTerm = '%' . $request->input('query') . '%';
        $query->orWhere('company_name', 'like', $searchTerm)
              ->orWhere('industry', 'like', $searchTerm);

        if ($request->filled('industry')) {
            $query->where('industry', 'like', '%' . $request->input('industry') . '%');
        }

        return $query->paginate($request->input('per_page', 20));
    }
}
