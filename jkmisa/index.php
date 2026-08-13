<?php
// payment_reminder.php

// Example customer data.
// Replace this with your database query.
$customer = [
    'account_no' => 'ACC001',
    'name'       => 'Juan Dela Cruz',
    'amount_due' => 999.00,
    'due_date'   => '2026-08-15',
    'contact'    => '09123456789',
    'status'     => 'UNPAID'
];

$today = new DateTime();
$dueDate = new DateTime($customer['due_date']);

$days = (int)$today->diff($dueDate)->format('%r%a');

if ($customer['status'] === 'PAID') {
    $message = 'Your account is already paid. Thank you!';
    $type = 'success';
} elseif ($days > 0) {
    $message = "Your payment is due in {$days} day(s).";
    $type = 'warning';
} elseif ($days === 0) {
    $message = 'Your payment is due today.';
    $type = 'danger';
} else {
    $overdue = abs($days);
    $message = "Your payment is overdue by {$overdue} day(s).";
    $type = 'danger';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment Reminder</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
        }

        .payment-card {
            max-width: 520px;
            margin: 60px auto;
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0,0,0,.10);
        }

        .payment-header {
            padding: 25px;
            text-align: center;
            background: #212529;
            color: white;
        }

        .amount {
            font-size: 36px;
            font-weight: 700;
        }

        .customer-name {
            font-size: 20px;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .pay-btn {
            width: 100%;
            padding: 13px;
            font-size: 17px;
            font-weight: 600;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card payment-card">

        <div class="payment-header">
            <h4>Payment Reminder</h4>
            <div class="mt-2">
                Account #<?= htmlspecialchars($customer['account_no']) ?>
            </div>
        </div>

        <div class="card-body p-4">

            <div class="text-center mb-4">

                <div class="customer-name">
                    <?= htmlspecialchars($customer['name']) ?>
                </div>

                <div class="text-muted mt-2">
                    Amount Due
                </div>

                <div class="amount">
                    ₱<?= number_format($customer['amount_due'], 2) ?>
                </div>

            </div>

            <div class="alert alert-<?= $type ?>">
                <?= htmlspecialchars($message) ?>
            </div>

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
                        <?= date('F d, Y', strtotime($customer['due_date'])) ?>
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

            <?php if ($customer['status'] !== 'PAID'): ?>

                <a
                    href="payment.php?account=<?= urlencode($customer['account_no']) ?>"
                    class="btn btn-primary pay-btn"
                >
                    Pay Now
                </a>

            <?php else: ?>

                <div class="alert alert-success text-center mb-0">
                    Payment received. Thank you!
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
