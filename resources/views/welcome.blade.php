<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement CinetPay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .payment-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 0;
            overflow: hidden;
            margin: 50px auto;
            max-width: 1000px;
        }
        
        .left-section {
            background: white;
            padding: 40px;
            border-right: 1px solid #e9ecef;
        }
        
        .right-section {
            background: #f8f9fa;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-payment {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-payment:disabled {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }
        
        .cinetpay-logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .required {
            color: #dc3545;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
        }
        
        .currency-select {
            border-radius: 10px;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .payment-methods {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .payment-method {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            text-align: center;
        }
        
        .payment-method:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .privacy-text {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.5;
            margin: 20px 0;
        }
        
        .form-section {
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <div class="row g-0">
                <!-- Section gauche - Informations personnelles -->
                <div class="col-lg-7 left-section">
                    <h2 class="mb-4">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Informations personnelles
                    </h2>
                    
                    <form id="paymentForm" method="POST" action="{{ route('process_payment') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-section">
                                <label class="form-label">Prénom <span class="required">*</span></label>
                                <input type="text" class="form-control" name="first_name" id="firstName" required>
                            </div>
                            <div class="col-md-6 form-section">
                                <label class="form-label">Nom <span class="required">*</span></label>
                                <input type="text" class="form-control" name="last_name" id="lastName" required>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <label class="form-label">Téléphone <span class="required">*</span></label>
                            <input type="tel" class="form-control" name="phone" id="phone" placeholder="+225 XX XX XX XX" required>
                        </div>
                        
                        <div class="form-section">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        
                        <div class="form-section">
                            <label class="form-label">Montant <span class="required">*</span></label>
                            <div class="row">
                                <div class="col-8">
                                    <input type="number" class="form-control" name="amount" id="amount" min="100" step="1" placeholder="Saisir le montant" required>
                                </div>
                                <div class="col-4">
                                    <select class="form-control currency-select" name="currency" id="currency">
                                        <option value="XOF">XOF</option>
                                        <!-- <option value="EUR">EUR</option>
                                        <option value="USD">USD</option> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <label class="form-label">Description du paiement</label>
                            <input type="text" class="form-control" name="description" id="description" placeholder="Ex: Achat produit, Service..." value="Paiement en ligne">
                        </div>
                    </form>
                </div>
                
                <!-- Section droite - Récapitulatif et paiement -->
                <div class="col-lg-5 right-section">
                    <div class="text-center">
                        <div class="cinetpay-logo mb-4">
                            <h3 class="text-primary">
                                <i class="fas fa-credit-card me-2"></i>
                                Payer avec <span style="color: #FF6B35;">CinetPay</span>
                            </h3>
                        </div>
                        
                        <div class="payment-summary">
                            <p class="mb-2">Veuillez suivre les étapes ci-dessous afin de finaliser votre paiement. Ce système est mis en place par CSGCI.</p>
                            
                            <div class="total-amount">
                                Total: <span id="displayAmount">0</span> <span id="displayCurrency">XOF</span>
                            </div>
                            
                            <div class="payment-methods">
                                <div class="payment-method">
                                    <i class="fas fa-mobile-alt text-success"></i><br>
                                    Mobile Money
                                </div>
                                <div class="payment-method">
                                    <i class="fas fa-credit-card text-primary"></i><br>
                                    Carte bancaire
                                </div>
                                <div class="payment-method">
                                    <i class="fas fa-wave text-warning"></i><br>
                                    Virement
                                </div>
                            </div>
                            
                            <div class="privacy-text">
                                Vos données personnelles seront utilisées pour traiter votre commande, soutenir votre expérience sur ce site Web et à d'autres fins décrites dans notre politique de confidentialité.
                            </div>
                            
                            <button type="button" class="btn btn-payment w-100" id="placeOrderBtn" disabled>
                                <span class="loading-spinner">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                </span>
                                <span class="btn-text">
                                    <i class="fas fa-lock me-2"></i>
                                    Payer maintenant
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mise à jour en temps réel du montant
        const amountInput = document.getElementById('amount');
        const currencySelect = document.getElementById('currency');
        const displayAmount = document.getElementById('displayAmount');
        const displayCurrency = document.getElementById('displayCurrency');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const form = document.getElementById('paymentForm');
        
        function updateDisplay() {
            const amount = amountInput.value || '0';
            const currency = currencySelect.value;
            displayAmount.textContent = amount;
            displayCurrency.textContent = currency;
            
            // Activer/désactiver le bouton
            checkFormValid();
        }
        
        function checkFormValid() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;
            const amount = parseFloat(amountInput.value);
            
            const isValid = firstName && lastName && phone && email && amount >= 100;
            placeOrderBtn.disabled = !isValid;
        }
        
        // Événements
        amountInput.addEventListener('input', updateDisplay);
        currencySelect.addEventListener('change', updateDisplay);
        
        // Validation en temps réel
        ['firstName', 'lastName', 'phone', 'email'].forEach(id => {
            document.getElementById(id).addEventListener('input', checkFormValid);
        });
        
        // Formatage du numéro de téléphone
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('225')) {
                value = '+' + value;
            } else if (value.length > 0 && !value.startsWith('+')) {
                value = '+225' + value;
            }
            e.target.value = value;
        });
        
        // Soumission du formulaire
        placeOrderBtn.addEventListener('click', function() {
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Animation de chargement
            const spinner = document.querySelector('.loading-spinner');
            const btnText = document.querySelector('.btn-text');
            
            spinner.style.display = 'inline-block';
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
            placeOrderBtn.disabled = true;
            
            // Soumission après animation
            setTimeout(() => {
                form.submit();
            }, 1000);
        });
        
        // Animation des méthodes de paiement
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    </script>
    
    <style>
        .payment-method.selected {
            border-color: #667eea !important;
            background: #f8f9ff !important;
            transform: scale(1.05);
        }
        
        @media (max-width: 768px) {
            .payment-container {
                margin: 20px;
            }
            .left-section, .right-section {
                padding: 20px;
            }
        }
    </style>
</body>
</html>