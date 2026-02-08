<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRController extends Controller
{
    /**
     * Check if user has Admin or HR role
     */
    private function checkAccess()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'You must be logged in to access this resource.');
        }
        
        // Check Spatie roles
        $hasSpatieRole = $user->hasRole('Admin') || $user->hasRole('HR') || $user->hasRole('Developer');
        
        // Check department field
        $hasDepartment = in_array($user->department, ['Admin', 'HR']);
        
        // Also check role field for backward compatibility
        $roleField = $user->role ?? $user->getRoleNameAttribute();
        $hasRoleField = in_array($roleField, ['Admin', 'HR', 'Developer']);
        
        // Allow access if any check passes
        if ($hasSpatieRole || $hasDepartment || $hasRoleField) {
            return; // Allow access
        }
        
        abort(403, 'You do not have permission to access this resource. Your role: ' . ($roleField ?? 'User') . ', Department: ' . ($user->department ?? 'none'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $query = User::with('empBasicInfo');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(25);

        $departments = User::distinct()->pluck('department')->filter();

        return view('hr.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAccess();

        return view('hr.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255|unique:users,employee_id',
            'salutation' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'reporting_manager' => 'nullable|string|max:255',
            'branch_location' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'doj' => 'nullable|date',
            'employment_type' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'starting_salary' => 'nullable|numeric',
            'last_withdrawn_salary' => 'nullable|numeric',
            'email' => 'nullable|email|max:255|unique:users,email',
            'user_id' => 'nullable|string|max:255|unique:users,user_id',
            'password' => 'required|string|min:6|max:255',
            'role' => 'nullable|in:Admin,Manager,Developer,User',
            'status' => 'nullable|string|max:50',
            // Basic Information fields
            'previous_employer' => 'nullable|string|max:255',
            'previous_employer_contact_person' => 'nullable|string|max:255',
            'previous_employer_contact_number' => 'nullable|string|max:255',
            'reason_for_leaving' => 'nullable|string|max:255',
            'highest_qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'year_of_passing' => 'nullable|string|max:10',
            'work_experience' => 'nullable|string|max:255',
            'father_mother_name' => 'nullable|string|max:255',
            'father_mother_contact_number' => 'nullable|string|max:255',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_contact_number' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'aadhar_number' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'permanent_same_as_present' => 'nullable|boolean',
            // Exit & Clearance fields
            'exit_initiated_by' => 'nullable|string|max:255',
            'resignation_date' => 'nullable|date',
            'notice_period' => 'nullable|string|max:255',
            'last_working_day' => 'nullable|date',
            'exit_interview_notes' => 'nullable|string',
            'service_certificate_issued' => 'nullable|string|max:50',
            'service_certificate_issue_date' => 'nullable|date',
            'credit_card_handover' => 'nullable|string|max:50',
            'handed_over_laptop' => 'nullable|string|max:50',
            'handed_over_mobile' => 'nullable|string|max:50',
            'handed_over_id_card' => 'nullable|string|max:50',
            'all_dues_cleared' => 'nullable|string|max:50',
            // Incentive & performance
            'incentive_eligibility' => 'nullable|string|in:Yes,No',
            'incentive_type' => 'nullable|string|in:Fixed,Percentage',
            'monthly_target' => 'nullable|string|max:255',
            'incentive_payout_date' => 'nullable|date',
        ]);

        // Generate employee_id if not provided
        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = 'EMP' . str_pad(User::max('id') + 1 ?? 1, 6, '0', STR_PAD_LEFT);
        }

        // Hash password
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        // Create basic info profile
        $basicInfoData = [
            'previous_employer' => $validated['previous_employer'] ?? null,
            'contact_person' => $validated['previous_employer_contact_person'] ?? null,
            'contact_number' => $validated['previous_employer_contact_number'] ?? null,
            'reason_for_leaving' => $validated['reason_for_leaving'] ?? null,
            'highest_qualification' => $validated['highest_qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'year_of_passing' => $validated['year_of_passing'] ?? null,
            'work_experience' => $validated['work_experience'] ?? null,
            'father_mother_name' => $validated['father_mother_name'] ?? null,
            'father_mother_contact_number' => $validated['father_mother_contact_number'] ?? null,
            'nominee_name' => $validated['nominee_name'] ?? null,
            'nominee_contact_number' => $validated['nominee_contact_number'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'aadhar_number' => $validated['aadhar_number'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'passport_number' => $validated['passport_number'] ?? null,
            'present_address' => $validated['present_address'] ?? null,
            'permanent_address' => ($validated['permanent_same_as_present'] ?? false) ? ($validated['present_address'] ?? null) : ($validated['permanent_address'] ?? null),
        ];
        $user->empBasicInfo()->create($basicInfoData);

        // Create exit clearance data
        $exitClearanceData = [
            'exit_initiated_by' => $validated['exit_initiated_by'] ?? null,
            'resignation_date' => $validated['resignation_date'] ?? null,
            'notice_period' => $validated['notice_period'] ?? null,
            'last_working_day' => $validated['last_working_day'] ?? null,
            'exit_interview_notes' => $validated['exit_interview_notes'] ?? null,
            'service_certificate_issued' => ($validated['service_certificate_issued'] ?? null) === 'Yes',
            'issuing_date' => $validated['service_certificate_issue_date'] ?? null,
            'credit_card_handover' => $validated['credit_card_handover'] ?? null,
            'handed_over_laptop' => in_array($validated['handed_over_laptop'] ?? null, ['Given', 'Returned', '1']),
            'handed_over_mobile' => in_array($validated['handed_over_mobile'] ?? null, ['Given', 'Returned', '1']),
            'handed_over_id_card' => in_array($validated['handed_over_id_card'] ?? null, ['Given', 'Returned', '1']),
            'all_dues_cleared' => in_array($validated['all_dues_cleared'] ?? null, ['Given', 'Returned', '1']),
        ];
        $user->exitClearance()->create($exitClearanceData);

        // Create incentive performance record if any incentive fields provided
        $incentiveData = [
            'incentive_eligibility' => ($request->input('incentive_eligibility') === 'Yes'),
            'incentive_type' => $request->input('incentive_type') ?? null,
            'monthly_target' => $request->input('monthly_target') ?? null,
            'incentive_payout_date' => $request->input('incentive_payout_date') ?? null,
        ];
        // Only create if any related field is set
        if ($incentiveData['incentive_type'] || $incentiveData['monthly_target'] || $request->has('incentive_eligibility')) {
            $user->incentivePerformance()->create($incentiveData);
        }

        // Sync Spatie role
        if ($user->role) {
            try {
                $user->syncRoles([]);
                $user->assignRole($user->role);
            } catch (\Exception $e) {
                // Role might not exist, that's okay
            }
        }

        return redirect()->route('hr.employees.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employee)
    {
        $this->checkAccess();

        return view('hr.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        $this->checkAccess();
        $employee->load('exitClearance', 'incentivePerformance');

        return view('hr.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255|unique:users,employee_id,' . $employee->id,
            'salutation' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'reporting_manager' => 'nullable|string|max:255',
            'branch_location' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'doj' => 'nullable|date',
            'employment_type' => 'nullable|string|max:50',
            'employment_status' => 'nullable|string|max:50',
            'starting_salary' => 'nullable|numeric',
            'last_withdrawn_salary' => 'nullable|numeric',
            'email' => 'nullable|email|max:255|unique:users,email,' . $employee->id,
            'user_id' => 'nullable|string|max:255|unique:users,user_id,' . $employee->id,
            'password' => 'nullable|string|min:6|max:255',
            'role' => 'nullable|in:Admin,Manager,Developer,User',
            'status' => 'nullable|string|max:50',
            // Basic Information fields
            'previous_employer' => 'nullable|string|max:255',
            'previous_employer_contact_person' => 'nullable|string|max:255',
            'previous_employer_contact_number' => 'nullable|string|max:255',
            'reason_for_leaving' => 'nullable|string|max:255',
            'highest_qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'year_of_passing' => 'nullable|string|max:10',
            'work_experience' => 'nullable|string|max:255',
            'father_mother_name' => 'nullable|string|max:255',
            'father_mother_contact_number' => 'nullable|string|max:255',
            'nominee_name' => 'nullable|string|max:255',
            'nominee_contact_number' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'aadhar_number' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'permanent_same_as_present' => 'nullable|boolean',
            // Exit & Clearance fields
            'exit_initiated_by' => 'nullable|string|max:255',
            'resignation_date' => 'nullable|date',
            'notice_period' => 'nullable|string|max:255',
            'last_working_day' => 'nullable|date',
            'exit_interview_notes' => 'nullable|string',
            'service_certificate_issued' => 'nullable|string|max:50',
            'service_certificate_issue_date' => 'nullable|date',
            'credit_card_handover' => 'nullable|string|max:50',
            'handed_over_laptop' => 'nullable|string|max:50',
            'handed_over_mobile' => 'nullable|string|max:50',
            'handed_over_id_card' => 'nullable|string|max:50',
            'all_dues_cleared' => 'nullable|string|max:50',
            // Incentive & performance
            'incentive_eligibility' => 'nullable|string|in:Yes,No',
            'incentive_type' => 'nullable|string|in:Fixed,Percentage',
            'monthly_target' => 'nullable|string|max:255',
            'incentive_payout_date' => 'nullable|date',
        ]);

        // Hash password if provided, otherwise remove from array to keep existing password
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);
        $employee->refresh();

        // Update basic info profile
        $basicInfoData = [
            'previous_employer' => $validated['previous_employer'] ?? null,
            'contact_person' => $validated['previous_employer_contact_person'] ?? null,
            'contact_number' => $validated['previous_employer_contact_number'] ?? null,
            'reason_for_leaving' => $validated['reason_for_leaving'] ?? null,
            'highest_qualification' => $validated['highest_qualification'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'year_of_passing' => $validated['year_of_passing'] ?? null,
            'work_experience' => $validated['work_experience'] ?? null,
            'father_mother_name' => $validated['father_mother_name'] ?? null,
            'father_mother_contact_number' => $validated['father_mother_contact_number'] ?? null,
            'nominee_name' => $validated['nominee_name'] ?? null,
            'nominee_contact_number' => $validated['nominee_contact_number'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'aadhar_number' => $validated['aadhar_number'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'passport_number' => $validated['passport_number'] ?? null,
            'present_address' => $validated['present_address'] ?? null,
            'permanent_address' => ($validated['permanent_same_as_present'] ?? false) ? ($validated['present_address'] ?? null) : ($validated['permanent_address'] ?? null),
        ];
        $employee->empBasicInfo()->updateOrCreate(['user_id' => $employee->id], $basicInfoData);

        // Update exit clearance data
        $exitClearanceData = [
            'exit_initiated_by' => $validated['exit_initiated_by'] ?? null,
            'resignation_date' => $validated['resignation_date'] ?? null,
            'notice_period' => $validated['notice_period'] ?? null,
            'last_working_day' => $validated['last_working_day'] ?? null,
            'exit_interview_notes' => $validated['exit_interview_notes'] ?? null,
            'service_certificate_issued' => ($validated['service_certificate_issued'] ?? null) === 'Yes',
            'issuing_date' => $validated['service_certificate_issue_date'] ?? null,
            'credit_card_handover' => $validated['credit_card_handover'] ?? null,
            'handed_over_laptop' => in_array($validated['handed_over_laptop'] ?? null, ['Given', 'Returned', '1']),
            'handed_over_mobile' => in_array($validated['handed_over_mobile'] ?? null, ['Given', 'Returned', '1']),
            'handed_over_id_card' => in_array($validated['handed_over_id_card'] ?? null, ['Given', 'Returned', '1']),
            'all_dues_cleared' => in_array($validated['all_dues_cleared'] ?? null, ['Given', 'Returned', '1']),
        ];
        $employee->exitClearance()->updateOrCreate(['user_id' => $employee->id], $exitClearanceData);

        // Update incentive performance
        $incentiveData = [
            'incentive_eligibility' => ($request->input('incentive_eligibility') === 'Yes'),
            'incentive_type' => $request->input('incentive_type') ?? null,
            'monthly_target' => $request->input('monthly_target') ?? null,
            'incentive_payout_date' => $request->input('incentive_payout_date') ?? null,
        ];
        if ($incentiveData['incentive_type'] || $incentiveData['monthly_target'] || $request->has('incentive_eligibility')) {
            $employee->incentivePerformance()->updateOrCreate(['user_id' => $employee->id], $incentiveData);
        }

        // Sync Spatie role
        if ($employee->role) {
            try {
                $employee->syncRoles([]);
                $employee->assignRole($employee->role);
            } catch (\Exception $e) {
                // Role might not exist, that's okay
            }
        }

        return redirect()->route('hr.employees.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        $this->checkAccess();

        $employee->delete();

        return redirect()->route('hr.employees.index')
            ->with('success', 'User deleted successfully!');
    }
}
