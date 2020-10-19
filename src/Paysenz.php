<?php


namespace ShakilAhmmed\PaySenz;

use ShakilAhmmed\PaySenz\Contracts\PaymentGatewayContracts;

class Paysenz implements PaymentGatewayContracts
{
    private static $config;

    /**
     * @param array $config
     */
    public static function init(array $config)
    {
        self::$config = $config;
    }

    /**
     * @return bool[]|mixed
     */
    public static function retrieveToken()
    {
        if (isset($_SESSION['paysenz_access_token'])) {
            return $_SESSION['paysenz_access_token'];
        } else {
            try {
                $requestParams = [
                    'grant_type' => self::$config['GRANT_TYPE'],
                    'client_id' => self::$config['CLIENT_ID'],
                    'client_secret' => self::$config['CLIENT_SECRET'],
                    'username' => self::$config['CLIENT_USER_NAME'],
                    'password' => self::$config['CLIENT_PASSWORD'],
                    'scope' => self::$config['CLIENT_SCOPE'],
                ];
                $payload = json_encode($requestParams);


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, self::$config['TOKEN_URL']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                // For certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                // Set HTTP Header for POST request
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($payload))
                );

                $response = curl_exec($ch);

                curl_close($ch);
                $content = json_decode((string)$response, true);
                $_SESSION['paysenz_access_token'] = $content['access_token'];
                return $content['access_token'];
            } catch (\Exception $e) {
                return array('error' => true);
            }
        }
    }

    /**
     * @param $token
     * @param $post
     * @return mixed
     */
    public static function verifyPaymentRequest($token, $post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$config['VERIFY_URL']);

        header('Content-Type: application/json');
        $post = json_encode($post); // Create JSON string from data ARRAY
        $authorization = "Authorization: Bearer " . $token; // **Prepare Autorisation Token**
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization)); // **Inject Token into Header**
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    /**
     * @return mixed|string[]
     */
    public static function urls(): array
    {
        return [
            'callback_success_url' => self::$config['BASE_URL'] . self::$config['SUCCESS_URL'],
            'callback_fail_url' => self::$config['BASE_URL'] . self::$config['FAIL_URL'],
            'callback_cancel_url' => self::$config['BASE_URL'] . self::$config['CANCEL_URL'],
            'callback_ipn_url' => self::$config['BASE_URL'] . self::$config['IPN_URL'],
        ];
    }

    /**
     * @return array|mixed
     */
    public static function info(): array
    {
        return [
            'grant_tyle' => self::$config['GRANT_TYPE'],
            'client_id' => self::$config['CLIENT_ID'],
            'client_secret' => self::$config['CLIENT_SECRET'],
            'client_user_name' => self::$config['CLIENT_USER_NAME'],
            'client_password' => self::$config['CLIENT_PASSWORD'],
            'client_scope' => self::$config['CLIENT_SCOPE'],
        ];
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public static function paymentRetry($orderId)
    {
        $requestParams = [
            'order_id' => $orderId,
            'client_id' => self::$config['CLIENT_ID'],
        ];
        $post = json_encode($requestParams);
        $ch = curl_init();
        $authorization = "Authorization: Bearer " . self::retrieveToken(); // **Prepare Autorisation Token**
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization)); // **Inject Token into Header**
        curl_setopt($ch, CURLOPT_URL, self::TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        // For certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    /**
     * @return string
     */

    public static function baseUrl(): string
    {
        return self::$config['BASE_URL'];
    }

    /**
     * @return string
     */
    public static function successUrl(): string
    {
        return self::baseUrl() . self::$config['SUCCESS_URL'];
    }

    /**
     * @return string
     */
    public static function failUrl(): string
    {
        return self::baseUrl() . self::$config['FAIL_URL'];
    }

    /**
     * @return string
     */
    public static function cancelUrl(): string
    {
        return self::baseUrl() . self::$config['CANCEL_URL'];
    }

    /**
     * @return string
     */
    public static function ipnUrl(): string
    {
        return self::baseUrl() . self::$config['IPN_URL'];
    }
}
