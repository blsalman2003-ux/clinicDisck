<?php

require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

Auth::requireRole('admin', 'doctor', 'patient');

$action = $_GET['action'] ?? 'index';

match ($action) {
    'index'  => listAppointments(),
    'book'   => showBook(),
    'store'  => storeAppointment(),
    'detail' => showDetail(),
    'status' => updateStatus(),
    'cancel' => cancelAppointment(),
    default  => redirect(url('appointments', 'index')),
};

function listAppointments(): void
{
    $apptModel   = new AppointmentModel();
    $role        = Auth::role();
    $userId      = Auth::currentUser()['id'];
    $currentPage = max(1, (int)($_GET['p'] ?? 1));

    $filters = array_filter([
        'status'       => sanitize($_GET['status']       ?? ''),
        'start_date'   => sanitize($_GET['start_date']   ?? ''),
        'end_date'     => sanitize($_GET['end_date']     ?? ''),
        'doctor_id'    => (int)($_GET['doctor_id']       ?? 0) ?: null,
        'patient_name' => sanitize($_GET['patient_name'] ?? ''),
    ]);

    if ($role === 'patient') {
        $total        = $apptModel->countFiltered('patient', $userId, $filters);
        $pager        = new Paginator($total, ITEMS_PER_PAGE, $currentPage);
        $appointments = $apptModel->getByPatient($userId, $pager->offset(), ITEMS_PER_PAGE, $filters);

    } elseif ($role === 'doctor') {
        $doctor       = (new DoctorModel())->findByUserId($userId);
        $doctorId     = (int)$doctor['id'];
        $total        = $apptModel->countFiltered('doctor', $doctorId, $filters);
        $pager        = new Paginator($total, ITEMS_PER_PAGE, $currentPage);
        $appointments = $apptModel->getByDoctor($doctorId, $pager->offset(), ITEMS_PER_PAGE, $filters);
        $todayAppointments = $apptModel->getTodayByDoctor($doctorId);

    } else {

        $doctors      = (new DoctorModel())->getAll();
        $total        = $apptModel->countFiltered('admin', 0, $filters);
        $pager        = new Paginator($total, ITEMS_PER_PAGE, $currentPage);
        $appointments = $apptModel->getAll($pager->offset(), ITEMS_PER_PAGE, $filters);
    }

    require_once __DIR__ . '/../views/appointments/index.php';
}

function showBook(): void
{
    Auth::requireRole('patient');
    $doctors   = (new DoctorModel())->getAll();
    $timeSlots = TIME_SLOTS;
    require_once __DIR__ . '/../views/appointments/book.php';
}

