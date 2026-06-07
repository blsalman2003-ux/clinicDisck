<?php
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

Auth::requireRole('admin', 'doctor');

$action = $_GET['action'] ?? 'index';

match ($action) {
    'index'           => listDoctors(),
    'edit'            => showEdit(),
    'update'          => updateDoctor(),
    'profile'         => doctorProfile(),
    'update_profile'  => updateDoctorProfile(),
    'specializations' => listSpecializations(),
    'store_spec'      => storeSpec(),
    'delete_spec'     => deleteSpec(),
    default           => redirect(url('doctors', 'index')),
};

function listDoctors(): void
{
    Auth::requireRole('admin');
    $doctorModel = new DoctorModel();
    $currentPage = max(1, (int)($_GET['p'] ?? 1));
    $total   = $doctorModel->countAll();
    $pager   = new Paginator($total, ITEMS_PER_PAGE, $currentPage);
    $doctors = $doctorModel->getAllPaginated($pager->offset(), ITEMS_PER_PAGE);
    require_once __DIR__ . '/../views/doctors/index.php';
}

function showEdit(): void
{
    Auth::requireRole('admin');
    $doctor = (new DoctorModel())->findById((int)($_GET['id'] ?? 0));
    if (!$doctor) {
        Auth::flash('danger', 'Doctor not found.');
        redirect(url('doctors', 'index'));
    }
    $specializations = (new SpecializationModel())->getAll();
    require_once __DIR__ . '/../views/doctors/edit.php';
}

function updateDoctor(): void
{
    Auth::requireRole('admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('doctors', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id   = (int)($_POST['doctor_id'] ?? 0);
    $data = buildDoctorData($id);

    (new DoctorModel())->update($id, $data);
    Auth::flash('success', 'Doctor updated successfully.');
    redirect(url('doctors', 'index'));
}

function doctorProfile(): void
{
    Auth::requireRole('doctor');
    $doctor = (new DoctorModel())->findByUserId(Auth::currentUser()['id']);
    if (!$doctor) {
        Auth::flash('danger', 'Doctor profile not found. Contact admin.');
        redirect(url('dashboard'));
    }
    $specializations = (new SpecializationModel())->getAll();
    require_once __DIR__ . '/../views/doctors/profile.php';
}

function updateDoctorProfile(): void
{
    Auth::requireRole('doctor');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('doctors', 'profile'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $doctor = (new DoctorModel())->findByUserId(Auth::currentUser()['id']);
    if (!$doctor) {
        Auth::flash('danger', 'Profile not found.');
        redirect(url('dashboard'));
    }

    $data = buildDoctorData($doctor['id']);
    (new DoctorModel())->update($doctor['id'], $data);

    Auth::flash('success', 'Profile updated successfully.');
    redirect(url('doctors', 'profile'));
}

function buildDoctorData(int $doctorId): array
{
    $data = [
        'bio'              => sanitize($_POST['bio']              ?? ''),
        'consultation_fee' => (float)($_POST['consultation_fee']  ?? 0),
        'available_days'   => !empty($_POST['available_days'])
            ? implode(',', array_map('sanitize', $_POST['available_days']))
            : 'Sun,Mon,Tue,Wed,Thu',
    ];

    if (!empty($_POST['specialization_id'])) {
        $data['specialization_id'] = (int)$_POST['specialization_id'];
    }

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];

        if ($file['size'] > MAX_DOCTOR_PHOTO_SIZE) {
            Auth::flash('danger', 'Photo must be under 1MB.');
            redirect(url('doctors', 'profile'));
        }
        if (!getimagesize($file['tmp_name'])) {
            Auth::flash('danger', 'Invalid image file.');
            redirect(url('doctors', 'profile'));
        }
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'doctor_' . $doctorId . '_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], UPLOAD_DOCTOR_PHOTOS . $filename);
        $data['photo'] = $filename;
    }

    return $data;
}

function listSpecializations(): void
{
    Auth::requireRole('admin');
    $specializations = (new SpecializationModel())->getAll();
    require_once __DIR__ . '/../views/doctors/specializations.php';
}

function storeSpec(): void
{
    Auth::requireRole('admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('doctors', 'specializations'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $name  = sanitize($_POST['name'] ?? '');
    $model = new SpecializationModel();

    if (!$name) {
        Auth::flash('danger', 'Name is required.');
        redirect(url('doctors', 'specializations'));
    }
    if ($model->nameExists($name)) {
        Auth::flash('danger', 'Specialization already exists.');
        redirect(url('doctors', 'specializations'));
    }

    $model->create($name);
    Auth::flash('success', 'Specialization added.');
    redirect(url('doctors', 'specializations'));
}

function deleteSpec(): void
{
    Auth::requireRole('admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('doctors', 'specializations'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id    = (int)($_POST['id'] ?? 0);
    $model = new SpecializationModel();

    if (!$model->isSafeToDelete($id)) {
        Auth::flash('danger', 'Cannot delete — doctors are using this specialization.');
        redirect(url('doctors', 'specializations'));
    }

    $model->delete($id);
    Auth::flash('success', 'Specialization deleted.');
    redirect(url('doctors', 'specializations'));
}
