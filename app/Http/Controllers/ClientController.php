<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatus;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\ActivityLogService;
use App\Services\CodeGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(
        private readonly CodeGeneratorService $codeGeneratorService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'country' => $request->string('country')->toString(),
            'status' => $request->string('status')->toString(),
            'product' => $request->string('product')->toString(),
        ];

        $clients = Client::query()
            ->with('creator')
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('client_code', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('pic_name', 'like', "%{$search}%")
                        ->orWhere('pic_email', 'like', "%{$search}%");
                });
            })
            ->when($filters['country'], fn ($query, $country) => $query->where('country', 'like', "%{$country}%"))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['product'], fn ($query, $product) => $query->where('interested_products', 'like', "%{$product}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', [
            'pageTitle' => 'Client Management / CRM',
            'pageSubtitle' => 'Kelola buyer pipeline, kebutuhan produk, dan kontak client export dalam satu workspace.',
            'clients' => $clients,
            'filters' => $filters,
            'statusOptions' => ClientStatus::options(),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('clients.create', [
            'pageTitle' => 'Add Client',
            'pageSubtitle' => 'Input buyer atau prospect baru ke pipeline CRM Archipela.',
            'client' => new Client([
                'country' => 'Indonesia',
                'currency' => 'USD',
                'status' => ClientStatus::LEAD->value,
            ]),
            'statusOptions' => ClientStatus::options(),
            'formAction' => route('clients.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Client',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['client_code'] = $this->codeGeneratorService->generateClientCode();
        $payload['created_by'] = $request->user()?->id;

        $client = Client::query()->create($payload);

        $this->activityLogService->log(
            moduleName: 'clients',
            record: $client,
            action: 'created',
            newValue: $client->fresh()?->toArray(),
            description: "Client {$client->client_code} created",
        );

        return redirect()
            ->route('clients.show', $client)
            ->with('status', "Client {$client->client_code} created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): View
    {
        return view('clients.show', [
            'pageTitle' => 'Client Detail',
            'pageSubtitle' => 'Ringkasan buyer profile, contact information, dan status pipeline saat ini.',
            'client' => $client->load('creator'),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): View
    {
        return view('clients.edit', [
            'pageTitle' => 'Edit Client',
            'pageSubtitle' => "Update data client {$client->client_code}.",
            'client' => $client,
            'statusOptions' => ClientStatus::options(),
            'formAction' => route('clients.update', $client),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Client',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $oldValue = $client->toArray();
        $client->update($request->validated());

        $this->activityLogService->log(
            moduleName: 'clients',
            record: $client,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $client->fresh()?->toArray(),
            description: "Client {$client->client_code} updated",
        );

        return redirect()
            ->route('clients.show', $client)
            ->with('status', "Client {$client->client_code} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $oldValue = $client->toArray();
        $clientCode = $client->client_code;
        $client->delete();

        $this->activityLogService->log(
            moduleName: 'clients',
            record: $client,
            action: 'deleted',
            oldValue: $oldValue,
            description: "Client {$clientCode} deleted",
        );

        return redirect()
            ->route('clients.index')
            ->with('status', "Client {$clientCode} deleted successfully.");
    }

    private function statusBadgeMap(): array
    {
        return [
            ClientStatus::LEAD->value => 'bg-light-secondary',
            ClientStatus::CONTACTED->value => 'bg-light-info',
            ClientStatus::QUALIFIED->value => 'bg-light-primary',
            ClientStatus::SAMPLE_REQUESTED->value => 'bg-light-warning',
            ClientStatus::SAMPLE_SENT->value => 'bg-warning text-dark',
            ClientStatus::QUOTATION_SENT->value => 'bg-light-info',
            ClientStatus::NEGOTIATION->value => 'bg-primary',
            ClientStatus::PO_RECEIVED->value => 'bg-success',
            ClientStatus::ACTIVE_BUYER->value => 'bg-success',
            ClientStatus::REPEAT_BUYER->value => 'bg-success',
            ClientStatus::LOST->value => 'bg-danger',
            ClientStatus::INACTIVE->value => 'bg-dark',
        ];
    }

    private function statusLabelMap(): array
    {
        return collect(ClientStatus::options())
            ->pluck('label', 'value')
            ->all();
    }
}