function storeAppointment(): void
{
    Auth::requireRole('patient');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('appointments', 'book'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $patientId = Auth::currentUser()['id'];
    $doctorId  = (int)($_POST['doctor_id']  ?? 0);
    $date      = sanitize($_POST['appt_date'] ?? '');
    $time      = sanitize($_POST['appt_time'] ?? '');
    $reason    = sanitize($_POST['reason']    ?? '');

    $errors = [];
    if (!$doctorId)                              $errors[] = 'Please select a doctor.';
    if (!$date)                                  $errors[] = 'Please select a date.';
    if (!$time)                                  $errors[] = 'Please select a time slot.';
    if ($date && strtotime($date) < strtotime('today')) $errors[] = 'Date cannot be in the past.';

    if ($date && $doctorId) {
        $availDays = (new DoctorModel())->getAvailableDays($doctorId);
        $dayName   = date('D', strtotime($date));
        if (!in_array($dayName, $availDays)) {
            $errors[] = 'Doctor is not available on ' . date('l', strtotime($date)) . '.';
        }
    }

    if ($errors) {
        flashOldInput($_POST);
        Auth::flash('danger', implode(' ', $errors));
        redirect(url('appointments', 'book'));
    }

    $apptModel = new AppointmentModel();
    if ($apptModel->hasConflict($doctorId, $date, $time)) {
        flashOldInput($_POST);
        Auth::flash('danger', 'This slot is already booked. Please choose another time.');
        redirect(url('appointments', 'book'));
    }

    $booked = $apptModel->book([
        'patient_id' => $patientId,
        'doctor_id'  => $doctorId,
        'appt_date'  => $date,
        'appt_time'  => $time,
        'reason'     => $reason ?: null,
    ]);

    if ($booked) {
        Auth::flash('success', 'Appointment booked successfully!');
        redirect(url('appointments', 'index'));
    } else {
        Auth::flash('danger', 'Booking failed. Please try again.');
        redirect(url('appointments', 'book'));
    }
}

function showDetail(): void
{
    $id   = (int)($_GET['id'] ?? 0);
    $appt = (new AppointmentModel())->findById($id);

    if (!$appt) {
        Auth::flash('danger', 'Appointment not found.');
        redirect(url('appointments', 'index'));
    }

    $role   = Auth::role();
    $userId = Auth::currentUser()['id'];

    if ($role === 'patient' && $appt['patient_id'] !== $userId) {
        show403();
    }

    if ($role === 'doctor') {
        $doctor = (new DoctorModel())->findByUserId($userId);
        if (!$doctor || $appt['doctor_id'] !== (int)$doctor['id']) {
            show403();
        }
    }

    $prescription = (new PrescriptionModel())->findByAppointmentId($id);

    require_once __DIR__ . '/../views/appointments/detail.php';
}

function updateStatus(): void
{
    Auth::requireRole('admin', 'doctor');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('appointments', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id     = (int)($_POST['appt_id']      ?? 0);
    $status = sanitize($_POST['status']    ?? '');
    $notes  = sanitize($_POST['doctor_notes'] ?? '');

    if (!in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        Auth::flash('danger', 'Invalid status.');
        redirect(url('appointments', 'index'));
    }

    $apptModel = new AppointmentModel();
    $appt      = $apptModel->findById($id);

    if (!$appt) {
        Auth::flash('danger', 'Appointment not found.');
        redirect(url('appointments', 'index'));
    }

    if (Auth::role() === 'doctor') {
        $doctor = (new DoctorModel())->findByUserId(Auth::currentUser()['id']);
        if (!$doctor || $appt['doctor_id'] !== (int)$doctor['id']) {
            show403();
        }
    }

    $apptModel->updateStatus($id, $status, $notes);
    $apptModel->logStatusChange($id, Auth::currentUser()['id'], $appt['status'], $status, $notes);

    Auth::flash('success', 'Status updated to ' . ucfirst($status) . '.');
    redirect(url('appointments', 'detail') . '&id=' . $id);
}

function cancelAppointment(): void
{
    Auth::requireRole('patient');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('appointments', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id     = (int)($_POST['appt_id']       ?? 0);
    $reason = sanitize($_POST['cancel_reason'] ?? '');

    if (strlen($reason) < 10) {
        Auth::flash('danger', 'Please provide a cancellation reason (min 10 characters).');
        redirect(url('appointments', 'detail') . '&id=' . $id);
    }

    $apptModel = new AppointmentModel();
    $appt      = $apptModel->findById($id);

    if (!$appt || $appt['patient_id'] !== Auth::currentUser()['id']) {
        show403();
    }

    if (!in_array($appt['status'], ['pending', 'confirmed'])) {
        Auth::flash('warning', 'This appointment cannot be cancelled.');
        redirect(url('appointments', 'index'));
    }

    $apptModel->cancel($id, Auth::currentUser()['id'], $reason);
    $apptModel->logStatusChange($id, Auth::currentUser()['id'], $appt['status'], 'cancelled', $reason);

    Auth::flash('success', 'Appointment cancelled.');
    redirect(url('appointments', 'index'));
}

function show403(): void
{
    http_response_code(403);
    require_once __DIR__ . '/../views/errors/403.php';
    exit;
}
