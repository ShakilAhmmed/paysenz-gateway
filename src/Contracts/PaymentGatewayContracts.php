<?php

namespace ShakilAhmmed\PaySenz\Contracts;
interface PaymentGatewayContracts
{
    /**
     * @return mixed
     */
    public static function retrieveToken();

    /**
     * @param $token
     * @param $post
     * @return mixed
     */
    public static function verifyPaymentRequest($token, $post);

    /**
     * @return mixed
     */
    public static function urls();

    /**
     * @return mixed
     */
    public static function info();

    /**
     * @param $orderId
     * @return mixed
     */
    public static function paymentRetry($orderId);

    /**
     * @return mixed
     */
    public static function baseUrl();

    /**
     * @return mixed
     */
    public static function successUrl();

    /**
     * @return mixed
     */
    public static function failUrl();

    /**
     * @return mixed
     */
    public static function cancelUrl();

    /**
     * @return mixed
     */
    public static function ipnUrl();

}