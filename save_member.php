<?php
session_start();
require 'config.php';
// 1) Only a logged-in admin may save changes
if (empty($_SESSION['admin'])) {
    die('Not authorized. Please log in as admin first.');
}
// 2) Which member card (0 to 4) was edited?
$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
$dataFile = __DIR__ . '/data/members.json';
$members  = json_decode(file_get_contents($dataFile), true);
if (!is_array($members) || !isset($members[$index])) {
    die('Invalid member.');
}
// 3) Update the text fields (only if the admin actually typed something)
$name      = trim($_POST['name'] ?? '');
$role      = trim($_POST['role'] ?? '');
$skillsRaw = trim($_POST['skills'] ?? '');
$bio       = trim($_POST['bio'] ?? '');
$email     = trim($_POST['email'] ?? '');
if ($name !== '') $members[$index]['name'] = $name;
if ($role !== '') $members[$index]['role'] = $role;
if ($skillsRaw !== '') {
    $skills = array_filter(array_map('trim', explode(',', $skillsRaw)));
    $members[$index]['skills'] = array_values($skills);
}
if ($bio !== '') $members[$index]['bio'] = $bio;
if ($email !== '') $members[$index]['email'] = $email;
// 4) Handle the photo: either an uploaded file OR a pasted image URL
$photoUrl  = trim($_POST['photoUrl'] ?? '');
$uploadDir = __DIR__ . '/uploads/';
if (!empty($_FILES['photoFile']['name']) && $_FILES['photoFile']['error'] === UPLOAD_ERR_OK) {
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($_FILES['photoFile']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        die('Only JPG, PNG, GIF or WEBP images are allowed.');
    }
    if ($_FILES['photoFile']['size'] > 3 * 1024 * 1024) {
        die('Image is too big. Please use a photo under 3MB.');
    }
    if (@getimagesize($_FILES['photoFile']['tmp_name']) === false) {
        die('That file is not a valid image.');
    }
    $newFileName = 'member' . $index . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $newFileName;
    if (move_uploaded_file($_FILES['photoFile']['tmp_name'], $destination)) {
        $members[$index]['photo'] = 'uploads/' . $newFileName;
    } else {
        die('Could not save the uploaded photo. Check that the "uploads" folder is writable (CHMOD 755 or 777).');
    }
} elseif ($photoUrl !== '') {
    $members[$index]['photo'] = $photoUrl;
}
// makes the change visible to EVERY visitor,
file_put_contents($dataFile, json_encode($members, JSON_PRETTY_PRINT), LOCK_EX);
// Send the admin back to the members section to see the result
header('Location: admin/index.php#membersToggle');
exit;