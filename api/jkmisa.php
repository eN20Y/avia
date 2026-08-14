<?php

/*
|--------------------------------------------------------------------------
| JKMISA PAYMENT REMINDER SYSTEM
|--------------------------------------------------------------------------
|
| Due reminder:
| /api/jkmisa.php?jkmisa=fiber&account=ACC001&type=due
|
| Cut-off reminder:
| /api/jkmisa.php?jkmisa=fiber&account=ACC001&type=cutoff
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| GET PARAMETERS
|--------------------------------------------------------------------------
*/

$companyKey = strtolower(trim($_GET['jkmisa'] ?? ''));

$accountNo = trim($_GET['account'] ?? '');

$type = strtolower(trim($_GET['type'] ?? 'due'));


/*
|--------------------------------------------------------------------------
| VALIDATE TYPE
|--------------------------------------------------------------------------
*/

$allowedTypes = [
    'due',
    'cutoff'
];

if (!in_array($type, $allowedTypes, true)) {
    http_response_code(400);
    exit('Invalid reminder type.');
}


/*
|--------------------------------------------------------------------------
| COMPANY CONFIGURATION
|--------------------------------------------------------------------------
*/

$companies = [

    'fiber' => [
        'name' => 'JKMISA Fiber Internet',
        'short_name' => 'JKMISA FIBER',

        'contact' => '09123456789',

        'email' => 'billing@jkmisa.com',

        'address' => 'Cebu City, Philippines',

        'currency' => '₱',

        'primary_color' => '#0d6efd',

        'payment_url' => 'https://payder.vercel.app/payment'
    ],


    /*
    |--------------------------------------------------------------------------
    | ADD MORE COMPANIES HERE
    |--------------------------------------------------------------------------
    */

    'gtech' => [
        'name' => 'GTECHLINK',
        'short_name' => 'GTECHLINK',

        'contact' => '09234567890',

        'email' => 'gtech@gmail.com',

        'address' => 'Cebu City, Philippines',

        'currency' => '₱',

        'primary_color' => '#198754',

        'payment_url' => 'https://payder.vercel.app/payment'
    ]

];


/*
|--------------------------------------------------------------------------
| CHECK COMPANY
|--------------------------------------------------------------------------
*/

if ($companyKey === '') {

    http_response_code(400);

    exit('Missing company.');

}


if (!isset($companies[$companyKey])) {

    http_response_code(404);

    exit('Company not found.');

}


$company = $companies[$companyKey];


/*
|--------------------------------------------------------------------------
| CHECK ACCOUNT
|--------------------------------------------------------------------------
*/

if ($accountNo === '') {

    http_response_code(400);

    exit('Missing account number.');

}


/*
|--------------------------------------------------------------------------
| CUSTOMER DATA
|--------------------------------------------------------------------------
|
| TEMPORARY DATA
|
| Replace this section with your database/API later.
|--------------------------------------------------------------------------
*/

$customers = [

    'ACC001' => [
        'account_no' => 'ACC001',
        'name' => 'Juan Dela Cruz',
        'amount_due' => 999.00,
        'due_date' => '2026-08-15',
        'contact' => '09123456789',

        // Possible:
        // ACTIVE
        // UNPAID
        // CUTOFF
        // PAID

        'status' => 'UNPAID'
    ],


    'ACC002' => [
        'account_no' => 'ACC002',
        'name' => 'Maria Santos',
        'amount_due' => 1499.00,
        'due_date' => '2026-08-10',
        'contact' => '09234567890',
        'status' => 'CUTOFF'
    ]

];


/*
|--------------------------------------------------------------------------
| FIND CUSTOMER
|--------------------------------------------------------------------------
*/

if (!isset($customers[$accountNo])) {

    http_response_code(404);

    exit('Customer account not found.');

}


$customer = $customers[$accountNo];


/*
|--------------------------------------------------------------------------
| DATE CALCULATION
|--------------------------------------------------------------------------
*/

$today = new DateTime();

$dueDate = new DateTime(
    $customer['due_date']
);

$days = (int) $today
    ->diff($dueDate)
    ->format('%r%a');


/*
|--------------------------------------------------------------------------
| DEFAULT VALUES
|--------------------------------------------------------------------------
*/

$title = '';

$message = '';

$alertType = 'warning';

$icon = '';

$showPayButton = true;


/*
|--------------------------------------------------------------------------
| PAID ACCOUNT
|--------------------------------------------------------------------------
*/

if ($customer['status'] === 'PAID') {

    $title = 'Payment Received';

    $message =
        'Your account is already paid. Thank you for your payment.';

    $alertType = 'success';

    $icon = '✓';

    $showPayButton = false;

}


/*
|--------------------------------------------------------------------------
| TEMPORARY CUT-OFF
|--------------------------------------------------------------------------
*/

elseif ($type === 'cutoff' || $customer['status'] === 'CUTOFF') {

    $title = 'Internet Temporarily Disconnected';

    $message =
        'Your internet service has been temporarily disconnected because your account has an outstanding balance.';

    $alertType = 'danger';

    $icon = '⚠';

    $showPayButton = true;

}


/*
|--------------------------------------------------------------------------
| DUE DATE
|--------------------------------------------------------------------------
*/

