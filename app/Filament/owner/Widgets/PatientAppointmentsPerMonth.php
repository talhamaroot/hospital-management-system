<?php

namespace App\Filament\owner\Widgets;

use App\Models\PatientAppointment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PatientAppointmentsPerMonth extends ChartWidget
{
    protected static ?string $heading = 'Patient Appointments Per Month';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // Get the current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Initialize an array to hold daily appointment counts for the current month
        $appointmentsData = array_fill(1, Carbon::now()->daysInMonth, 0);

        // Query to get daily counts of appointments for the current month
        $appointments = PatientAppointment::selectRaw('DAY(created_at) as day, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->groupByRaw('DAY(created_at)')
            ->orderBy('day')
            ->get();

        // Fill the $appointmentsData array with counts for each day of the current month
        foreach ($appointments as $appointment) {
            $day = $appointment->day;
            $count = $appointment->count;
            $appointmentsData[$day] = $count;
        }

        // Labels for each day of the current month
        $labels = [];
        for ($day = 1; $day <= Carbon::now()->daysInMonth; $day++) {
            $labels[] = $day;
        }

        // Prepare the data structure to return
        $data = [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => array_values($appointmentsData), // array_values to reset keys to start from 0
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];

        return $data;
    }
}
