<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appointment;
use App\Models\PatientProfile;
use App\Models\LaboratoryResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Perform a global search across multiple models
     */
    public function globalSearch(string $query, int $perPage = 20): array
    {
        $results = [];

        // Search in Users (Patients, Employees, Doctors)
        $results['users'] = $this->searchUsers($query, 10);

        // Search in Appointments
        $results['appointments'] = $this->searchAppointments($query, 10);

        // Search in Patient Profiles
        $results['patients'] = $this->searchPatientProfiles($query, 10);

        // Search in Laboratory Results
        $results['lab_results'] = $this->searchLabResults($query, 10);

        return $results;
    }

    /**
     * Search users with role-based filtering
     */
    public function searchUsers(string $query, int $limit = null): Collection
    {
        $queryBuilder = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        });

        if ($limit) {
            $queryBuilder->limit($limit);
        }

        return $queryBuilder->with(['roles', 'patientProfile'])->get();
    }

    /**
     * Search appointments
     */
    public function searchAppointments(string $query, int $limit = null): Collection
    {
        $queryBuilder = Appointment::with(['patient', 'employee', 'service'])
            ->where(function ($q) use ($query) {
                $q->whereHas('patient', function ($patientQuery) use ($query) {
                    $patientQuery->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                })
                ->orWhereHas('employee', function ($employeeQuery) use ($query) {
                    $employeeQuery->where('name', 'like', "%{$query}%");
                })
                ->orWhereHas('service', function ($serviceQuery) use ($query) {
                    $serviceQuery->where('name', 'like', "%{$query}%");
                })
                ->orWhere('patient_notes', 'like', "%{$query}%")
                ->orWhere('employee_notes', 'like', "%{$query}%");
            });

        if ($limit) {
            $queryBuilder->limit($limit);
        }

        return $queryBuilder->orderBy('appointment_datetime', 'desc')->get();
    }

    /**
     * Search patient profiles
     */
    public function searchPatientProfiles(string $query, int $limit = null): Collection
    {
        $queryBuilder = PatientProfile::with('user')
            ->where(function ($q) use ($query) {
                $q->where('phone', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('emergency_contact_name', 'like', "%{$query}%")
                  ->orWhere('emergency_contact_phone', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%")
                               ->orWhere('email', 'like', "%{$query}%");
                  });
            });

        if ($limit) {
            $queryBuilder->limit($limit);
        }

        return $queryBuilder->get();
    }

    /**
     * Search laboratory results
     */
    public function searchLabResults(string $query, int $limit = null): Collection
    {
        $queryBuilder = LaboratoryResult::with(['patient.user', 'orderingProvider', 'performingTechnician'])
            ->where(function ($q) use ($query) {
                $q->where('test_name', 'like', "%{$query}%")
                  ->orWhere('test_code', 'like', "%{$query}%")
                  ->orWhere('result_value', 'like', "%{$query}%")
                  ->orWhereHas('patient.user', function ($patientQuery) use ($query) {
                      $patientQuery->where('name', 'like', "%{$query}%");
                  });
            });

        if ($limit) {
            $queryBuilder->limit($limit);
        }

        return $queryBuilder->orderBy('result_available_date_time', 'desc')->get();
    }

    /**
     * Advanced search with filters
     */
    public function advancedSearch(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = User::query();

        // Apply user type filter
        if (isset($filters['user_type'])) {
            switch ($filters['user_type']) {
                case 'patient':
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Patient');
                    });
                    break;
                case 'employee':
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Employee');
                    });
                    break;
                case 'doctor':
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Doctor');
                    });
                    break;
            }
        }

        // Apply date range filter
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        }

        // Apply search query
        if (isset($filters['query']) && !empty($filters['query'])) {
            $searchTerm = $filters['query'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get search suggestions
     */
    public function getSearchSuggestions(string $query, int $limit = 5): array
    {
        $suggestions = [];

        // User name suggestions
        $userSuggestions = User::where('name', 'like', "%{$query}%")
            ->limit($limit)
            ->pluck('name')
            ->toArray();

        // Service suggestions
        $serviceSuggestions = \App\Models\Service::where('name', 'like', "%{$query}%")
            ->limit($limit)
            ->pluck('name')
            ->toArray();

        // Test name suggestions
        $testSuggestions = LaboratoryResult::where('test_name', 'like', "%{$query}%")
            ->distinct()
            ->limit($limit)
            ->pluck('test_name')
            ->toArray();

        return [
            'users' => $userSuggestions,
            'services' => $serviceSuggestions,
            'tests' => $testSuggestions,
        ];
    }

    /**
     * Get search statistics
     */
    public function getSearchStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_patients' => User::role('Patient')->count(),
            'total_appointments' => Appointment::count(),
            'total_lab_results' => LaboratoryResult::count(),
            'recent_searches' => [], // Could be implemented with a separate searches table
        ];
    }
}
