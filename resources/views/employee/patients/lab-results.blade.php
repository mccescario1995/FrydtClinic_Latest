@extends('employee.layouts.app')

@section('title', 'Lab Results - ' . $patient->name)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Lab Results</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employee.patients.create-lab-result', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">+ Add Lab Result</a>
            <a href="{{ route('employee.patients.medical-records', $patient->id) }}" onclick="sessionStorage.setItem('lastMedicalRecordsTab', 'lab');" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back</a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Lab Results List -->
    @if($labResults->count() > 0)
        <div style="background: white; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Test Name</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Category</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Result</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Status</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Ordered Date</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labResults as $result)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 15px; border-right: 1px solid #dee2e6;">
                                    <strong style="color: #333;">{{ $result->test_name }}</strong>
                                    @if($result->urgent)
                                        <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 8px;">URGENT</span>
                                    @endif
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $result->test_category }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    @if($result->result_value)
                                        <strong>{{ $result->getResultDisplayAttribute() }}</strong>
                                        @if($result->isAbnormal())
                                            <span style="color: #dc3545; font-weight: bold;"> ({{ $result->result_status }})</span>
                                        @endif
                                    @else
                                        Pending
                                    @endif
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6;">
                                    <span style="background: {{ $result->getTestStatusBadgeClass() === 'success' ? '#d4edda' : ($result->getTestStatusBadgeClass() === 'warning' ? '#fff3cd' : '#e2e3e5') }}; color: {{ $result->getTestStatusBadgeClass() === 'success' ? '#155724' : ($result->getTestStatusBadgeClass() === 'warning' ? '#856404' : '#383d41') }}; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                                        {{ ucfirst(str_replace('_', ' ', $result->test_status)) }}
                                    </span>
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ \Carbon\Carbon::parse($result->test_ordered_date_time)->format('M d, Y') }}
                                </td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('employee.patients.show-lab-result', [$patient->id, $result->id]) }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; display: inline-block;">View</a>
                                        <a href="{{ route('employee.patients.edit-lab-result', [$patient->id, $result->id]) }}" style="background: #ffc107; color: black; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; display: inline-block;">Edit</a>
                                        <form method="POST" action="{{ route('employee.patients.delete-lab-result', [$patient->id, $result->id]) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this lab result?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; cursor: pointer;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding: 15px; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: center;">
                {{ $labResults->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div style="background: white; border: 2px dashed #dee2e6; border-radius: 8px; padding: 60px 20px; text-align: center;">
            <div style="font-size: 48px; color: #dee2e6; margin-bottom: 20px;">🧪</div>
            <h3 style="color: #666; margin: 0 0 10px 0;">No Lab Results Found</h3>
            <p style="color: #999; margin: 0 0 20px 0;">There are no lab results for this patient yet.</p>
            <a href="{{ route('employee.patients.create-lab-result', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; display: inline-block;">
                Add First Lab Result
            </a>
        </div>
    @endif
</div>
@endsection
