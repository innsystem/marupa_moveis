<?php

namespace App\Integrations;

use Google_Client;
use Google_Service_Analytics;

class GoogleAnalyticsIntegration
{
    protected $client;
    protected $analytics;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/google-analytics/service-account-credentials.json'));
        $this->client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

        $this->analytics = new Google_Service_Analytics($this->client);
    }

    public function getAnalyticsData($viewId, $startDate, $endDate)
    {
        $metrics = 'ga:sessions,ga:users,ga:newUsers,ga:bounceRate';
        $optParams = [
            'dimensions' => 'ga:date',
        ];

        $results = $this->analytics->data_ga->get(
            'ga:' . $viewId,
            $startDate,
            $endDate,
            $metrics,
            $optParams
        );

        return $results->getRows();
    }
}