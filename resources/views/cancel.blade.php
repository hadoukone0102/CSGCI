<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement annulé</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #ff6b6b 0%, #c44569 100%); min-height: 100vh;">

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 p-4 text-center" style="max-width: 500px;">
            <div class="mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#dc3545" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                </svg>
            </div>
            <h1 class="fw-bold text-danger">Paiement annulé</h1>
            <p class="text-muted">
                Votre transaction <strong>{{ $transaction_id }}</strong> a été annulée.<br>
                Vous pouvez réessayer si vous le souhaitez.
            </p>
            <div class="mt-4 d-flex justify-content-center gap-2">
                <a href="{{ url('/') }}" class="btn btn-secondary btn-lg rounded-pill px-4">
                    Retour à l'accueil
                </a>
                <a href="{{ url('/formulaire-paiement') }}" class="btn btn-danger btn-lg rounded-pill px-4">
                    Réessayer le paiement
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
