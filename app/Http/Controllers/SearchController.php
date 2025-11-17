<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Student;
use App\Models\Project;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    /**
     * Global search endpoint
     */
    public function global(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $type = $request->input('type', 'all'); // all, students, projects, investors
        $limit = min($request->input('limit', 10), 50); // Max 50 results

        if (strlen($query) < 2) {
            return response()->json([
                'students' => [],
                'projects' => [],
                'investors' => [],
                'total' => 0
            ]);
        }

        $results = [];

        if ($type === 'all' || $type === 'students') {
            $results['students'] = $this->searchStudents($query, $limit);
        }

        if ($type === 'all' || $type === 'projects') {
            $results['projects'] = $this->searchProjects($query, $limit);
        }

        if ($type === 'all' || $type === 'investors') {
            $results['investors'] = $this->searchInvestors($query, $limit);
        }

        $results['total'] = array_sum([
            count($results['students'] ?? []),
            count($results['projects'] ?? []),
            count($results['investors'] ?? [])
        ]);

        return response()->json($results);
    }

    /**
     * Search students with optimized queries
     */
    public function students(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $limit = min($request->input('limit', 20), 100);

        if (strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'total' => 0
            ]);
        }

        $results = $this->searchStudents($query, $limit);

        return response()->json([
            'data' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Search projects with optimized queries
     */
    public function projects(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $limit = min($request->input('limit', 20), 100);

        if (strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'total' => 0
            ]);
        }

        $results = $this->searchProjects($query, $limit);

        return response()->json([
            'data' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Search investors with optimized queries
     */
    public function investors(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $limit = min($request->input('limit', 20), 100);

        if (strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'total' => 0
            ]);
        }

        $results = $this->searchInvestors($query, $limit);

        return response()->json([
            'data' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Optimized student search
     */
    private function searchStudents(string $query, int $limit): array
    {
        // Use a single query with joins for optimal performance
        $students = Student::select([
            'students.id',
            'students.student_id',
            'users.username',
            'users.full_name',
            'users.email',
            'users.avatar'
        ])
        ->join('users', 'students.user_id', '=', 'users.id')
        ->leftJoin('student_expertise', 'students.id', '=', 'student_expertise.student_id')
        ->leftJoin('expertises', 'student_expertise.expertise_id', '=', 'expertises.id')
        ->where(function (Builder $q) use ($query) {
            $q->where('users.username', 'LIKE', "%{$query}%")
              ->orWhere('users.full_name', 'LIKE', "%{$query}%")
              ->orWhere('users.email', 'LIKE', "%{$query}%")
              ->orWhere('students.student_id', 'LIKE', "%{$query}%")
              ->orWhere('expertises.name', 'LIKE', "%{$query}%");
        })
        ->whereNull('users.deleted_at') // Ensure user is not soft deleted
        ->distinct()
        ->limit($limit)
        ->get()
        ->map(function ($student) {
            return [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'username' => $student->username,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'avatar' => $student->avatar ? asset('storage/' . $student->avatar) : asset('images/default-avatar.png'),
                'url' => route('detail.student', ['student' => $student->username]),
                'type' => 'student'
            ];
        });

        return $students->toArray();
    }

    /**
     * Optimized project search
     */
    private function searchProjects(string $query, int $limit): array
    {
        $projects = Project::select([
            'projects.id',
            'projects.title',
            'projects.slug',
            'projects.description',
            'projects.status',
            'projects.type',
            'projects.price',
            'projects.view_count',
            'students.id as student_id',
            'users.username as owner_username',
            'users.full_name as owner_name'
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
        ->leftJoin('students as member_students', 'project_members.student_id', '=', 'member_students.id')
        ->leftJoin('users as member_users', 'member_students.user_id', '=', 'member_users.id')
        ->where('projects.status', 'published') // Only show published projects
        ->where(function (Builder $q) use ($query) {
            $q->where('projects.title', 'LIKE', "%{$query}%")
              ->orWhere('categories.name', 'LIKE', "%{$query}%")
              ->orWhere('subjects.name', 'LIKE', "%{$query}%")
              ->orWhere('teachers.name', 'LIKE', "%{$query}%")
              ->orWhere('users.username', 'LIKE', "%{$query}%")
              ->orWhere('users.full_name', 'LIKE', "%{$query}%")
              ->orWhere('member_users.username', 'LIKE', "%{$query}%")
              ->orWhere('member_users.full_name', 'LIKE', "%{$query}%");
        })
        ->whereNull('projects.deleted_at') // Ensure project is not soft deleted
        ->whereNull('users.deleted_at') // Ensure owner is not soft deleted
        ->distinct()
        ->limit($limit)
        ->get()
        ->map(function ($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => \Str::limit(strip_tags($project->description), 100),
                'status' => $project->status,
                'type' => $project->type,
                'price' => $project->price,
                'formatted_price' => 'Rp ' . number_format($project->price, 0, ',', '.'),
                'view_count' => $project->view_count,
                'owner' => [
                    'username' => $project->owner_username,
                    'full_name' => $project->owner_name
                ],
                'url' => route('projects.show', ['project' => $project->slug]),
                'type' => 'project'
            ];
        });

        return $projects->toArray();
    }

    /**
     * Optimized investor search
     */
    private function searchInvestors(string $query, int $limit): array
    {
        $investors = Investor::select([
            'investors.id',
            'investors.company_name',
            'investors.industry',
            'users.username',
            'users.full_name',
            'users.email',
            'users.avatar'
        ])
        ->join('users', 'investors.user_id', '=', 'users.id')
        ->where(function (Builder $q) use ($query) {
            $q->where('users.email', 'LIKE', "%{$query}%")
              ->orWhere('investors.company_name', 'LIKE', "%{$query}%")
              ->orWhere('investors.industry', 'LIKE', "%{$query}%")
              ->orWhere('users.username', 'LIKE', "%{$query}%")
              ->orWhere('users.full_name', 'LIKE', "%{$query}%");
        })
        ->whereNull('users.deleted_at') // Ensure user is not soft deleted
        ->distinct()
        ->limit($limit)
        ->get()
        ->map(function ($investor) {
            return [
                'id' => $investor->id,
                'username' => $investor->username,
                'full_name' => $investor->full_name,
                'email' => $investor->email,
                'company_name' => $investor->company_name,
                'industry' => $investor->industry,
                'avatar' => $investor->avatar ? asset('storage/' . $investor->avatar) : asset('images/default-avatar.png'),
                'type' => 'investor'
            ];
        });

        return $investors->toArray();
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        $type = $request->input('type', 'all');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Get recent popular searches (you can implement this based on your analytics)
        if ($type === 'all' || $type === 'projects') {
            // Most searched categories
            $categories = \DB::table('categories')
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(5)
                ->pluck('name')
                ->map(function ($name) {
                    return [
                        'text' => $name,
                        'type' => 'category'
                    ];
                });

            $suggestions = array_merge($suggestions, $categories->toArray());
        }

        if ($type === 'all' || $type === 'students') {
            // Popular expertises
            $expertises = \DB::table('expertises')
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(5)
                ->pluck('name')
                ->map(function ($name) {
                    return [
                        'text' => $name,
                        'type' => 'expertise'
                    ];
                });

            $suggestions = array_merge($suggestions, $expertises->toArray());
        }

        return response()->json(array_slice($suggestions, 0, 10));
    }
}