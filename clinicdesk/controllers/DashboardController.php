<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

Auth::requireRole('admin', 'doctor', 'patient');

$role   = Auth::role();
$userId = Auth::currentUser()['id'];

match ($role) {
    'admin'   => adminDashboard(),
    'doctor'  => doctorDashboard($userId),
    'patient' => patientDashboard($userId),
};

function adminDashboard(): void
{
    $userModel = new UserModel();
    $apptModel = new AppointmentModel();

    $roleCounts = [];
    foreach ($userModel->countByRole() as $row) {
        $roleCounts[$row['role']] = (int)$row['total'];
    }

    $weekStatus = [];
    foreach ($apptModel->countThisWeekByStatus() as $row) {
        $weekStatus[$row['status']] = (int)$row['total'];
    }

    $stats = [
        'patients'    => $roleCounts['patient'] ?? 0,
        'doctors'     => $roleCounts['doctor']  ?? 0,
        'today'       => $apptModel->countToday(),
        'week_total'  => array_sum($weekStatus),
        'week_status' => $weekStatus,
    ];

    $recentAppointments = $apptModel->getRecent(5);

    require_once __DIR__ . '/../views/dashboard/admin.php';
}

function doctorDashboard(int $userId): void
{
    $doctor = (new DoctorModel())->findByUserId($userId);

    if (!$doctor) {
        Auth::flash('danger', 'Doctor profile not found. Contact admin.');
        redirect(url('auth', 'login'));
    }

    $apptModel         = new AppointmentModel();
    $doctorId          = (int)$doctor['id'];
    $stats             = $apptModel->countMonthStatsByDoctor($doctorId);
    $todayAppointments = $apptModel->getTodayByDoctor($doctorId);

    require_once __DIR__ . '/../views/dashboard/doctor.php';
}

function patientDashboard(int $userId): void
{
    $apptModel = new AppointmentModel();

    $stats             = $apptModel->countStatsByPatient($userId);
    $upcoming          = $apptModel->getUpcomingByPatient($userId, 1);
    $nextAppointment   = $upcoming[0] ?? null;
    $prescriptionCount = (new PrescriptionModel())->countByPatient($userId);

    require_once __DIR__ . '/../views/dashboard/patient.php';
}
