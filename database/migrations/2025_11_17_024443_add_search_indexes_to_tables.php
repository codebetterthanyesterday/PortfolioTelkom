<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for users table (for searching students and investors)
        Schema::table('users', function (Blueprint $table) {
            // Check if indexes don't already exist before creating them
            if (!$this->indexExists('users', 'users_username_index')) {
                $table->index(['username'], 'users_username_index');
            }
            if (!$this->indexExists('users', 'users_full_name_index')) {
                $table->index(['full_name'], 'users_full_name_index');
            }
            if (!$this->indexExists('users', 'users_role_index')) {
                $table->index(['role'], 'users_role_index');
            }
            if (!$this->indexExists('users', 'users_search_composite_index')) {
                $table->index(['username', 'full_name', 'email'], 'users_search_composite_index');
            }
        });

        // Add indexes for students table
        Schema::table('students', function (Blueprint $table) {
            if (!$this->indexExists('students', 'students_student_id_index')) {
                $table->index(['student_id'], 'students_student_id_index');
            }
        });

        // Add indexes for investors table
        Schema::table('investors', function (Blueprint $table) {
            if (!$this->indexExists('investors', 'investors_company_name_index')) {
                $table->index(['company_name'], 'investors_company_name_index');
            }
            if (!$this->indexExists('investors', 'investors_industry_index')) {
                $table->index(['industry'], 'investors_industry_index');
            }
        });

        // Add indexes for projects table
        Schema::table('projects', function (Blueprint $table) {
            if (!$this->indexExists('projects', 'projects_title_index')) {
                $table->index(['title'], 'projects_title_index');
            }
            if (!$this->indexExists('projects', 'projects_status_index')) {
                $table->index(['status'], 'projects_status_index');
            }
            if (!$this->indexExists('projects', 'projects_type_index')) {
                $table->index(['type'], 'projects_type_index');
            }
            if (!$this->indexExists('projects', 'projects_view_count_index')) {
                $table->index(['view_count'], 'projects_view_count_index');
            }
            if (!$this->indexExists('projects', 'projects_status_type_index')) {
                $table->index(['status', 'type'], 'projects_status_type_index');
            }
        });

        // Add indexes for categories table
        Schema::table('categories', function (Blueprint $table) {
            if (!$this->indexExists('categories', 'categories_name_index')) {
                $table->index(['name'], 'categories_name_index');
            }
        });

        // Add indexes for subjects table
        Schema::table('subjects', function (Blueprint $table) {
            if (!$this->indexExists('subjects', 'subjects_name_index')) {
                $table->index(['name'], 'subjects_name_index');
            }
            if (!$this->indexExists('subjects', 'subjects_code_index')) {
                $table->index(['code'], 'subjects_code_index');
            }
        });

        // Add indexes for teachers table
        Schema::table('teachers', function (Blueprint $table) {
            if (!$this->indexExists('teachers', 'teachers_name_index')) {
                $table->index(['name'], 'teachers_name_index');
            }
            if (!$this->indexExists('teachers', 'teachers_email_index')) {
                $table->index(['email'], 'teachers_email_index');
            }
            if (!$this->indexExists('teachers', 'teachers_institution_index')) {
                $table->index(['institution'], 'teachers_institution_index');
            }
        });

        // Add indexes for expertises table
        Schema::table('expertises', function (Blueprint $table) {
            if (!$this->indexExists('expertises', 'expertises_name_index')) {
                $table->index(['name'], 'expertises_name_index');
            }
        });

        // Add indexes for junction tables to optimize joins - these likely don't exist yet
        Schema::table('project_category', function (Blueprint $table) {
            if (!$this->indexExists('project_category', 'project_category_project_id_index')) {
                $table->index(['project_id'], 'project_category_project_id_index');
            }
            if (!$this->indexExists('project_category', 'project_category_category_id_index')) {
                $table->index(['category_id'], 'project_category_category_id_index');
            }
        });

        Schema::table('project_subject', function (Blueprint $table) {
            if (!$this->indexExists('project_subject', 'project_subject_project_id_index')) {
                $table->index(['project_id'], 'project_subject_project_id_index');
            }
            if (!$this->indexExists('project_subject', 'project_subject_subject_id_index')) {
                $table->index(['subject_id'], 'project_subject_subject_id_index');
            }
        });

        Schema::table('project_teacher', function (Blueprint $table) {
            if (!$this->indexExists('project_teacher', 'project_teacher_project_id_index')) {
                $table->index(['project_id'], 'project_teacher_project_id_index');
            }
            if (!$this->indexExists('project_teacher', 'project_teacher_teacher_id_index')) {
                $table->index(['teacher_id'], 'project_teacher_teacher_id_index');
            }
        });

        Schema::table('student_expertise', function (Blueprint $table) {
            if (!$this->indexExists('student_expertise', 'student_expertise_student_id_index')) {
                $table->index(['student_id'], 'student_expertise_student_id_index');
            }
            if (!$this->indexExists('student_expertise', 'student_expertise_expertise_id_index')) {
                $table->index(['expertise_id'], 'student_expertise_expertise_id_index');
            }
        });

        Schema::table('project_members', function (Blueprint $table) {
            if (!$this->indexExists('project_members', 'project_members_project_id_index')) {
                $table->index(['project_id'], 'project_members_project_id_index');
            }
            if (!$this->indexExists('project_members', 'project_members_student_id_index')) {
                $table->index(['student_id'], 'project_members_student_id_index');
            }
            if (!$this->indexExists('project_members', 'project_members_role_index')) {
                $table->index(['role'], 'project_members_role_index');
            }
        });

        Schema::table('wishlists', function (Blueprint $table) {
            if (!$this->indexExists('wishlists', 'wishlists_investor_id_index')) {
                $table->index(['investor_id'], 'wishlists_investor_id_index');
            }
            if (!$this->indexExists('wishlists', 'wishlists_project_id_index')) {
                $table->index(['project_id'], 'wishlists_project_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes that we created
        $indexesToDrop = [
            'users' => [
                'users_username_index',
                'users_full_name_index', 
                'users_role_index',
                'users_search_composite_index'
            ],
            'students' => [
                'students_student_id_index'
            ],
            'investors' => [
                'investors_company_name_index',
                'investors_industry_index'
            ],
            'projects' => [
                'projects_title_index',
                'projects_status_index',
                'projects_type_index',
                'projects_view_count_index',
                'projects_status_type_index'
            ],
            'categories' => [
                'categories_name_index'
            ],
            'subjects' => [
                'subjects_name_index',
                'subjects_code_index'
            ],
            'teachers' => [
                'teachers_name_index',
                'teachers_email_index',
                'teachers_institution_index'
            ],
            'expertises' => [
                'expertises_name_index'
            ],
            'project_category' => [
                'project_category_project_id_index',
                'project_category_category_id_index'
            ],
            'project_subject' => [
                'project_subject_project_id_index',
                'project_subject_subject_id_index'
            ],
            'project_teacher' => [
                'project_teacher_project_id_index',
                'project_teacher_teacher_id_index'
            ],
            'student_expertise' => [
                'student_expertise_student_id_index',
                'student_expertise_expertise_id_index'
            ],
            'project_members' => [
                'project_members_project_id_index',
                'project_members_student_id_index',
                'project_members_role_index'
            ],
            'wishlists' => [
                'wishlists_investor_id_index',
                'wishlists_project_id_index'
            ]
        ];

        foreach ($indexesToDrop as $tableName => $indexes) {
            foreach ($indexes as $indexName) {
                if ($this->indexExists($tableName, $indexName)) {
                    DB::statement("DROP INDEX IF EXISTS {$indexName}");
                }
            }
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $indexName)
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'pgsql') {
            $query = "SELECT 1 FROM pg_indexes WHERE indexname = ?";
            return $connection->select($query, [$indexName]) ? true : false;
        } else {
            $query = "SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?";
            return $connection->select($query, [$databaseName, $table, $indexName]) ? true : false;
        }
    }
};
