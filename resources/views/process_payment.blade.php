<?php
// process_payment.php
require_once 'app/Services/CinetPay.php'; // Ajustez le chemin selon votre structure
use App\Models\Payment;

// Configuration CinetPay
$apikey = '122534553368a882d40a5342.97773008';
$site_id = '105905508';
$customer_phone_number = '+2250748164960';

// Validation des données POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Méthode non autorisée');
}

// Récupération et validation des données
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);
$currency = trim($_POST['currency'] ?? 'XOF');
$description = trim($_POST['description'] ?? 'Paiement en ligne');

// Validation des champs requis
$errors = [];
if (empty($firstName)) $errors[] = 'Le prénom est requis';
if (empty($lastName)) $errors[] = 'Le nom est requis';
if (empty($phone)) $errors[] = 'Le téléphone est requis';
if (empty($email)) $errors[] = 'L\'email est requis';
if ($amount < 100) $errors[] = 'Le montant minimum est de 100';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';

if (!empty($errors)) {
    die('Erreurs de validation: ' . implode(', ', $errors));
}

// Préparation des données pour CinetPay
$transaction_id = 'TXN_' . date('YmdHis') . '_' . rand(1000, 9999);
 $formData = [
    "transaction_id" => $transaction_id,
    "amount" => $amount,
    "currency" => $currency,
    "alternative_currency" => "",
    "description" => $description,
    "customer_id" => "1",
    "customer_name" => $firstName,
    "customer_surname" => $lastName,
    "customer_email" => $email,
    "customer_phone_number" => $customer_phone_number,
    "customer_address" => "Abidjan, Angré",
    "customer_city" => "Abidjan",
    "customer_country" => "CI", // Code pays Côte d'Ivoire
    "customer_state" => "CI",
    "customer_zip_code" => "00225",
    "notify_url" => url("/webhook"),
    "return_url" => url("/success?txn=" . $transaction_id),
    "channels" => "ALL", // Pour avoir toutes les méthodes de paiement
    "metadata" => "user1",
    "lang" => "FR",
    "invoice_data" => [
        "Donnee1" => "",
        "Donnee2" => "",
        "Donnee3" => ""
    ]
];

// Initialisation du service CinetPay
$CinetPay = new \App\Services\CinetPay($site_id, $apikey);
$result = $CinetPay->generatePaymentLink($formData);

// Traitement de la réponse
if (isset($result["code"]) && $result["code"] == '201' && isset($result["data"]["payment_url"])) {
    // Redirection automatique vers le paiement
    header('Location: ' . $result["data"]["payment_url"]);
    
    $payment = Payment::create([
        'transaction_id' => $transaction_id,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'phone' => $phone,
        'email' => $email,
        'amount' => $amount,
        'currency' => $currency,
        'description' => $description,
        'status' => 'pending',
        'payment_url' => $result["data"]["payment_url"] ?? null,
        'response_data' => $result ?? [],
    ]);
    exit;
} else {
    // Affichage de la page d'erreur avec fallback
    $error_message = $result["message"] ?? "Erreur lors de la génération du lien de paiement";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traitement du paiement - CinetPay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-result {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="payment-result">
        <?php if (isset($result["data"]["payment_url"])): ?>
            <div class="text-success mb-4">
                <i class="fas fa-check-circle fa-4x"></i>
            </div>
            <h3 class="text-success mb-3">Redirection vers le paiement...</h3>
            <p>Vous allez être redirigé vers la plateforme de paiement CinetPay.</p>
            <p class="text-muted">Si la redirection ne fonctionne pas, cliquez sur le bouton ci-dessous :</p>
            <a href="<?php echo htmlspecialchars($result["data"]["payment_url"]); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-credit-card me-2"></i>Procéder au paiement
            </a>
            <script>
                setTimeout(function() {
                    window.location.href = "<?php echo addslashes($result["data"]["payment_url"]); ?>";
                }, 3000);
            </script>
        <?php else: ?>
            <div class="text-danger mb-4">
                <i class="fas fa-exclamation-triangle fa-4x"></i>
            </div>
            <h3 class="text-danger mb-3">Erreur de paiement</h3>
            <div class="alert alert-danger">
                <strong>Erreur:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
            
            <!-- Informations de debug -->
            <div class="accordion" id="debugAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#debugInfo">
                            Informations de débogage
                        </button>
                    </h2>
                    <div id="debugInfo" class="accordion-collapse collapse">
                        <div class="accordion-body text-start">
                            <h6>Données envoyées:</h6>
                            <pre><?php echo htmlspecialchars(print_r($formData, true)); ?></pre>
                            
                            <h6>Réponse API:</h6>
                            <pre><?php echo htmlspecialchars(print_r($result, true)); ?></pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour au formulaire
                </a>
                <button onclick="location.reload()" class="btn btn-primary ms-2">
                    <i class="fas fa-redo me-2"></i>Réessayer
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>