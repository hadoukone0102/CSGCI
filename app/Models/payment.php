<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Nom de la table (facultatif si le pluriel "payments" est utilisé)
     */
    protected $table = 'payments';

    /**
     * Champs qui peuvent être remplis en masse
     */
    protected $fillable = [
        'transaction_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'amount',
        'currency',
        'description',
        'status',        // ex: pending, success, failed
        'payment_url',   // lien généré par CinetPay
        'response_data', // JSON brut de l’API
    ];

    /**
     * Casts pour certains champs
     */
    protected $casts = [
        'amount' => 'float',
        'response_data' => 'array',
    ];
}
