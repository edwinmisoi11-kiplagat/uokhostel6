<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

function fail(string $message, int $status = 400): void {
    http_response_code($status);
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Application status</title><body style="font-family:Arial,sans-serif;background:#edf4ff;padding:32px;color:#17263c">';
    echo '<main style="max-width:620px;margin:8vh auto;background:white;padding:28px;border-radius:16px">';
    echo '<h1>Hostel 6 Application</h1><p>' . $safeMessage . '</p>';
    echo '<p><a href="index.html">Return to the application form</a></p></main></body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405) ;
    header('Allow: POST'||'Location: index.html');
    exit;
}

$fullName = trim((string)($_POST['full_name'] ?? ''));
$registrationNumber = trim((string)($_POST['registration_number'] ?? ''));
$roomNumberRaw = trim((string)($_POST['room_number'] ?? ''));
$yearRaw = trim((string)($_POST['year_of_study'] ?? ''));
$phoneNumber = trim((string)($_POST['phone_number'] ?? ''));

if ($fullName === '' || $registrationNumber === '' || $roomNumberRaw === '' || $yearRaw === '' || $phoneNumber === '') {
    fail('Please complete all required fields.');
}
if (mb_strlen($fullName) > 120 || mb_strlen($registrationNumber) > 50 || mb_strlen($phoneNumber) > 20) {
    fail('One or more fields exceed the allowed length.');
}
if (!filter_var($roomNumberRaw, FILTER_VALIDATE_INT) || (int)$roomNumberRaw < 1 || (int)$roomNumberRaw > 300) {
    fail('Room number must be between 1 and 300.');
}
if (!in_array((int)$yearRaw, [1, 2, 3, 4], true)) {
    fail('Year of study must be between 1 and 4.');
}
$digits = preg_replace('/\D+/', '', $phoneNumber);
if ($digits === null || strlen($digits) < 9 || strlen($digits) > 15) {
    fail('Please provide a valid phone number containing 9 to 15 digits.');
}

try {
    $sql = 'INSERT INTO applications
            (full_name, registration_number, room_number, year_of_study, phone_number)
            VALUES (:full_name, :registration_number, :room_number, :year_of_study, :phone_number)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $fullName,
        ':registration_number' => $registrationNumber,
        ':room_number' => (int)$roomNumberRaw,
        ':year_of_study' => (int)$yearRaw,
        ':phone_number' => $phoneNumber,
    ]);
    $applicationId = (int)$pdo->lastInsertId();
} catch (PDOException $e) {
    // Registration numbers are unique; do not expose raw database errors to visitors.
    error_log('Hostel 6 application insert failed: ' . $e->getMessage());
    if ($e->getCode() === '23000') {
        fail('An application with this registration number may already exist. Please check your details or contact the hostel administrator.', 409);
    }
    fail('We could not save your application right now. Please try again later.', 500);
}

$safeName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeRegistration = htmlspecialchars($registrationNumber, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Application Submitted | Hostel 6</title>
</head>
<body style="margin:0;background:linear-gradient(135deg,#07366c,#2478cb);font-family:Arial,sans-serif;color:#17263c;padding:24px">
  <main style="max-width:650px;margin:8vh auto;background:white;border-radius:20px;padding:32px;box-shadow:0 18px 50px #00204444">
    <p style="color:#1769c2;font-weight:bold;letter-spacing:2px;font-size:12px">UNIVERSITY OF KABIANGA</p>
    <h1>Application received</h1>
    <p>Thank you, <?= $safeName ?>. Your Hostel 6 accommodation application has been submitted successfully for review.</p>
    <div style="background:#eaf3ff;border-radius:12px;padding:16px;line-height:1.8">
      <strong>Application reference:</strong> H6-<?= str_pad((string)$applicationId, 6, '0', STR_PAD_LEFT) ?><br>
      <strong>Registration number:</strong> <?= $safeRegistration ?><br>
      <strong>Status:</strong> Pending administrator review
    </div>
    <p style="color:#65748a;font-size:13px">Room allocation is subject to availability and approval by the hostel administrator.</p>
    <a href="index.html" style="display:inline-block;margin-top:12px;padding:12px 18px;background:#0b4f9c;color:white;text-decoration:none;border-radius:9px">Back to portal</a>
  </main>
</body>
</html>
