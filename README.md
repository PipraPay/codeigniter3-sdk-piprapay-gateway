# PipraPay SDK for CodeIgniter 3

A lightweight CodeIgniter 3 SDK for integrating [PipraPay](https://piprapay.com) payment gateway.  
Supports charge creation, payment verification, and webhook/IPN handling.

---

## ✅ Features

- Create charge/payment requests
- Verify payments
- Handle IPN/webhook securely
- Supports multiple currencies (BDT, USD, etc.)
- Works with sandbox and live base URLs

---

## 📁 Installation

1. **Download or clone this repo.**
2. Copy the file `PipraPay.php` into your CodeIgniter `application/libraries/` directory.

---

## ⚙️ Configuration

In your controller, load the library like this:

```php
$this->load->library('PipraPay', [
    'api_key' => 'YOUR_API_KEY',
    'base_url' => 'https://sandbox.piprapay.com', // or live URL
    'currency' => 'BDT' // or USD
]);

Alternatively, use setCredentials() method:
$this->piprapay->setCredentials('YOUR_API_KEY', 'https://sandbox.piprapay.com', 'BDT');

---

##💳 Create Charge (Payment Request)

$response = $this->piprapay->createCharge([
    'full_name' => 'John Doe',
    'email_mobile' => 'john@example.com',
    'amount' => 50,
    'metadata' => ['invoiceid' => 'INV-123'],
    'redirect_url' => 'https://your-site.com/payment-success',
    'cancel_url' => 'https://your-site.com/payment-cancel',
    'webhook_url' => 'https://your-site.com/webhook-handler'
]);

if ($response['status']) {
    redirect($response['pp_url']); // Redirect user to PipraPay checkout
} else {
    echo 'Error: ' . ($response['error'] ?? 'Unknown error');
}

---

##✅ Verify Payment (After Webhook or Manual)

$verify = $this->piprapay->verifyPayment('181055228'); // pp_id from webhook or response
if ($verify['status']) {
    // process $verify['status'], $verify['transaction_id'], etc.
}

---

##🔔 Handle Webhook (IPN)

$this->load->library('PipraPay');
$result = $this->piprapay->handleWebhook('YOUR_API_KEY');

if ($result['status']) {
    $data = $result['data'];
    // Save transaction, update order, etc.
} else {
    log_message('error', 'Invalid PipraPay webhook access.');
    show_error('Unauthorized', 401);
}

---

##📦 Example Response

{
  "pp_id": "181055228",
  "customer_name": "Demo",
  "customer_email_mobile": "demo@gmail.com",
  "payment_method": "bKash Personal",
  "amount": "10",
  "currency": "BDT",
  "metadata": {
    "invoiceid": "abc123"
  },
  "transaction_id": "abcxyz123",
  "status": "completed"
}
