<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\PaypalPaymentProceed;
use App\Events\StripePaymentProceed;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaypalPayment;
use App\Http\Requests\StoreStripePayment;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/payment/stripe",
     *     summary="Payment",
     *     tags={"Payment"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="user_id", type="string"),
     *                      @OA\Property(property="subscription_id", type="string"),
     *                 )
     *             },
     *              example={
     *                  "user_id": "1",
     *                  "subscription_id": "1"
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="user_id", type="string", example="1"),
     *                  @OA\Property(property="subscription_id", type="string", example="1"),
     *                  @OA\Property(property="amount", type="string", example="100.00"),
     *                  @OA\Property(property="payment_method", type="string", example="stripe"),
     *                  @OA\Property(property="payment_status", type="string", example="pending"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param StoreStripePayment $request
     * @return PaymentResource
     */
    public function stripe(StoreStripePayment $request)
    {
        $paymentData = $request->only(['user_id', 'subscription_id']);

        $subscription = Subscription::find($paymentData['subscription_id']);

        $amount = $subscription->plan->price;

        $payment = Payment::create([
            'user_id' => $paymentData['user_id'],
            'subscription_id' => $paymentData['subscription_id'],
            'amount' => $amount,
            'payment_method' => Payment::STRIPE_PAYMENT_METHOD,
            'payment_status' => Payment::PENDING_PAYMENT_METHOD
        ]);

        event(new StripePaymentProceed());

        return new PaymentResource($payment);
    }

    /**
     * * @OA\Post(
     *     path="/api/v1/payment/paypal",
     *     summary="Payment",
     *     tags={"Payment"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="user_id", type="string"),
     *                      @OA\Property(property="subscription_id", type="string"),
     *                 )
     *             },
     *              example={
     *                  "user_id": "1",
     *                  "subscription_id": "1"
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="user_id", type="string", example="1"),
     *                  @OA\Property(property="subscription_id", type="string", example="1"),
     *                  @OA\Property(property="amount", type="string", example="100.00"),
     *                  @OA\Property(property="payment_method", type="string", example="paypal"),
     *                  @OA\Property(property="payment_status", type="string", example="pending"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param StorePaypalPayment $request
     * @return PaymentResource
     */
    public function paypal(StorePaypalPayment $request)
    {
        $paymentData = $request->only(['user_id', 'subscription_id']);

        $subscription = Subscription::find($paymentData['subscription_id']);

        $amount = $subscription->plan->price;

        $payment = Payment::create([
            'user_id' => $paymentData['user_id'],
            'subscription_id' => $paymentData['subscription_id'],
            'amount' => $amount,
            'payment_method' => Payment::PAYPAL_PAYMENT_METHOD,
            'payment_status' => Payment::PENDING_PAYMENT_METHOD
        ]);

        event(new PaypalPaymentProceed());

        return new PaymentResource($payment);
    }
}
