<?php

namespace Workdo\Webhook\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SendWebhook extends Model
{
    static public function SendWebhookCall($module, $parameter, $action, $workspace_id = null)
    {
        $webhookId = WebhookModule::where('module', $module)->where('submodule', $action)->first();
        if (!empty($webhookId)) {
            if (!empty($workspace_id)) {
                $webhook = Webhook::where('action', $webhookId->id)->where('workspace', '=', $workspace_id)->first();
            } else {
                $webhook = Webhook::where('action', $webhookId->id)->where('workspace', '=', getActiveWorkSpace())->first();
            }

            if (!empty($webhook)) {

                $url = $webhook->url;
                $method = strtoupper($webhook->method);
                $reference_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $data['method'] = $method;
                $data['reference_url'] = $reference_url;
                $data['request'] = json_encode((is_array($parameter)) ? $parameter : $parameter->toArray());

                if (!empty($url) && !empty($parameter)) { 
                    try {
                        $curlHandle = curl_init($url);
                        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
                        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
                        $curlResponse = curl_exec($curlHandle);
                        curl_close($curlHandle);
                        if (empty(trim($curlResponse))) {
                            return true;
                        } else {
                            return false;
                        }
                    } catch (\Throwable $th) {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    static public function SendWebhookCallWorkflow($setting, $details)
    {
        if (!empty($setting->webhook_url) && !empty($details)) {

            $url = $setting->webhook_url;
            $method = strtoupper($setting->method);
            $reference_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $data['method'] = $setting->method;
            $data['reference_url'] = $reference_url;
            $data['request'] = json_encode((is_array($details)) ? $details : $details->toArray());

            if (!empty($url) && !empty($details)) {
                try {
                    $curlHandle = curl_init($url);
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
                    $curlResponse = curl_exec($curlHandle);
                    curl_close($curlHandle);
                    if (empty(trim($curlResponse))) {

                        return true;
                    } else {

                        return false;
                    }
                } catch (\Throwable $th) {

                    return false;
                }
            } else {

                return false;
            }
        } else {

            return false;
        }
    }
}
