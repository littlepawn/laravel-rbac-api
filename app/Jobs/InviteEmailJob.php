<?php

namespace App\Jobs;

use App\Constants\CommonConstants;
use App\Constants\RedisConstants;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InviteEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string $email
     */
    protected string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $email = $this->email;
            $verificationToken = Str::random(60);
            $expireTime = RedisConstants::EMAIL_INVITE_ADMIN_EXPIRE_TIME;
            $EMAIL_VERIFY_KEY = RedisConstants::geneRedisKey(RedisConstants::EMAIL_INVITE_TOKEN_PREFIX, $email);
            Redis::setex($EMAIL_VERIFY_KEY, $expireTime, $verificationToken);

            //模拟发送邮件为写入
            $this->sendEmail($email, $verificationToken, $expireTime);

            echo "Invite Email sent successfully.\n";
        } catch (\Throwable $e) {
            Log::error('Error sending email: ' . $e->getMessage() . ' | line: ' . $e->getLine());
        }
    }

    /**
     * 模拟发送验证邮箱
     * @param string $email
     * @param string $verificationToken
     * @param int $expireTime
     * @return void
     */
    protected function sendEmail(string $email, string $verificationToken, int $expireTime): void
    {
        // Send email logic
        $filename = CommonConstants::EMAIL_INVITE_FILENAME;
        $data = [];
        if (Storage::exists($filename)) {
            $rawData = Storage::get($filename);
            if (!empty($rawData)) $data = json_decode($rawData, true);
        }
        $verifyUrl = config('app.dev_config.url') . route('admin.invite', ['email' => $email, 'token' => $verificationToken], false);
        $data[$email] = [
            'verification_token' => $verificationToken,
            'expire_time' => date('Y-m-d H:i:s', $expireTime + time()),
            'invite_url' => $verifyUrl];
        Storage::put($filename, json_encode($data, JSON_PRETTY_PRINT));
    }
}
