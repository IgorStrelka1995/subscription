<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionPlanRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use App\Http\Resources\SubscriptionPlanCollection;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use OpenApi\Attributes as OA;

class SubscriptionPlanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/subscription_plan",
     *     summary="List of all subscription plans",
     *     tags={"Subscription Plan"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by subscription plan name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[price]",
     *         in="query",
     *         description="Filter by subscription plan price",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[description]",
     *         in="query",
     *         description="Filter by subscription plan description",
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="price", type="integer"),
     *                  @OA\Property(property="duration", type="integer"),
     *                  @OA\Property(property="created_at", type="string"),
     *              )),
     *              example={
     *                  "data": {{
     *                      "id": 1,
     *                      "name": "Light",
     *                      "description": "Voluptatem est aliquam enim eum soluta perferendis fugit.",
     *                      "price": "100",
     *                      "duration": 90,
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  },
     *                  {
     *                      "id": 2,
     *                      "name": "Optimal",
     *                      "description": "Rerum eius id voluptatem dolores.",
     *                      "price": "500",
     *                      "duration": 180,
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  },
     *                  {
     *                      "id": 3,
     *                      "name": "Maximal",
     *                      "description": "Temporibus qui qui assumenda itaque placeat.",
     *                      "price": "1000",
     *                      "duration": 365,
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  }}
     *              }
     *         )
     *     )
     * )
     *
     * @return SubscriptionPlanCollection
     */
    public function index()
    {
        $subscriptionPlans = QueryBuilder::for(SubscriptionPlan::class)
            ->allowedFilters([
                AllowedFilter::exact('name'),
                AllowedFilter::exact('price'),
                'description'
            ])->get();

        return new SubscriptionPlanCollection($subscriptionPlans);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/subscription_plan",
     *     summary="Create new subscription plan.",
     *     tags={"Subscription Plan"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string"),
     *                      @OA\Property(property="price", type="string"),
     *                      @OA\Property(property="duration", type="integer"),
     *                 )
     *             },
     *              example={
     *                  "name": "Maximal+",
     *                  "description": "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
     *                  "price": "200.00",
     *                  "duration": 545
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=4),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="price", type="string"),
     *                  @OA\Property(property="duration", type="integer"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param StoreSubscriptionPlanRequest $request
     * @return SubscriptionPlanResource
     */
    public function store(StoreSubscriptionPlanRequest $request)
    {
        $data = $request->only(['name', 'description', 'price', 'duration']);

        $subscriptionPlan = SubscriptionPlan::create($data);

        return new SubscriptionPlanResource($subscriptionPlan);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/subscription_plan/{subscription_plan}",
     *     summary="Get single subscription plan",
     *     tags={"Subscription Plan"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Subscription plan id",
     *          in="path",
     *          name="subscription_plan",
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
     *                  @OA\Property(property="name", type="string", example="Light"),
     *                  @OA\Property(property="description", type="string", example="Voluptatem est aliquam enim eum soluta perferendis fugit."),
     *                  @OA\Property(property="price", type="integer", example="100"),
     *                  @OA\Property(property="duration", type="integer", example=90),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param SubscriptionPlan $subscriptionPlan
     * @return SubscriptionPlanResource
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        return new SubscriptionPlanResource($subscriptionPlan);
    }

    /**
     *  @OA\Put(
     *     path="/api/v1/subscription_plan/{subscription_plan}",
     *     summary="Update subscription plan",
     *     tags={"Subscription Plan"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Subscription plan id",
     *          in="path",
     *          name="subscription_plan",
     *          required=true,
     *          example=3
     *     ),
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string"),
     *                      @OA\Property(property="price", type="string"),
     *                      @OA\Property(property="duration", type="integer"),
     *                 )
     *             },
     *              example={
     *                  "name": "Maximal+",
     *                  "description": "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
     *                  "price": "200.00",
     *                  "duration": 545
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=3),
     *                  @OA\Property(property="name", type="string", example="Maximal+"),
     *                  @OA\Property(property="description", type="string", example="Voluptatum sequi odio sint dolorem consectetur nihil quasi."),
     *                  @OA\Property(property="price", type="string", example="200.00"),
     *                  @OA\Property(property="duration", type="integer", example=545),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z"),
     *              )
     *         )
     *     )
     * )
     *
     * @param UpdateSubscriptionPlanRequest $request
     * @param SubscriptionPlan $subscriptionPlan
     * @return SubscriptionPlanResource
     */
    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan)
    {
        $data = $request->only(['name', 'description', 'price', 'duration']);

        $subscriptionPlan->update($data);

        return new SubscriptionPlanResource($subscriptionPlan);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/subscription_plan/{subscription_plan}",
     *     summary="Delete subscription plan",
     *     tags={"Subscription Plan"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Subscription plan id",
     *          in="path",
     *          name="subscription_plan",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="OK"
     *     )
     * )
     *
     * @param SubscriptionPlan $subscriptionPlan
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
