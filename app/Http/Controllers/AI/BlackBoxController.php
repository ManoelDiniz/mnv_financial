<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlackboxAIRequest;
use App\Services\AI\BlackboxAIService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BlackboxAIController extends Controller
{
    public function __construct(
        private BlackboxAIService $blackboxAIService
    ) {}

    public function generateCode(BlackboxAIRequest $request): JsonResponse
    {
        try {
            $result = $this->blackboxAIService->generateCode($request->validated());

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate code: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function analyzeCode(BlackboxAIRequest $request): JsonResponse
    {
        try {
            $result = $this->blackboxAIService->analyzeCode($request->validated()['code']);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze code: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
