<?php

require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

Auth::requireRole('admin');

$action = $_GET['action'] ?? 'index';

match ($action) {
    'index'  => showReport(),
    default  => redirect(url('reports', 'index')),
};

function showReport(): void
{
    $doctors    = (new DoctorModel())->getAll();
    $results    = [];
    $summary    = [];
    $hasFilter  = false;

    $startDate = sanitize($_GET['start_date'] ?? '');
    $endDate   = sanitize($_GET['end_date']   ?? '');
    $doctorId  = (int)($_GET['doctor_id']     ?? 0) ?: null;
    $status    = sanitize($_GET['status']      ?? '');

    if ($startDate && $endDate) {

        if ($startDate > $endDate) {
            Auth::flash('danger', 'Start date must be before end date.');
            redirect(url('reports', 'index'));
        }

        $hasFilter = true;
        $filters   = array_filter([
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'doctor_id'  => $doctorId,
            'status'     => $status,
        ]);

        $results = (new AppointmentModel())->getAll(0, 10000, $filters);

        foreach ($results as $row) {
            $s = $row['status'];
            $summary[$s] = ($summary[$s] ?? 0) + 1;
        }

        if (($_GET['export'] ?? '') === 'csv') {
            exportCsv($results, $startDate, $endDate);
        }
    }

    require_once __DIR__ . '/../views/reports/index.php';
}

function exportCsv(array $results, string $start, string $end): void
{
    $filename = 'appointments_' . $start . '_to_' . $end . '.csv';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');

    fputcsv($out, ['Patient', 'Email', 'Doctor', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);

    foreach ($results as $row) {
        fputcsv($out, [
            $row['patient_name'],
            $row['patient_email'] ?? '',
            $row['doctor_name'],
            $row['specialization_name'],
            $row['appt_date'],
            $row['appt_time'],
            $row['status'],
            $row['reason'] ?? '',
        ]);
    }

    fclose($out);
    exit;
}
