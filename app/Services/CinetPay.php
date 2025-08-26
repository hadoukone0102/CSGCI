<?php

namespace App\Services;

class CinetPay
{
    private $site_id;
    private $api_key;
   
    public function __construct($site_id, $api_key) {
        $this->site_id = $site_id;
        $this->api_key = $api_key;
    }
   
    public function generatePaymentLink($data) {
        $url = 'https://api-checkout.cinetpay.com/v2/payment';
       
        // Préparer le payload au format JSON comme dans l'exemple
        $payload = [
            "apikey" => $this->api_key,
            "site_id" => $this->site_id,
            "transaction_id" => $data['transaction_id'] ?? 'TXN_' . time() . '_' . rand(1000, 9999),
            "amount" => $data['amount'] ?? 0,
            "currency" => $data['currency'] ?? 'XOF',
            "description" => $data['description'] ?? 'Paiement en ligne',
            "customer_id" => $data['customer_id'] ?? '1',
            "customer_name" => $data['customer_name'],
            "customer_surname" => $data['customer_surname'],
            "customer_email" => $data['customer_email'],
            "customer_phone_number" => $data['customer_phone_number'] ?? "+2250748164960",
            "customer_address" => $data['customer_address'] ?? 'Abidjan, Angré',
            "customer_city" => $data['customer_city'] ?? 'Abidjan',
            "customer_country" => $data['customer_country'] ?? 'CI',
            "customer_state" => $data['customer_state'] ?? 'CI',
            "customer_zip_code" => $data['customer_zip_code'] ?? '00225',
            "channels" => $data['channels'] ?? 'ALL',
            "metadata" => $data['metadata'] ?? 'User001',
            "lang" => $data['lang'] ?? 'FR',
            "notify_url" => $data['notify_url'],
            "return_url" => $data['return_url'],
        ];
       
        // Encodage en JSON
        $jsonData = json_encode($payload);
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // JSON au lieu de form-data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // JSON header
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
       
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
       
        // Log pour débogage
        error_log("CinetPay JSON Payload: " . $jsonData);
        error_log("CinetPay Response: " . $response);
        error_log("CinetPay HTTP Code: " . $httpCode);
       
        // Vérification des erreurs cURL
        if ($curlError) {
            return [
                'code' => '500',
                'message' => 'Erreur cURL: ' . $curlError,
                'data' => null
            ];
        }
       
        // Vérification de la réponse
        if ($response === false || empty($response)) {
            return [
                'code' => '500',
                'message' => 'Réponse vide de l\'API',
                'data' => null
            ];
        }
       
        // Vérification du code HTTP
        if ($httpCode !== 200) {
            return [
                'code' => '500',
                'message' => 'Erreur HTTP: ' . $httpCode . '. Réponse: ' . substr($response, 0, 500),
                'data' => null
            ];
        }
       
        $result = json_decode($response, true);
       
        // Vérification de la validité du JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'code' => '500',
                'message' => 'Réponse JSON invalide: ' . json_last_error_msg(),
                'data' => null,
                'raw_response' => $response
            ];
        }
       
        return $result;
    }
}