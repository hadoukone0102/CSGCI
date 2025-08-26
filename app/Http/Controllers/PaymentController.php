<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\CinetPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function test()
    {
        return view('cinetpay');
    }
    
    public function index(Request $request)
    {
        // Configuration CinetPay
        $apikey = '122534553368a882d40a5342.97773008';
        $site_id = '105905508';
        $customer_phone_number = '+2250748164960';

        // Validation des données
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|string|in:XOF,EUR,USD',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string'
        ]);
        
        // Récupération des données
        $firstName = trim($request->first_name);
        $lastName = trim($request->last_name);
        $phone = $this->formatPhone(trim($request->phone));
        $email = trim($request->email);
        $amount = floatval($request->amount);
        $currency = trim($request->currency);
        $description = trim($request->description ?? 'Paiement en ligne');
        $paymentMethod = $request->payment_method ?? 'ALL';
        
        // Génération de l'ID de transaction
        $transaction_id = 'TXN_' . date('YmdHis') . '_' . rand(1000, 9999);
        
        // Préparation des données au format JSON comme dans la documentation
        $formData = [
            "transaction_id" => $transaction_id,
            "amount" => $amount,
            "currency" => $currency,
            "alternative_currency" => "",
            "description" => $description,
            "customer_id" => "172",
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
            "channels" => "ALL",
            "metadata" => "user1",
            "lang" => "FR",
            "invoice_data" => [
                "Donnee1" => "",
                "Donnee2" => "",
                "Donnee3" => ""
            ]
        ];
        
        try {
            // Sauvegarder la transaction en base
            $payment = Payment::create([
                'transaction_id' => $transaction_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'api_data' => json_encode($formData)
            ]);
            
            // Appel à l'API CinetPay
            $CinetPay = new CinetPay($site_id, $apikey);
            $result = $CinetPay->generatePaymentLink($formData);
            
            // Log de la réponse
            Log::info('CinetPay API Response: ', $result);
            
            // Vérification de la réponse
            if (isset($result["code"]) && $result["code"] == '201' && isset($result["data"]["payment_url"])) {
                // Mettre à jour avec l'URL de paiement
                $payment->update([
                    'payment_url' => $result["data"]["payment_url"],
                    'api_response' => json_encode($result)
                ]);
                
                // Redirection vers CinetPay
                return redirect()->away($result["data"]["payment_url"]);
                
            } else {
                // Gestion des erreurs
                $payment->update([
                    'status' => 'failed',
                    'api_response' => json_encode($result)
                ]);
                
                $error_message = $result["message"] ?? "Erreur lors de la génération du lien de paiement";
                return back()->withErrors(['payment' => $error_message])->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur CinetPay: ' . $e->getMessage());
            
            if (isset($payment)) {
                $payment->update(['status' => 'error']);
            }
            
            return back()->withErrors(['exception' => 'Erreur technique: ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Formatage du numéro de téléphone au format international
     */
    private function formatPhone($phone)
    {
        // Nettoyer le numéro (garder seulement les chiffres et le +)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Si le numéro commence déjà par +225
        if (strpos($phone, '+225') === 0) {
            return $phone;
        }
        
        // Si le numéro commence par 225
        if (strpos($phone, '225') === 0) {
            return '+' . $phone;
        }
        
        // Si le numéro est local (commence par 0 ou directement les chiffres)
        if (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1); // Retirer le 0 initial
        }
        
        return '+225' . $phone;
    }
    
    /**
     * Méthode pour vérifier le statut d'une transaction
     */
    public function checkTransactionStatus($transaction_id)
    {
        $url = 'https://api-checkout.cinetpay.com/v2/payment/check';
        
        $payload = [
            "apikey" => $this->api_key,
            "site_id" => $this->site_id,
            "transaction_id" => $transaction_id
        ];
        
        $jsonData = json_encode($payload);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response && $httpCode == 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Webhook pour traiter les notifications CinetPay
     */
    public function handleWebhook($webhookData)
    {
        // Vérification de la signature (optionnel mais recommandé)
        if (isset($webhookData['cpm_trans_id'])) {
            $transaction_id = $webhookData['cpm_trans_id'];
            $status = $webhookData['cpm_result'] ?? null;
            
            // Mettre à jour le statut dans la base
            $payment = Payment::where('transaction_id', $transaction_id)->first();
            if ($payment) {
                $newStatus = ($status === '00') ? 'completed' : 'failed';
                $payment->update([
                    'status' => $newStatus,
                    'webhook_data' => json_encode($webhookData)
                ]);
                
                return $payment;
            }
        }
        
        return null;
    }
}