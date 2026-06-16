<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\University;
use App\Models\Student;
use App\Models\Field;
use App\Models\Major;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Fields & Majors
        $field = Field::create(['name' => 'Information Technology']);
        $major = Major::create(['name' => 'Software Engineering', 'field_id' => $field->id]);

        // Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@careerbridge.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Company
        $companyUser = User::create([
            'name' => 'Tech Corp HR',
            'email' => 'hr@techcorp.com',
            'password' => Hash::make('password'),
            'role' => 'company',
            'is_active' => true,
        ]);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'Tech Corp',
            'description' => 'A leading tech company.',
        ]);

        // University
        $uniUser = User::create([
            'name' => 'Uni Admin',
            'email' => 'admin@uni.edu',
            'password' => Hash::make('password'),
            'role' => 'university',
            'is_active' => true,
        ]);
        $university = University::create([
            'user_id' => $uniUser->id,
            'university_name' => 'Tech University',
            'description' => 'Top tech university.',
        ]);

        // Student
        $studentUser = User::create([
            'name' => 'Nguyen Van A',
            'email' => 'student@uni.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
        Student::create([
            'user_id' => $studentUser->id,
            'university_id' => $university->id,
            'major_id' => $major->id,
            'student_code' => 'SE12345',
            'gpa' => 3.8,
        ]);

        // Mock Job
        Job::create([
            'company_id' => $company->id,
            'major_id' => $major->id,
            'title' => 'Software Engineer (Laravel/React)',
            'description' => 'We are looking for a backend engineer familiar with Laravel.',
            'requirements' => 'PHP, Laravel, MySQL, REST APIs.',
            'is_approved' => true,
        ]);
    }
}