<?php

require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

Auth::requireRole('admin', 'doctor', 'patient');

$action = $_GET['action'] ?? 'index';

match ($action) {
    'index'    => listPrescriptions(),
    'add'      => showAdd(),
    'store'    => storePrescription(),
    'download' => downloadFile(),
    default    => redirect(url('prescriptions', 'index')),
};

function listPrescriptions(): void
{
    $role   = Auth::role();
    $userId = Auth::currentUser()['id'];

    if ($role === 'doctor') {
        $doctor = (new DoctorModel())->findByUserId($userId);
        if (!$doctor) {
            Auth::flash('danger', 'Doctor profile not found.');
            redirect(url('dashboard'));
        }
        $prescriptions = (new PrescriptionModel())->getByDoctor((int)$doctor['id']);
    } else {

        $prescriptions = (new PrescriptionModel())->getByPatient($userId);
    }

    require_once __DIR__ . '/../views/prescriptions/index.php';
}

function showAdd(): void
{
    Auth::requireRole('doctor');
    $apptId = (int)($_GET['appt_id'] ?? 0);
    $appt   = getVerifiedAppointment($apptId);
    require_once __DIR__ . '/../views/prescriptions/add.php';
}

function storePrescription(): void
{
    Auth::requireRole('doctor');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('prescriptions', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $apptId      = (int)($_POST['appt_id']     ?? 0);
    $diagnosis   = sanitize($_POST['diagnosis']   ?? '');
    $medications = sanitize($_POST['medications'] ?? '');
    $notes       = sanitize($_POST['notes']       ?? '');

    $appt = getVerifiedAppointment($apptId);

    if (!$diagnosis || !$medications) {
        Auth::flash('danger', 'Diagnosis and medications are required.');
        redirect(url('prescriptions', 'add') . '&appt_id=' . $apptId);
    }

    $filePath = uploadPrescriptionFile($apptId);

    (new PrescriptionModel())->create([
        'appointment_id' => $apptId,
        'diagnosis'      => $diagnosis,
        'medications'    => $medications,
        'notes'          => $notes ?: null,
        'file_path'      => $filePath,
    ]);

    Auth::flash('success', 'Prescription saved successfully.');
    redirect(url('appointments', 'detail') . '&id=' . $apptId);
}

function downloadFile(): void
{
    $apptId = (int)($_GET['id'] ?? 0);
    $appt   = (new AppointmentModel())->findById($apptId);

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

    $prescription = (new PrescriptionModel())->findByAppointmentId($apptId);

    if (!$prescription || !$prescription['file_path']) {
        Auth::flash('warning', 'No file attached to this prescription.');
        redirect(url('appointments', 'detail') . '&id=' . $apptId);
    }

    $fullPath = UPLOAD_PRESCRIPTIONS . $prescription['file_path'];

    if (!file_exists($fullPath)) {
        Auth::flash('danger', 'File not found on server.');
        redirect(url('appointments', 'detail') . '&id=' . $apptId);
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="prescription.pdf"');
    header('Content-Length: ' . filesize($fullPath));
    readfile($fullPath);
    exit;
}

function getVerifiedAppointment(int $apptId): array
{
    $appt = (new AppointmentModel())->findById($apptId);

    if (!$appt) {
        Auth::flash('danger', 'Appointment not found.');
        redirect(url('appointments', 'index'));
    }

    if ($appt['status'] !== 'completed') {
        Auth::flash('warning', 'Prescription can only be added to completed appointments.');
        redirect(url('appointments', 'detail') . '&id=' . $apptId);
    }

    $doctor = (new DoctorModel())->findByUserId(Auth::currentUser()['id']);
    if (!$doctor || $appt['doctor_id'] !== (int)$doctor['id']) {
        show403();
    }

    if ((new PrescriptionModel())->findByAppointmentId($apptId)) {
        Auth::flash('warning', 'A prescription already exists for this appointment.');
        redirect(url('appointments', 'detail') . '&id=' . $apptId);
    }

    return $appt;
}

function uploadPrescriptionFile(int $apptId): ?string
{
    if (empty($_FILES['prescription_file']['name'])) {
        return null;
    }

    $file = $_FILES['prescription_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        Auth::flash('danger', 'Upload error. Please try again.');
        redirect(url('prescriptions', 'add') . '&appt_id=' . $apptId);
    }

    if ($file['size'] > MAX_PRESCRIPTION_SIZE) {
        Auth::flash('danger', 'File must be under 3MB.');
        redirect(url('prescriptions', 'add') . '&appt_id=' . $apptId);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($mime !== 'application/pdf') {
        Auth::flash('danger', 'Only PDF files are allowed.');
        redirect(url('prescriptions', 'add') . '&appt_id=' . $apptId);
    }

    $filename = 'prescription_' . $apptId . '_' . time() . '.pdf';
    move_uploaded_file($file['tmp_name'], UPLOAD_PRESCRIPTIONS . $filename);

    return $filename;
}

function show403(): void
{
    http_response_code(403);
    require_once __DIR__ . '/../views/errors/403.php';
    exit;
}
