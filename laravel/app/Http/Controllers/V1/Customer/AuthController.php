<?php

namespace App\Http\Controllers\V1\Customer;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/v1/customer/auth/register",
     *     summary="Customer Register",
     *     tags={"Customer Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Registered successfully",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Indicates if the request was successful"),
     *             @OA\Property(property="message", type="string", example="Registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *                 @OA\Property(property="customer", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="email_verified_at", type="string", nullable=true, example=null),
     *                     @OA\Property(property="created_at", type="string", example="2024-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="data", type="object", example={"email": {"The email field is required."}})
     *         )
     *     ),
     *     @OA\Response(response=500, description="Could not create token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Could not create token"),
     *             @OA\Property(property="data", example={})
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request?->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:customers,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        try {
            $customer = Customer::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            $token = $customer->createToken('auth_token')->plainTextToken;
        } catch (Exception $e) {
            return $this->sendError('Could not create token', [], 500);
        }

        return $this->sendResponse('Registered successfully', [
            'token' => $token,
            'customer' => $customer,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/customer/auth/login",
     *     summary="Customer Login",
     *     tags={"Customer Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logged in successfully",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Indicates if the request was successful"),
     *             @OA\Property(property="message", type="string", example="Logged in successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *                 @OA\Property(property="customer", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="email_verified_at", type="string", nullable=true, example=null),
     *                     @OA\Property(property="created_at", type="string", example="2024-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(property="data", example={})
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="data", type="object", example={"email": {"The email field is required."}})
     *         )
     *     ),
     *     @OA\Response(response=500, description="Could not create token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Could not create token"),
     *             @OA\Property(property="data", example={})
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request?->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        // Check if email exists
        $customer = Customer::where('email', $request->input('email'))
            ->first();

        // Check if customer exists and password is correct
        if (! $customer || ! Hash::check($request->input('password'), $customer->password)) {
            return $this->sendError('Invalid credentials', [], 401);
        }

        try {
            $customer->tokens()->delete(); // Revoke all previous tokens

            $token = $customer->createToken('auth_token')->plainTextToken;
        } catch (Exception $e) {
            return $this->sendError('Could not create token', [], 500);
        }

        return $this->sendResponse('Logged in successfully', [
            'token' => $token,
            'customer' => $customer,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/customer/auth/logout",
     *     summary="Customer Logout",
     *     tags={"Customer Auth"},
     *     security={{ "sanctum": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Indicates if the request was successful"),
     *             @OA\Property(property="message", type="string", example="Logged out successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request?->user()?->tokens()?->delete();

        return $this->sendResponse('Logged out successfully', null);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/customer/auth/forgot",
     *     summary="Customer Forgot Password",
     *     tags={"Customer Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true, description="Indicates if the request was successful"),
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="data", type="object", example={"email": {"The email field is required."}})
     *         )
     *     ),
     *     @OA\Response(response=500, description="Unable to send reset link or failed to process request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false, description="Indicates the request failed"),
     *             @OA\Property(property="message", type="string", example="Unable to send reset link"),
     *             @OA\Property(property="data", example={})
     *         )
     *     )
     * )
     */
    public function forgot(Request $request)
    {
        $validator = Validator::make($request?->all(), [
            'email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        try {
            $status = Password::broker('customers')->sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return $this->sendResponse('Password reset link sent to your email', null);
            }

            return $this->sendError('Unable to send reset link', [], 500);
        } catch (Exception $e) {
            return $this->sendError('Failed to process request', ['error' => $e->getMessage()], 500);
        }
    }
}
