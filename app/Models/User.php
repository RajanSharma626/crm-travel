<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'salutation',
        'name',
        'dob',
        'marital_status',
        'department',
        'designation',
        'reporting_manager',
        'blood_group',
        'branch_location',
        'doj',
        'date_of_leaving',
        'employment_type',
        'employment_status',
        'starting_salary',
        'last_withdrawn_salary',
        'user_id',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'doj' => 'date',
            'date_of_leaving' => 'date',
            'starting_salary' => 'decimal:2',
            'last_withdrawn_salary' => 'decimal:2',
        ];
    }

    public function empBasicInfo()
    {
        return $this->hasOne(EmpBasicInfo::class);
    }

    public function incentivePerformance()
    {
        return $this->hasOne(IncentivePerformance::class);
    }

    public function statutoryPayrollDetails()
    {
        return $this->hasOne(StatutoryPayrollDetails::class);
    }

    public function exitClearance()
    {
        return $this->hasOne(ExitClearance::class);
    }

    /**
     * Get role name (backward compatibility)
     */
    public function getRoleNameAttribute()
    {
        return $this->roles()->first()?->name ?? $this->attributes['role'] ?? null;
    }
    
    /**
     * Check if user has a specific role (backward compatibility)
     * This method checks both Spatie roles and the role field
     */
    public function hasRoleName($role): bool
    {
        // Check Spatie roles first
        if ($this->roles()->where('name', $role)->exists()) {
            return true;
        }
        
        // Also check the role field for backward compatibility
        return $this->attributes['role'] === $role;
    }

    // Accessors for empBasicInfo fields
    public function getPreviousEmployerAttribute()
    {
        return $this->empBasicInfo?->previous_employer;
    }

    public function getPreviousEmployerContactPersonAttribute()
    {
        return $this->empBasicInfo?->contact_person;
    }

    public function getPreviousEmployerContactNumberAttribute()
    {
        return $this->empBasicInfo?->contact_number;
    }

    public function getReasonForLeavingAttribute()
    {
        return $this->empBasicInfo?->reason_for_leaving;
    }

    public function getHighestQualificationAttribute()
    {
        return $this->empBasicInfo?->highest_qualification;
    }

    public function getSpecializationAttribute()
    {
        return $this->empBasicInfo?->specialization;
    }

    public function getYearOfPassingAttribute()
    {
        return $this->empBasicInfo?->year_of_passing;
    }

    public function getWorkExperienceAttribute()
    {
        return $this->empBasicInfo?->work_experience;
    }

    public function getFatherMotherNameAttribute()
    {
        return $this->empBasicInfo?->father_mother_name;
    }

    public function getFatherMotherContactNumberAttribute()
    {
        return $this->empBasicInfo?->father_mother_contact_number;
    }

    public function getNomineeNameAttribute()
    {
        return $this->empBasicInfo?->nominee_name;
    }

    public function getNomineeContactNumberAttribute()
    {
        return $this->empBasicInfo?->nominee_contact_number;
    }

    public function getEmergencyContactAttribute()
    {
        return $this->empBasicInfo?->emergency_contact;
    }

    public function getAadharNumberAttribute()
    {
        return $this->empBasicInfo?->aadhar_number;
    }

    public function getPanNumberAttribute()
    {
        return $this->empBasicInfo?->pan_number;
    }

    public function getPassportNumberAttribute()
    {
        return $this->empBasicInfo?->passport_number;
    }

    public function getPresentAddressAttribute()
    {
        return $this->empBasicInfo?->present_address;
    }

    public function getPermanentAddressAttribute()
    {
        return $this->empBasicInfo?->permanent_address;
    }
}
