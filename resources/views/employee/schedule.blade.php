@extends('employee.layouts.app')

@section('title', 'My Schedule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">My Work Schedule</h4>
                </div>
                <div class="card-body">
                    @if($schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr class="{{ $schedule->day_of_week == now()->dayOfWeekIso ? 'table-primary' : '' }}">
                                            <td>
                                                <strong>{{ $schedule->day_name }}</strong>
                                                @if($schedule->day_of_week == now()->dayOfWeekIso)
                                                    <span class="badge bg-primary">Today</span>
                                                @endif
                                            </td>
                                            <td>{{ $schedule->start_time }}</td>
                                            <td>{{ $schedule->end_time }}</td>
                                            <td>
                                                <span class="badge bg-success">Active</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5>No Schedule Found</h5>
                            <p>You don't have any work schedule assigned yet. Please contact your administrator.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
