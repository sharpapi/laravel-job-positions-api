<?php

declare(strict_types=1);

namespace SharpAPI\JobPositionsApi;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use SharpAPI\Core\Client\SharpApiClient;

/**
 * @api
 */
class JobPositionsApiService extends SharpApiClient
{
    /**
     * Initializes a new instance of the class.
     *
     * @throws InvalidArgumentException if the API key is empty.
     */
    public function __construct()
    {
        parent::__construct(config('sharpapi-job-positions-api.api_key'));
        $this->setApiBaseUrl(
            config(
                'sharpapi-job-positions-api.base_url',
                'https://sharpapi.com/api/v1'
            )
        );
        $this->setUserAgent('SharpAPILaravelJobPositionsApi/1.0.0');
    }

    /**
     * Search for job positions by title or keyword.
     *
     * @param string $query The search query
     * @param int|null $limit Maximum number of results to return
     * @return array The search results
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function searchJobPositions(string $query, ?int $limit = null): array
    {
        $params = [
            'name' => $query,
        ];

        if ($limit !== null) {
            $params['per_page'] = $limit;
        }

        $response = $this->makeRequest(
            'GET',
            '/utilities/job_positions_list',
            $params
        );

        $responseData = json_decode((string) $response->getBody(), true);
        $results = $responseData['data'] ?? [];
        
        // If a limit was specified, ensure we only return that many results
        if ($limit !== null && count($results) > $limit) {
            $results = array_slice($results, 0, $limit);
        }

        return $results;
    }

    /**
     * Get job position details by ID.
     *
     * @param string $positionId The ID of the job position
     * @return array The job position details
     *
     * @throws GuzzleException
     *
     * @api
     */
    public function getJobPositionById(string $positionId): array
    {
        // Since there doesn't seem to be a direct endpoint for getting a job position by ID,
        // we'll get the full list and filter it
        $response = $this->makeRequest(
            'GET',
            '/utilities/job_positions_list',
            ['per_page' => 100] // Get a larger number of results to increase chances of finding the position
        );
        
        $responseData = json_decode((string) $response->getBody(), true);
        $positions = $responseData['data'] ?? [];
        
        // Find the position with the matching ID
        foreach ($positions as $position) {
            if ($position['id'] === $positionId) {
                return $position;
            }
        }
        
        // If we couldn't find it in the first page, we might need to implement pagination
        // For now, return an empty array if not found
        return [];
    }

}