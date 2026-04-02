<?php
namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display the services page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
                                                     // Fetch all services from the database
        $services = Service::orderBy('service_type', 'desc') // Packages first, then singles
            ->orderBy('name', 'asc')
            ->get();

        // Group services by type for easier handling in the view
        $groupedServices = [
            'packages' => $services->where('service_type', 'package'),
            'singles'  => $services->where('service_type', 'single'),
        ];

        // Pass data to the view
        return view('pages.services.index', compact('services', 'groupedServices'));
    }

    /**
     * Show a specific service details.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);

        // Get related services (same type)
        $relatedServices = Service::where('service_type', $service->service_type)
            ->where('id', '!=', $service->id)
            ->limit(3)
            ->get();

        return view('pages.services.show', compact('service', 'relatedServices'));
    }

    /**
     * Get services data as JSON (for AJAX requests or API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServices()
    {
        $services = Service::select('id', 'name', 'description', 'service_type', 'base_price', 'philhealth_price', 'duration_minutes')
            ->orderBy('service_type', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $services,
        ]);
    }

    /**
     * Get services by type (single or package)
     *
     * @param string $type
     * @return \Illuminate\View\View
     */
    public function getByType($type)
    {
        // Validate type
        if (! in_array($type, ['single', 'package'])) {
            abort(404, 'Service type not found.');
        }

        $services = Service::where('service_type', $type)
            ->orderBy('name', 'asc')
            ->get();

        $pageTitle = ucfirst($type) . ' Services';

        return view('pages.services.by-type', compact('services', 'type', 'pageTitle'));
    }
}
