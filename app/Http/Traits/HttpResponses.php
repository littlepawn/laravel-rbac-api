<?php

namespace App\Http\Traits;

use App\Constants\ResponseConstants;
use App\Exceptions\CommonException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait HttpResponses
{
    /**
     * 优化正确响应
     * @param $data
     * @param string|null $successMessage
     * @return JsonResponse
     */
    protected function success(
        $data = new \stdClass(),
        string $successMessage = null): JsonResponse
    {
        if (empty($data)) {
            $data = new \stdClass();
        }
        return response()->json([
            'code' => ResponseConstants::SUCCESS,
            'message' => $successMessage ? $this->transSuccess($successMessage) : ResponseConstants::getMessageByCode(ResponseConstants::SUCCESS),
            'data' => $data,
        ], ResponseAlias::HTTP_OK);
    }

    /**
     * 优化无数据成功响应
     * @param string|null $successMessage
     * @return JsonResponse
     */
    protected function successNoData(
        string $successMessage = null): JsonResponse
    {
        return response()->json([
            'code' => ResponseConstants::SUCCESS,
            'message' => $successMessage ? $this->transSuccess($successMessage) : ResponseConstants::getMessageByCode(ResponseConstants::SUCCESS),
            'data' => new \stdClass(),
        ], ResponseAlias::HTTP_OK);
    }

    /**
     * 优化错误响应
     * @param int $code
     * @param string|null $message
     * @param int $httpCode
     * @return JsonResponse
     */
    protected function error(
        int    $code = ResponseConstants::UNKNOWN,
        string $message = null,
        int    $httpCode = ResponseAlias::HTTP_BAD_REQUEST
    ): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message ?: ResponseConstants::getMessageByCode($code),
            'data' => new \stdClass(),
        ], $httpCode);
    }

    /**
     * @throws CommonException
     */
    protected function throwException(int $code = ResponseConstants::UNKNOWN, string $message = ''): void
    {
        if ($message) {
            throw new CommonException($message, $code);
        }
        throw new CommonException(ResponseConstants::getMessageByCode($code), $code);
    }

    /**
     * 获取成功返回提示语
     * @param string|null $successMessage
     * @return string|null
     */
    private function transSuccess(string $successMessage = null): ?string
    {
        return __('success.' . $successMessage) ?: null;
    }
}
