<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

$action = $_GET['action'] ?? 'index';

match ($action) {
    'index'           => listUsers(),
    'create'          => showCreate(),
    'store'           => storeUser(),
    'edit'            => showEdit(),
    'update'          => updateUser(),
    'toggle'          => toggleUser(),
    'profile'         => showProfile(),
    'update_profile'  => updateProfile(),
    'change_password' => changePassword(),
    default           => redirect(url('users', 'index')),
};

function listUsers(): void
{
    Auth::requireRole('admin');
    $userModel   = new UserModel();
    $search      = sanitize($_GET['search'] ?? '');
    $roleFilter  = sanitize($_GET['role']   ?? '');
    $currentPage = max(1, (int)($_GET['p']  ?? 1));

    $total = $userModel->countAll($roleFilter, $search);
    $pager = new Paginator($total, ITEMS_PER_PAGE, $currentPage);
    $users = $userModel->getAllPaginated($pager->offset(), ITEMS_PER_PAGE, $roleFilter, $search);

    require_once __DIR__ . '/../views/users/index.php';
}

function showCreate(): void
{
    Auth::requireRole('admin');
    $specializations = (new SpecializationModel())->getAll();
    require_once __DIR__ . '/../views/users/create.php';
}

function storeUser(): void
{
    Auth::requireRole('admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('users', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $name     = sanitize($_POST['name']     ?? '');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $role     = sanitize($_POST['role']     ?? '');
    $phone    = sanitize($_POST['phone']    ?? '');
    $password = $_POST['password']          ?? '';

    $errors = [];
    if (!$name)                                         $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'Valid email is required.';
    if (!in_array($role, ['admin','doctor','patient']))  $errors[] = 'Invalid role.';
    if (strlen($password) < 8)                          $errors[] = 'Password must be at least 8 characters.';

    $userModel = new UserModel();
    if ($userModel->emailExists($email))                $errors[] = 'Email already in use.';

    if ($errors) {
        flashOldInput($_POST);
        Auth::flash('danger', implode(' ', $errors));
        redirect(url('users', 'create'));
    }

    $userId = $userModel->create([
        'name'     => $name,
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'role'     => $role,
        'phone'    => $phone ?: null,
    ]);

    if ($role === 'doctor' && $userId) {
        $days = !empty($_POST['available_days'])
            ? implode(',', array_map('sanitize', $_POST['available_days']))
            : 'Sun,Mon,Tue,Wed,Thu';

        (new DoctorModel())->create([
            'user_id'           => $userId,
            'specialization_id' => (int)($_POST['specialization_id'] ?? 0),
            'bio'               => sanitize($_POST['bio'] ?? ''),
            'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
            'available_days'    => $days,
        ]);
    }

    Auth::flash('success', 'User created successfully.');
    redirect(url('users', 'index'));
}

function showEdit(): void
{
    Auth::requireRole('admin');
    $user = (new UserModel())->findById((int)($_GET['id'] ?? 0));
    if (!$user) {
        Auth::flash('danger', 'User not found.');
        redirect(url('users', 'index'));
    }
    $specializations = (new SpecializationModel())->getAll();
    $doctorRecord    = $user['role'] === 'doctor'
        ? (new DoctorModel())->findByUserId($user['id'])
        : null;

    require_once __DIR__ . '/../views/users/edit.php';
}

function updateUser(): void
{
    Auth::requireRole('admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('users', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id    = (int)($_POST['id']    ?? 0);
    $name  = sanitize($_POST['name']  ?? '');
    $phone = sanitize($_POST['phone'] ?? '');

    $userModel = new UserModel();
    $user = $userModel->findById($id);
    if (!$user) {
        Auth::flash('danger', 'User not found.');
        redirect(url('users', 'index'));
    }

    $userModel->update($id, ['name' => $name, 'phone' => $phone ?: null]);

    if ($user['role'] === 'doctor') {
        $doctorModel = new DoctorModel();
        $doctor = $doctorModel->findByUserId($id);
        if ($doctor) {
            $days = !empty($_POST['available_days'])
                ? implode(',', array_map('sanitize', $_POST['available_days']))
                : 'Sun,Mon,Tue,Wed,Thu';

            $doctorModel->update($doctor['id'], [
                'specialization_id' => (int)($_POST['specialization_id'] ?? $doctor['specialization_id']),
                'bio'               => sanitize($_POST['bio'] ?? ''),
                'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
                'available_days'    => $days,
            ]);
        }
    }

    Auth::flash('success', 'User updated successfully.');
    redirect(url('users', 'index'));
}

function toggleUser(): void
{
    Auth::requireRole('admin');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('users', 'index'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id = (int)($_POST['id'] ?? 0);

    if ($id === Auth::currentUser()['id']) {
        Auth::flash('warning', 'You cannot deactivate your own account.');
        redirect(url('users', 'index'));
    }

    (new UserModel())->toggleActive($id);
    Auth::flash('success', 'User status updated.');
    redirect(url('users', 'index'));
}

function showProfile(): void
{
    Auth::requireRole('admin', 'doctor', 'patient');
    $user = (new UserModel())->findById(Auth::currentUser()['id']);
    if (!$user) {
        Auth::flash('danger', 'Profile not found.');
        redirect(url('dashboard'));
    }
    require_once __DIR__ . '/../views/users/profile.php';
}

function updateProfile(): void
{
    Auth::requireRole('admin', 'doctor', 'patient');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(url('users', 'profile'));
    CSRF::verify($_POST['csrf_token'] ?? '');

    $id    = Auth::currentUser()['id'];
    $name  = sanitize($_POST['name']  ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $data  = ['name' => $name, 'phone' => $phone ?: null];

    if (!$name) {
        Auth::flash('danger', 'Name is required.');
        redirect(url('users', 'profile'));
    }

    if (!empty($_FILES['avatar']['name'])) {
        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            Auth::flash('danger', 'Upload error. Try again.');
            redirect(url('users', 'profile'));
        }
        if ($file['size'] > MAX_AVATAR_SIZE) {
            Auth::flash('danger', 'Avatar must be under 1MB.');
            redirect(url('users', 'profile'));
        }
        if (!getimagesize($file['tmp_name'])) {
            Auth::flash('danger', 'Invalid image file. Please upload JPEG or PNG.');
            redirect(url('users', 'profile'));
        }
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            Auth::flash('danger', 'Only JPG and PNG images allowed.');
            redirect(url('users', 'profile'));
        }
        $filename = 'avatar_' . $id . '_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], UPLOAD_AVATARS . $filename);
        $data['avatar'] = $filename;
    }

    (new UserModel())->update($id, $data);
    Auth::flash('success', 'Profile updated successfully.');
    redirect(url('users', 'profile'));
}

function changePassword(): void
{
    Auth::requireRole('admin', 'doctor', 'patient');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        require_once __DIR__ . '/../views/users/change_password.php';
        return;
    }

    CSRF::verify($_POST['csrf_token'] ?? '');

    $id      = Auth::currentUser()['id'];
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $userModel = new UserModel();
    $user      = $userModel->findById($id);

    if (!password_verify($current, $user['password'])) {
        Auth::flash('danger', 'Current password is incorrect.');
        redirect(url('users', 'change_password'));
    }
    if (strlen($new) < 8) {
        Auth::flash('danger', 'New password must be at least 8 characters.');
        redirect(url('users', 'change_password'));
    }
    if (!preg_match('/[A-Z]/', $new)) {
        Auth::flash('danger', 'New password must contain at least one uppercase letter.');
        redirect(url('users', 'change_password'));
    }
    if (!preg_match('/[0-9]/', $new)) {
        Auth::flash('danger', 'New password must contain at least one number.');
        redirect(url('users', 'change_password'));
    }
    if ($new !== $confirm) {
        Auth::flash('danger', 'Passwords do not match.');
        redirect(url('users', 'change_password'));
    }

    $userModel->updatePassword($id, password_hash($new, PASSWORD_BCRYPT));
    Auth::flash('success', 'Password changed successfully.');
    redirect(url('dashboard'));
}
