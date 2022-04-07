<?php

namespace App\Services\GeoPay;

class GeoPay {

    protected $key;
    protected $secret;
    protected $url = 'https://partners.geo-pay.net';

    public function __construct($key, $secret) {
        $this->key = $key;
        $this->secret = $secret;
    }

    protected function query($method, $data = array()) {
        $data['api_key'] = $this->key;
        $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        $fields['data'] = $data;
        $sig = $this->signPack($data);
        $fields['sig'] = $sig;
        $ch = curl_init($this->url . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-MBX-APIKEY: ' . $this->key,
//            'sig:' .  $sig,
            'Content-type: application/json'
        ]);
        $result =  curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    protected function getQuery($method, $data = array()){
        $data['api_key'] = $this->key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $method . '?' . http_build_query($data) );
        $sig = $this->signPack($method . '?' . http_build_query($data) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-MBX-APIKEY: ' . $this->key,
            'Content-type: application/json',
            'sig: ' . $sig
        ]);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function balance($equivalent) {
        $data = array(
            'equivalent' => $equivalent
        );
        $return = $this->query('/api/v3/account/user/balance/', $data);
        $return = json_decode($return, true);
        $data = json_decode($return['data'], true);
        return $data['balance'];
    }

    public function order($equivalent, $amount, $cardNumber, $partnerTransactionId = 0) {//выплата на карту
        if(!$this->is_valid_credit_card($cardNumber)){
            return ['status'=>'error', 'error'=>'Invalid card Number'];
        }
        if($partnerTransactionId == 0){
            $partnerTransactionId = time() . 'Kit';
        }
        $data = array(
            'equivalent' => $equivalent,
            'amount' => $amount,
            'card_number' => $cardNumber,
            'partner_transaction_id' => $partnerTransactionId
        );
        $return = $this->query('/api/v3/payment-systems/withdraw/', $data);
        $return = json_decode($return, true);
        if($return['code'] == 0){
            $data = json_decode($return['data'], true);
            return ['status'=>'ok', 'transaction_id'=>$data['transaction_uuid'] ];
        }else{
            return ['status'=>'error', 'error_code'=>$return['code']];
        }
    }

    public function validate($equivalent, $amount, $cardNumber, $partnerTransactionId = 0) {//Проверить данные на валидность перед отправкой
        if($partnerTransactionId == 0){
            $partnerTransactionId = time() . 'Kit';
        }
        $data = array(
            'equivalent' => $equivalent,
            'amount' => $amount,
            'card_number' => $cardNumber,
            'partner_transaction_id' => $partnerTransactionId
        );
        $return = $this->query('/api/v3/payment-systems/withdraw/validate/', $data);
        $return = json_decode($return, true);
        $data = json_decode($return['data'], true);
        return $data;
    }

    public function checkOrder($transactionId, $isPartnerId = true) {//Проверить данные на валидность перед отправкой
        if($isPartnerId == true){
            $data = array(
                'partner_transaction_id' => $transactionId
            );
        }else{
            $data = array(
                'transaction_uuid' => $transactionId
            );
        }

        $return = $this->query('/api/v3/payment-systems/withdraw/status/', $data);
        $return = json_decode($return, true);
        if($return['code'] == 0){
            $data = json_decode($return['data'], true);
            return $data['status'];
        }else{
            return false;
        }
    }

    public function report($equivalent, $dateFrom, $dateTo, $start = 0, $end = 10) {//Проверить данные на валидность перед отправкой
        $data['equivalent'] = $equivalent;
        $data['start'] = $start;
        $data['end'] = $end;
        $data['date_from'] = $dateFrom;
        $data['date_to  '] = $dateTo;

        $return = $this->getQuery('/api/v3/account/history/payments/', $data);
        $return = json_decode($return, true);
        if($return['code'] == 0){
            $data = json_decode($return['data'], true);
            return $data['transactions'];
        }else{
            return false;
        }
    }

    function signPack($data)
    {
        $signature = '';
        $keyId = openssl_get_privatekey($this->secret, '');
        $res = openssl_sign($data, $signature, $this->secret, 'SHA256');
        openssl_free_key($keyId);
        if (!$res) {
            throw new Exception("Sign error!");
        }
        return base64_encode($signature);
    }

    function is_valid_credit_card($s)
    {
        $s = strrev(preg_replace('/[^\d]/','',$s));

        $sum = 0;
        for ($i = 0, $j = strlen($s); $i < $j; $i++) {
            if (($i % 2) == 0) {
                $val = $s[$i];
            } else {
                $val = $s[$i] * 2;
                if ($val > 9)  $val -= 9;
            }
            $sum += $val;
        }


        return (($sum % 10) == 0);
    }

    //

}
