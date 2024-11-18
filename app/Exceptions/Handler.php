<?php

namespace App\Exceptions;

use App\Constants\ResponseConstants;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // 覆盖父类中的 render 方法
    public function render($request, Throwable $exception)
    {
        // 记录异常
        $this->logException($request, $exception);
        // 设置语言
        $this->setLocale($request);

        if ($request->expectsJson() || $request->is('api/*')) {
            $response = $this->handleJsonException($exception);
            return response()->json($response['body'], $response['httpCode']);
        }

        return parent::render($request, $exception);
    }

    private function logException($request, Throwable $exception): void
    {
        if (config("app.switch.exception_log")) {
            $errMsg = "request api: " . $request->url() .
                "\nrequest params: " . json_encode($request->all(), JSON_PRETTY_PRINT) .
                "\nerror: " . $exception->getMessage() . " in " . $exception->getFile() . " line " . $exception->getLine();
            if ($userId = auth()->id()) {
                $errMsg = "user id: " . $userId . "\n" . $errMsg;
            }
            Log::channel('custom_error')->error($errMsg);
        }
    }

    private function setLocale($request): void
    {
        $locale = $request->header('Accept-Language', config('app.locale'));
        App::setLocale($locale);
    }

    /**
     * 处理异常
     * @param Throwable $exception
     * @return array
     */
    private function handleJsonException(Throwable $exception): array
    {
        $code = ResponseConstants::SERVER_ERROR;
        $message = ResponseConstants::getMessageByCode(ResponseConstants::SERVER_ERROR);
        $data = new \stdClass();
        $httpCode = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;

        switch (true) {
            // 请求参数验证失败
            case $exception instanceof ValidationException:
                $code = ResponseConstants::PARAMS_ERROR;
                $message = ResponseConstants::getMessageByCode(ResponseConstants::PARAMS_ERROR) . ': ' . array_values($exception->errors())[0][0];
                $httpCode = ResponseAlias::HTTP_BAD_REQUEST;
                break;
            // 404
            case $exception instanceof NotFoundHttpException:
                $code = ResponseConstants::NOT_FOUND;
                $message = ResponseConstants::getMessageByCode(ResponseConstants::NOT_FOUND);
                $httpCode = ResponseAlias::HTTP_NOT_FOUND;
                break;
            // 业务异常
            case $exception instanceof CommonException:
                $code = $exception->getCode();
                $message = $exception->getMessage();
                $httpCode = ResponseAlias::HTTP_BAD_REQUEST;
                break;
            // 请求方法不允许
            case $exception instanceof MethodNotAllowedHttpException:
                $code = ResponseConstants::METHOD_NOT_ALLOWED;
                $message = ResponseConstants::getMessageByCode(ResponseConstants::METHOD_NOT_ALLOWED);
                $httpCode = ResponseAlias::HTTP_METHOD_NOT_ALLOWED;
                break;
            // 数据库唯一键冲突
            case $exception instanceof UniqueConstraintViolationException:
                $code = ResponseConstants::DATABASE_UNIQUE_CONFLICT;
                $message = ResponseConstants::getMessageByCode(ResponseConstants::DATABASE_UNIQUE_CONFLICT);
                $httpCode = ResponseAlias::HTTP_CONFLICT;
                break;
            default:
                break;
        }

        $body = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        if (config("app.switch.debug_log")) {
            $body['error']['info'] = $exception->getMessage() . " in " . $exception->getFile() . " line " . $exception->getLine();
            $body['error']['type'] = get_class($exception);
            $body['error']['trace'] = $exception->getTrace();
        }

        return ['body' => $body, 'httpCode' => $httpCode];
    }
}
