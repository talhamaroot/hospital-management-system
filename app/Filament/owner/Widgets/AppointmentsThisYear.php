<?php

namespace App\Filament\owner\Widgets;

use App\Models\PatientAppointment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AppointmentsThisYear extends ChartWidget
{
    protected static ?string $heading = 'Appointments This Year';

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {

        // Initialize an array to hold monthly appointment counts
        $appointmentsData = [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
        ];

        // Query to get monthly counts of appointments for the current year
        $appointments = PatientAppointment::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get();

        // Fill the $appointmentsData array with counts for each month
        foreach ($appointments as $appointment) {
            $month = $appointment->month;
            $count = $appointment->count;
            $appointmentsData[$month - 1] = $count;
        }

        // Labels for each month (assuming full year)
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Prepare the data structure to return
        $data = [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $appointmentsData,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];

        return $data;
    }
}
