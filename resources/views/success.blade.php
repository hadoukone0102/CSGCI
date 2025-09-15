<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement réussi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="max-width: 500px;">
            <div class="mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06l2.6 2.6a.75.75 0 0 0 1.08-.02l3.9-4.95a.75.75 0 0 0-.02-1.08z"/>
                </svg>
            </div>
            <h1 class="fw-bold text-success">Paiement réussi</h1>
            <p class="text-muted">
                Merci pour votre confiance !<br>
                Votre transaction <strong>{{ $transaction_id }}</strong> a été traitée avec succès.
            </p>
            <div class="mt-4">
                <a href="{{ url('https://csgci.com/') }}" class="btn btn-success btn-lg rounded-pill px-4">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optionnel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