else {

    if ($days > 0) {

        $title = 'Payment Reminder';

        $message =
            "Your payment is due in {$days} day(s). Please settle your account to avoid service interruption.";

        $alertType = 'warning';

        $icon = '⏰';

    }

    elseif ($days === 0) {

        $title = 'Payment Due Today';

        $message =
            'Your payment is due today. Please settle your account to avoid service interruption.';

        $alertType = 'danger';

        $icon = '⚠';

    }

    else {

        $overdue = abs($days);

        $title = 'Payment Overdue';

        $message =
            "Your payment is overdue by {$overdue} day(s). Please settle your account.";

        $alertType = 'danger';

        $icon = '⚠';

    }

}


/*
|--------------------------------------------------------------------------
| PAYMENT LINK
|--------------------------------------------------------------------------
*/

$paymentLink =
    $company['payment_url']
    . '?company='
    . urlencode($companyKey)
    . '&account='
    . urlencode($customer['account_no']);


/*
|--------------------------------------------------------------------------
| HTML
|--------------------------------------------------------------------------
*/

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>

<?= htmlspecialchars($title) ?>

-

<?= htmlspecialchars($company['name']) ?>

</title>


<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>


<style>

:root {

    --company-color:
        <?= htmlspecialchars(
            $company['primary_color']
        ) ?>;

}


body {

    background: #f4f6f9;

    min-height: 100vh;

}


.payment-card {

    max-width: 520px;

    margin: 50px auto;

    border: 0;

    border-radius: 20px;

    overflow: hidden;

    box-shadow:
        0 10px 35px
        rgba(0,0,0,.10);

}


.header {

    background:
        var(--company-color);

    color: white;

    text-align: center;

    padding: 30px 20px;

}


.company-name {

    font-size: 23px;

    font-weight: 700;

}


.company-info {

    font-size: 13px;

    opacity: .9;

    margin-top: 5px;

}


.status-icon {

    width: 65px;

    height: 65px;

    border-radius: 50%;

    background: white;

    color:
        var(--company-color);

    display: flex;

    align-items: center;

    justify-content: center;

    margin:
        0 auto 15px;

    font-size: 30px;

    font-weight: bold;

}


.customer-name {

    font-size: 21px;

    font-weight: 600;

}


.amount-label {

    color: #777;

    margin-top: 15px;

}


.amount {

    font-size: 40px;

    font-weight: 700;

    color:
        var(--company-color);

}


.info-row {

    display: flex;

    justify-content: space-between;

    gap: 15px;

    padding: 12px 0;

    border-bottom:
        1px solid #eee;

}


.info-row:last-child {

    border-bottom: 0;

}


.pay-btn {

    width: 100%;

    padding: 14px;

    border-radius: 10px;

    background:
        var(--company-color);

    border-color:
        var(--company-color);

    font-weight: 600;

    font-size: 17px;

}


.footer {

    text-align: center;

    color: #888;

    font-size: 12px;

    margin-top: 20px;

}

</style>

</head>


<body>


<div class="container">


<div class="card payment-card">


<!-- COMPANY -->

<div class="header">


<div class="status-icon">

<?= $icon ?>

</div>


<div class="company-name">

<?= htmlspecialchars(
    $company['name']
) ?>

</div>


<div class="company-info">

<?= htmlspecialchars(
    $company['address']
) ?>

</div>


<div class="company-info">

<?= htmlspecialchars(
    $company['contact']
) ?>

</div>


</div>


<div class="card-body p-4">


<!-- TITLE -->

<h4 class="text-center mb-3">

<?= htmlspecialchars($title) ?>

</h4>


<!-- CUSTOMER -->

<div class="text-center mb-4">


<div class="customer-name">

<?= htmlspecialchars(
    $customer['name']
) ?>

</div>


<div class="amount-label">

Outstanding Balance

</div>


<div class="amount">

<?= htmlspecialchars(
    $company['currency']
) ?>

<?= number_format(
    $customer['amount_due'],
    2
) ?>

</div>


</div>


<!-- MESSAGE -->

<div class="alert alert-<?= $alertType ?>">

<?= htmlspecialchars($message) ?>

</div>


<!-- ACCOUNT -->

<div class="mb-4">


<div class="info-row">

<span>Account</span>

<strong>

<?= htmlspecialchars(
    $customer['account_no']
) ?>

</strong>

</div>


<div class="info-row">

<span>Due Date</span>

<strong>

<?= date(
    'F d, Y',
    strtotime(
        $customer['due_date']
    )
) ?>

</strong>

</div>


<div class="info-row">

<span>Status</span>

<strong>

<?= htmlspecialchars(
    $customer['status']
) ?>

</strong>

</div>


<div class="info-row">

<span>Contact</span>

<strong>

<?= htmlspecialchars(
    $customer['contact']
) ?>

</strong>

</div>


</div>


<?php if ($showPayButton): ?>


<a
    href="<?= htmlspecialchars(
        $paymentLink
    ) ?>"
    class="btn btn-primary pay-btn"
>

Pay Now

</a>


<?php endif; ?>


<div class="footer">

<?= htmlspecialchars(
    $company['name']
) ?>

<br>

<?= htmlspecialchars(
    $company['email']
) ?>

</div>


</div>

</div>

</div>


</body>

</html>
