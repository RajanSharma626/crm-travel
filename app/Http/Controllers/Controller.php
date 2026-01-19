<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get current user ID
     */
    protected function getCurrentUserId()
    {
        return Auth::id();
    }

    /**
     * Get department-specific stages
     */
    protected function getDepartmentStages($department)
    {
        $stages = [
            // Post Sales stages
            'Post Sales' => ['Pending', 'In Progress', 'Received', 'Briefing', 'Closed'],
            'Post Sales Manager' => ['Pending', 'In Progress', 'Received', 'Briefing', 'Closed'],
            // Operations stages
            'Operations' => ['Pending', 'In Progress', 'Vouchered', 'Monitoring', 'Closed'],
            'Operation' => ['Pending', 'In Progress', 'Vouchered', 'Monitoring', 'Closed'],
            'Operation Manager' => ['Pending', 'In Progress', 'Vouchered', 'Monitoring', 'Closed'],
            // Ticketing stages
            'Ticketing' => ['Pending', 'Booked', 'Issued'],
            // Visa stages
            'Visa' => ['Pending', 'In Progress', 'Granted', 'Refused'],
            // Insurance stages
            'Insurance' => ['Pending', 'In Progress', 'Booked', 'Closed'],
            // Delivery stages
            'Delivery' => ['Pending', 'In Progress', 'QC Passed', 'Delivered', 'Feedback', 'Closed'],
            'Delivery Manager' => ['Pending', 'In Progress', 'QC Passed', 'Delivered', 'Feedback', 'Closed'],
            // Sales stages (default to Post Sales stages if no specific stages defined)
            'Sales' => ['Pending', 'In Progress', 'Received', 'Briefing', 'Closed'],
            'Sales Manager' => ['Pending', 'In Progress', 'Received', 'Briefing', 'Closed'],
        ];

        // Map department/role to stage type
        $stageMap = [
            'Post Sales' => 'post_sales_stage',
            'Post Sales Manager' => 'post_sales_stage',
            'Operations' => 'operations_stage',
            'Operation' => 'operations_stage',
            'Operation Manager' => 'operations_stage',
            'Ticketing' => 'ticketing_stage',
            'Visa' => 'visa_stage',
            'Insurance' => 'insurance_stage',
            'Delivery' => 'delivery_stage',
            'Delivery Manager' => 'delivery_stage',
            'Sales' => 'sales_stage',
            'Sales Manager' => 'sales_stage',
        ];

        $stageKey = $stageMap[$department] ?? 'sales_stage';
        $stageOptions = $stages[$department] ?? ['Pending', 'In Progress', 'Received', 'Briefing', 'Closed'];

        return [
            'stage_key' => $stageKey,
            'stages' => $stageOptions,
            'department' => $department
        ];
    }

    /**
     * Get user's department for stage dropdown
     */
    protected function getUserDepartment()
    {
        $employee = Auth::user();
        // Get role - try role field first, then Spatie roles
        $role = $employee->role;
        if (!$role && $employee->roles && $employee->roles->isNotEmpty()) {
            $role = $employee->roles->first()->name;
        }
        $department = $employee->department;

        // Map role to department for stages
        if (in_array($role, ['Sales', 'Sales Manager'])) {
            return 'Sales';
        } elseif (in_array($role, ['Post Sales', 'Post Sales Manager'])) {
            return 'Post Sales';
        } elseif (in_array($role, ['Operation', 'Operation Manager'])) {
            return 'Operations';
        } elseif (in_array($role, ['Delivery', 'Delivery Manager'])) {
            return 'Delivery';
        } elseif ($department) {
            // Map department to standardized name
            $deptMap = [
                'Sales' => 'Sales',
                'Post Sales' => 'Post Sales',
                'Operations' => 'Operations',
                'Operation' => 'Operations',
                'Ticketing' => 'Ticketing',
                'Visa' => 'Visa',
                'Insurance' => 'Insurance',
                'Delivery' => 'Delivery',
            ];
            return $deptMap[$department] ?? $department;
        }

        return $role ?? 'Sales';
    }

    /**
     * Get stage for a lead based on assigned user's department
     */
    public static function getLeadStage($lead)
    {
        $assignedUser = $lead->assignedUser;
        
        if (!$assignedUser) {
            return [
                'stage' => 'Pending',
                'badge_class' => 'bg-secondary',
                'department' => 'Unknown'
            ];
        }

        // Get role - try role field first, then Spatie roles
        $userRole = $assignedUser->role;
        if (!$userRole && $assignedUser->roles && $assignedUser->roles->isNotEmpty()) {
            $userRole = $assignedUser->roles->first()->name;
        }
        $userDepartment = $assignedUser->department;

        // Map role to department and stage column
        $stageKey = null;
        $department = null;

        if (in_array($userRole, ['Sales', 'Sales Manager'])) {
            $stageKey = 'sales_stage';
            $department = 'Sales';
        } elseif (in_array($userRole, ['Post Sales', 'Post Sales Manager'])) {
            $stageKey = 'post_sales_stage';
            $department = 'Post Sales';
        } elseif (in_array($userRole, ['Operation', 'Operation Manager'])) {
            $stageKey = 'operations_stage';
            $department = 'Operations';
        } elseif (in_array($userRole, ['Delivery', 'Delivery Manager'])) {
            $stageKey = 'delivery_stage';
            $department = 'Delivery';
        } elseif ($userDepartment) {
            $deptMap = [
                'Sales' => ['sales_stage', 'Sales'],
                'Post Sales' => ['post_sales_stage', 'Post Sales'],
                'Operations' => ['operations_stage', 'Operations'],
                'Operation' => ['operations_stage', 'Operations'],
                'Ticketing' => ['ticketing_stage', 'Ticketing'],
                'Visa' => ['visa_stage', 'Visa'],
                'Insurance' => ['insurance_stage', 'Insurance'],
                'Delivery' => ['delivery_stage', 'Delivery'],
            ];
            if (isset($deptMap[$userDepartment])) {
                [$stageKey, $department] = $deptMap[$userDepartment];
            }
        }

        if (!$stageKey) {
            // Default to post_sales_stage if can't determine
            $stageKey = 'post_sales_stage';
            $department = 'Post Sales';
        }

        $currentStage = $lead->{$stageKey} ?? 'Pending';

        // Determine badge color based on stage
        $badgeClass = 'bg-secondary'; // default
        if ($currentStage === 'Pending') {
            $badgeClass = 'bg-warning text-dark';
        } elseif ($currentStage === 'In Progress') {
            $badgeClass = 'bg-info text-white';
        } elseif (in_array($currentStage, ['Closed', 'Delivered', 'Issued', 'Granted'])) {
            $badgeClass = 'bg-success text-white';
        } elseif (in_array($currentStage, ['Refused', 'Cancelled'])) {
            $badgeClass = 'bg-danger text-white';
        } elseif (in_array($currentStage, ['Vouchered', 'Monitoring', 'Received', 'Briefing', 'Booked', 'QC Passed', 'Feedback'])) {
            $badgeClass = 'bg-primary text-white';
        }

        return [
            'stage' => $currentStage,
            'badge_class' => $badgeClass,
            'department' => $department ?? 'Unknown'
        ];
    }
}
