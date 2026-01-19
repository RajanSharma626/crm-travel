<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing tables
        Schema::dropIfExists('users');
        Schema::dropIfExists('employees'); // Ensure employees table is dropped
        Schema::dropIfExists('emp_basic_infos');
        Schema::dropIfExists('incentive_performances');
        Schema::dropIfExists('statutory_payroll_details');
        Schema::dropIfExists('exit_clearances');

        // 1. User Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique()->nullable(); // EMP0001
            $table->string('salutation')->nullable();
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('reporting_manager')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('branch_location')->nullable();
            $table->date('doj')->nullable();
            $table->date('date_of_leaving')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('employment_status')->nullable();
            $table->decimal('starting_salary', 15, 2)->nullable();
            $table->decimal('last_withdrawn_salary', 15, 2)->nullable();
            
            // Login details
            $table->string('user_id')->unique(); // Custom login ID (e.g. Dev)
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->nullable(); // For Spatie compatibility/fallback
            
            $table->string('status')->default('Active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Emp Basic Info
        Schema::create('emp_basic_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('previous_employer')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('reason_for_leaving')->nullable();
            $table->string('highest_qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->string('year_of_passing')->nullable();
            $table->string('work_experience')->nullable();
            $table->string('father_mother_name')->nullable();
            $table->string('father_mother_contact_number')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_contact_number')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->timestamps();
        });

        // 3. Incentive & Performance
        Schema::create('incentive_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('incentive_eligibility')->default(false);
            $table->string('incentive_type')->nullable(); // fixed/%
            $table->string('monthly_target')->nullable();
            $table->date('incentive_payout_date')->nullable();
            $table->timestamps();
        });

        // 4. Statutory & Payroll Details
        Schema::create('statutory_payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('salary_structure')->nullable(); // CTC / Gross / Net
            $table->string('pf_number')->nullable();
            $table->string('esic_number')->nullable();
            $table->string('uan_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->timestamps();
        });

        // 5. Exit & Clearance
        Schema::create('exit_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('exit_initiated_by')->nullable(); // Employee / HR
            $table->string('employee_name')->nullable();
            $table->date('resignation_date')->nullable();
            $table->string('notice_period')->nullable();
            $table->date('last_working_day')->nullable();
            $table->text('exit_interview_notes')->nullable();
            $table->boolean('service_certificate_issued')->default(false);
            $table->date('issuing_date')->nullable();
            $table->boolean('handed_over_laptop')->default(false);
            $table->boolean('handed_over_mobile')->default(false);
            $table->boolean('handed_over_id_card')->default(false);
            $table->boolean('all_dues_cleared')->default(false);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exit_clearances');
        Schema::dropIfExists('statutory_payroll_details');
        Schema::dropIfExists('incentive_performances');
        Schema::dropIfExists('emp_basic_infos');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
