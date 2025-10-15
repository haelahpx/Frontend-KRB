<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GoogleMeetService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->setAccessType('offline');             // needed for refresh_token
        $this->client->setPrompt('consent');                 // first time only
        $this->client->setIncludeGrantedScopes(true);        // incremental auth
        $this->client->setScopes(explode(' ', env('GOOGLE_SCOPES')));
    }

    public function handleCallback(string $code): void
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \RuntimeException('OAuth error: ' . $token['error'] . (!empty($token['error_description']) ? ' - ' . $token['error_description'] : ''));
        }
        if (!isset($token['access_token'])) {
            throw new \RuntimeException('Invalid token from Google: missing access_token.');
        }

        // If Google didn’t send refresh_token this time, keep the old one (common behavior)
        $path = $this->tokenPath(\Auth::id());
        if (empty($token['refresh_token']) && file_exists($path)) {
            $old = json_decode(file_get_contents($path), true) ?: [];
            if (!empty($old['refresh_token'])) {
                $token['refresh_token'] = $old['refresh_token'];
            }
        }

        File::put($path, json_encode($token, JSON_PRETTY_PRINT));
    }

    private function bootWithTokensFor(int $userId): void
    {
        $path = $this->tokenPath($userId);
        if (!file_exists($path)) {
            throw new \RuntimeException('Google not connected. Please connect first.');
        }

        $token = json_decode(file_get_contents($path), true);
        if (!is_array($token) || !isset($token['access_token'])) {
            throw new \RuntimeException('Invalid token format in storage. Please reconnect Google.');
        }

        $this->client->setAccessType('offline');
        $this->client->setAccessToken($token);

        // Ensure the client knows the refresh token (not always present after setAccessToken)
        if (!$this->client->getRefreshToken() && !empty($token['refresh_token'])) {
            // Older Google PHP lib uses setAccessToken; new ones read refresh_token from the array.
            // This line just ensures it’s definitely in memory:
            $this->client->refreshToken($token['refresh_token']);
            $this->client->setAccessToken(array_merge($token, $this->client->getAccessToken()));
        }

        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken() ?: ($token['refresh_token'] ?? null);
            if (!$refreshToken) {
                throw new \RuntimeException('Missing refresh_token. Remove app access in Google Account, then connect again.');
            }

            $new = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            if (isset($new['error'])) {
                throw new \RuntimeException('Failed refreshing token: ' . $new['error']);
            }

            // merge but keep original refresh_token if Google didn’t return a new one
            $merged = array_merge($token, array_filter($new));
            if (empty($merged['refresh_token']))
                $merged['refresh_token'] = $refreshToken;

            file_put_contents($path, json_encode($merged, JSON_PRETTY_PRINT));
            $this->client->setAccessToken($merged);
        }
    }

    /**
     * Create an event with a Google Meet link.
     * Optionally pass attendees' emails to auto-invite people.
     */
    public function createMeet(string $summary, Carbon $start, Carbon $end, ?string $description = null, array $attendeesEmails = []): array
    {
        $userId = Auth::id();
        $this->bootWithTokensFor($userId);

        $service = new Calendar($this->client);
        $calendarId = env('GOOGLE_CALENDAR_ID', 'primary');

        $attendees = array_map(fn($e) => ['email' => trim($e)], $attendeesEmails);

        $event = new Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $start->copy()->timezone('Asia/Jakarta')->toRfc3339String(),
                'timeZone' => 'Asia/Jakarta',
            ],
            'end' => [
                'dateTime' => $end->copy()->timezone('Asia/Jakarta')->toRfc3339String(),
                'timeZone' => 'Asia/Jakarta',
            ],
            'attendees' => $attendees,              // optional
            'conferenceData' => [
                'createRequest' => [
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                    'requestId' => (string) Str::uuid(),
                ],
            ],
        ]);

        $created = $service->events->insert($calendarId, $event, [
            'conferenceDataVersion' => 1,
            'sendUpdates' => empty($attendees) ? 'none' : 'all',  // email invites if attendees exist
        ]);

        return [
            'url' => $created->hangoutLink ?? null,
            'code' => null,
            'password' => null,
        ];
    }
}
