<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display the global search page
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (!empty($query)) {
            $results = $this->searchService->globalSearch($query);
        }

        $stats = $this->searchService->getSearchStats();

        return view('backpack.search.index', compact('query', 'results', 'stats'));
    }

    /**
     * Perform advanced search
     */
    public function advanced(Request $request)
    {
        $filters = $request->only([
            'query',
            'user_type',
            'date_from',
            'date_to'
        ]);

        $results = $this->searchService->advancedSearch($filters);

        if ($request->wantsJson()) {
            return response()->json([
                'results' => $results,
                'filters' => $filters
            ]);
        }

        return view('backpack.search.advanced', compact('results', 'filters'));
    }

    /**
     * Get search suggestions (AJAX)
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query) || strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = $this->searchService->getSearchSuggestions($query);

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * Quick search for specific entity types
     */
    public function quickSearch(Request $request, string $type)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);

        $results = [];

        switch ($type) {
            case 'users':
                $results = $this->searchService->searchUsers($query, $limit);
                break;
            case 'appointments':
                $results = $this->searchService->searchAppointments($query, $limit);
                break;
            case 'patients':
                $results = $this->searchService->searchPatientProfiles($query, $limit);
                break;
            case 'lab-results':
                $results = $this->searchService->searchLabResults($query, $limit);
                break;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'type' => $type,
                'query' => $query,
                'results' => $results
            ]);
        }

        return view('backpack.search.results', compact('type', 'query', 'results'));
    }

    /**
     * Export search results
     */
    public function export(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        if (empty($query)) {
            return back()->with('error', 'Search query is required for export');
        }

        $results = $type === 'all'
            ? $this->searchService->globalSearch($query)
            : [$type => $this->searchService->quickSearch($request, $type)];

        // Generate Excel export
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SearchResultsExport($results, $query, $type),
            'search_results_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }
}
