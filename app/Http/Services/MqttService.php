<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    protected $mqtt;

    public function __construct()
    {
        $this->mqtt = new MqttClient(env('MQTT_HOST', '127.0.0.1'), env('MQTT_PORT', 1883), env('MQTT_CLIENT_ID', 'laravel_client'));
    }

    public function subscribe()
    {
        try {
            $this->mqtt->connect();

            $this->mqtt->subscribe('usuario/#', function ($topic, $message) {
                $parts = explode('/', $topic);
                $userId = $parts[1] ?? null;

                if ($userId) {
                    $queueKey = "user_messages_{$userId}";
                    $messages = Cache::get($queueKey, []);
                    $messages[] = $message;
                    Cache::put($queueKey, $messages, 3600);
                }
            }, 0);

            $this->mqtt->loop(true);
        } catch (\Exception $e) {
            Log::error("Erro ao ouvir os tópicos MQTT: " . $e->getMessage());
        }
    }

    public function getUserMessages($userId)
    {
        $queueKey = "user_messages_{$userId}";
        return Cache::get($queueKey, []);
    }
}
