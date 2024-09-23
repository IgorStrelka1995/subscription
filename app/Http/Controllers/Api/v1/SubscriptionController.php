<?php

namespace App\Http\Controllers\Api\v1;

use App\Facades\SubscriptionFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Resources\SubscriptionCollection;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::where('user_id', $request->user()->id)->get();

        return new SubscriptionCollection($subscriptions);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/subscription/subscribe",
     *     summary="Subscribe",
     *     tags={"Subscription"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="user_id", type="string"),
     *                      @OA\Property(property="plan_id", type="string"),
     *                 )
     *             },
     *              example={
     *                  "user_id": "1",
     *                  "plan_id": "1"
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
     *                  @OA\Property(property="plan_id", type="string", example="1"),
     *                  @OA\Property(property="start_date", type="string", example="2024-06-01 00:00:00"),
     *                  @OA\Property(property="end_date", type="string", example="2024-08-30 00:00:00"),
     *                  @OA\Property(property="status", type="string", example="active"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param StoreSubscriptionRequest $request
     * @return SubscriptionResource
     */
    public function subscribe(StoreSubscriptionRequest $request): SubscriptionResource
    {
        $data = $request->only(['user_id', 'plan_id']);

        $subscriptionData = SubscriptionFacade::generateSubscriptionData($data);

        $subscription = Subscription::create($subscriptionData);

        return new SubscriptionResource($subscription);
    }

    /**
     *  @OA\Put(
     *     path="/api/v1/subscription/cancel/{subscription}",
     *     summary="Cancel subscription",
     *     tags={"Subscription"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Subscription id",
     *          in="path",
     *          name="subscription",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="user_id", type="string", example="1"),
     *                  @OA\Property(property="plan_id", type="string", example="1"),
     *                  @OA\Property(property="start_date", type="string", example="2024-06-01 00:00:00"),
     *                  @OA\Property(property="end_date", type="string", example="2024-08-30 00:00:00"),
     *                  @OA\Property(property="status", type="string", example="cancelled"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Subscription $subscription
     * @return SubscriptionResource
     */
    public function cancel(Request $request, Subscription $subscription): SubscriptionResource
    {
        $this->authorize('cancel', $subscription);

        $subscription->status = Subscription::SUBSCRIPTION_STATUS_CANCELLED;

        $subscription->update();

        return new SubscriptionResource($subscription);
    }

    /**
     *  @OA\Put(
     *     path="/api/v1/subscription/prolongation/{subscription}",
     *     summary="Prolongation subscription",
     *     tags={"Subscription"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Subscription id",
     *          in="path",
     *          name="subscription",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="user_id", type="string", example="1"),
     *                  @OA\Property(property="plan_id", type="string", example="1"),
     *                  @OA\Property(property="start_date", type="string", example="2024-06-01 00:00:00"),
     *                  @OA\Property(property="end_date", type="string", example="2024-08-30 00:00:00"),
     *                  @OA\Property(property="status", type="string", example="active"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Subscription $subscription
     * @return SubscriptionResource
     */
    public function prolongation(Request $request, Subscription $subscription): SubscriptionResource
    {
        $this->authorize('prolongation', $subscription);

        $subscriptionData = SubscriptionFacade::prolongationSubscription($subscription);

        $subscription->update($subscriptionData);

        return new SubscriptionResource($subscription);
    }
}
