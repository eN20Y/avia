<?php

/*
|--------------------------------------------------------------------------
| PAYMENT REMINDER
|--------------------------------------------------------------------------
| Example:
|
| https://payder.vercel.app/api/jkmisa.php?jkmisa=fiber
|
| The "jkmisa" parameter selects the company.
|--------------------------------------------------------------------------
*/

$companyKey = strtolower(trim($_GET['jkmisa'] ?? ''));

/*
|--------------------------------------------------------------------------
| COMPANY CONFIGURATION
|--------------------------------------------------------------------------
*/

$companies = [

    'fiber' => [
        'name' => 'Jkmisa Fiber Internet',
        'short_name' => 'JKMISA FIBER',
        'logo' => '',
        'contact' => '09123456789',
        'email' => 'billing@jkmisa.com',
        'address' => 'Cebu City, Philippines',
        'currency' => '₱',
        'primary_color' => '#0d6efd'
    ],

    'abc' => [
        'name' => 'ABC Internet Services',
        'short_name' => 'ABC INTERNET',
        'logo' => '',
        'contact' => '09234567890',
        'email' => 'billing@abc.com',
        'address' => 'Cebu City, Philippines',
        'currency' => '₱',
        'primary_color' => '#198754'
    ],

    'xyz' => [
        'name' => 'XYZ Broadband',
        'short_name' => 'XYZ BROADBAND',
        'logo' => '',
        'contact' => '09345678901',
        'email' => 'billing@xyz.com',
        'address' => 'Cebu City, Philippines',
        'currency' => '₱',
        'primary_color' => '#6f42c1'
    ]

];

/*
|--------------------------------------------------------------------------
| CHECK COMPANY
|--------------------------------------------------------------------------
*/

if ($companyKey === '') {
    http_response_code(400);
    exit('Missing company parameter.');
}

if (!isset($companies[$companyKey])) {
    http_response_code(404);
    exit('Company not found.');
}

$company = $companies[$companyKey];

/*
|--------------------------------------------------------------------------
| CUSTOMER DATA
|--------------------------------------------------------------------------
|
| Replace this later with your database/API lookup.
|--------------------------------------------------------------------------
*/

$customer = [
    'account_no' => 'ACC001',
    'name'       => 'Juan Dela Cruz',
    'amount_due' => 999.00,
    'due_date'   => '2026-08-15',
    'contact'    => '09123456789',
    'status'     => 'UNPAID'
];

/*
|--------------------------------------------------------------------------
| PAYMENT STATUS
|--------------------------------------------------------------------------
*/

$today = new DateTime();
$dueDate = new DateTime($customer['due_date']);

$days = (int) $today->diff($dueDate)->format('%r%a');

if ($customer['status'] === 'PAID') {

    $message = 'Your account is already paid. Thank you!';
    $alertType = 'success';

} elseif ($days > 0) {

    $message = "Your payment is due in {$days} day(s).";
    $alertType = 'warning';

} elseif ($days === 0) {

    $message = 'Your payment is due today.';
    $alertType = 'danger';

} else {

    $overdue = abs($days);

    $message = "Your payment is overdue by {$overdue} day(s).";
    $alertType = 'danger';
}

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
        Payment Reminder - <?= htmlspecialchars($company['name']) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        :root {
            --company-color: <?= htmlspecialchars($company['primary_color']) ?>;
        }

        body {
            background: #f5f7fb;
            min-height: 100vh;
        }

        .payment-card {
            max-width: 520px;
            margin: 50px auto;
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0,0,0,.10);
        }

        .payment-header {
            padding: 28px 20px;
            text-align: center;
            background: var(--company-color);
            color: white;
        }

        .company-name {
            font-size: 23px;
            font-weight: 700;
        }

        .company-contact {
            font-size: 13px;
            opacity: .9;
            margin-top: 5px;
        }

        .amount {
            font-size: 38px;
            font-weight: 700;
            color: var(--company-color);
        }

        .customer-name {
            font-size: 21px;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .pay-btn {
            width: 100%;
            padding: 14px;
            font-size: 17px;
            font-weight: 600;
            border-radius: 10px;
            background: var(--company-color);
            border-color: var(--company-color);
        }

        .pay-btn:hover {
            opacity: .9;
        }

        .footer {
            text-align: center;
            color: #888;
            font-size: 13px;
            margin-top: 20px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card payment-card">

        <!-- COMPANY HEADER -->

        <div class="payment-header">

            <div class="company-name">

                <?= htmlspecialchars($company['name']) ?>

            </div>

            <div class="company-contact">

                <?= htmlspecialchars($company['address']) ?>

            </div>

            <div class="company-contact">

                <?= htmlspecialchars($company['contact']) ?>

            </div>

        </div>


        <div class="card-body p-4">

            <!-- CUSTOMER -->

            <div class="text-center mb-4">

                <div class="customer-name">

                    <?= htmlspecialchars($customer['name']) ?>

                </div>

                <div class="text-muted mt-2">

                    Amount Due

                </div>

                <div class="amount">

                    <?= htmlspecialchars($company['currency']) ?>

                    <?= number_format($customer['amount_due'], 2) ?>

                </div>

            </div>


            <!-- STATUS -->

            <div class="alert alert-<?= $alertType ?>">

                <?= htmlspecialchars($message) ?>

            </div>


            <!-- ACCOUNT INFORMATION -->

            <div class="mb-4">

                <div class="info-row">

                    <span>Account</span>

                    <strong>

                        <?= htmlspecialchars($customer['account_no']) ?>

                    </strong>

                </div>


                <div class="info-row">

                    <span>Due Date</span>

                    <strong>

                        <?= date(
                            'F d, Y',
                            strtotime($customer['due_date'])
                        ) ?>

                    </strong>

                </div>


                <div class="info-row">

                    <span>Status</span>

                    <strong>

                        <?= htmlspecialchars($customer['status']) ?>

                    </strong>

                </div>


                <div class="info-row">

                    <span>Contact</span>

                    <strong>

                        <?= htmlspecialchars($customer['contact']) ?>

                    </strong>

                </div>

            </div>


            <!-- PAYMENT -->

            <?php if ($customer['status'] !== 'PAID'): ?>

                <a
                    href="payment.php?account=<?= urlencode($customer['account_no']) ?>&company=<?= urlencode($companyKey) ?>"
                    class="btn btn-primary pay-btn"
                >

                    Pay Now

                </a>

            <?php else: ?>

                <div class="alert alert-success text-center mb-0">

                    Payment received. Thank you!

                </div>

            <?php endif; ?>


            <div class="footer">

                <?= htmlspecialchars($company['name']) ?>

                <br>

                <?= htmlspecialchars($company['email']) ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>
