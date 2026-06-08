<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArkipelaParameterRequest;
use App\Models\ArkipelaParameter;
use App\Services\ActivityLogService;
use App\Services\ArkipelaParameterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class ArkipelaParameterController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'group_key' => $request->string('group_key')->toString(),
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        $parameters = ArkipelaParameter::query()
            ->when($filters['group_key'], fn ($query, $groupKey) => $query->where('group_key', $groupKey))
            ->when($filters['status'] !== '', function ($query) use ($filters) {
                $query->where('is_active', $filters['status'] === 'active');
            })
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('group_key', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('group_key')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('settings.parameters.index', [
            'pageTitle' => 'Arkipela Parameters',
            'pageSubtitle' => 'Kelola master parameter reusable untuk unit qty, packaging, size unit, dan referensi lookup lain.',
            'parameters' => $parameters,
            'filters' => $filters,
            'groupOptions' => $this->groupOptions(),
        ]);
    }

    public function create(): View
    {
        return view('settings.parameters.create', [
            'pageTitle' => 'Create Parameter',
            'pageSubtitle' => 'Tambahkan master parameter baru agar bisa dipakai lintas module.',
            'parameter' => new ArkipelaParameter([
                'is_active' => true,
                'sort_order' => 0,
            ]),
            'attributesJson' => '',
            'groupOptions' => $this->groupOptions(),
            'formAction' => route('settings.parameters.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Parameter',
        ]);
    }

    public function store(ArkipelaParameterRequest $request): RedirectResponse
    {
        $parameter = ArkipelaParameter::query()->create($this->payloadFromRequest($request));
        $this->forgetParameterCache($parameter->group_key);

        $this->activityLogService->log(
            moduleName: 'arkipela-parameters',
            record: $parameter,
            action: 'created',
            newValue: $parameter->toArray(),
            description: "Parameter {$parameter->group_key}.{$parameter->code} created",
        );

        return redirect()
            ->route('settings.parameters.index')
            ->with('status', "Parameter {$parameter->name} created successfully.");
    }

    public function edit(ArkipelaParameter $parameter): View
    {
        return view('settings.parameters.edit', [
            'pageTitle' => 'Edit Parameter',
            'pageSubtitle' => "Update parameter {$parameter->group_key}.{$parameter->code}.",
            'parameter' => $parameter,
            'attributesJson' => $parameter->attributes ? json_encode($parameter->attributes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '',
            'groupOptions' => $this->groupOptions(),
            'formAction' => route('settings.parameters.update', $parameter),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Parameter',
        ]);
    }

    public function update(ArkipelaParameterRequest $request, ArkipelaParameter $parameter): RedirectResponse
    {
        $oldValue = $parameter->toArray();
        $oldGroupKey = $parameter->group_key;
        $parameter->update($this->payloadFromRequest($request));
        $this->forgetParameterCache($oldGroupKey);
        $this->forgetParameterCache($parameter->group_key);

        $this->activityLogService->log(
            moduleName: 'arkipela-parameters',
            record: $parameter,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $parameter->fresh()->toArray(),
            description: "Parameter {$parameter->group_key}.{$parameter->code} updated",
        );

        return redirect()
            ->route('settings.parameters.index')
            ->with('status', "Parameter {$parameter->name} updated successfully.");
    }

    private function payloadFromRequest(ArkipelaParameterRequest $request): array
    {
        $payload = $request->validated();
        $attributesJson = $payload['attributes_json'] ?? null;
        unset($payload['attributes_json']);

        $payload['description'] = $payload['description'] !== '' ? $payload['description'] : null;
        $payload['attributes'] = $attributesJson ? json_decode($attributesJson, true) : null;

        return $payload;
    }

    private function groupOptions(): array
    {
        $systemGroups = [
            ArkipelaParameterService::GROUP_QUANTITY_UNIT,
            ArkipelaParameterService::GROUP_DIMENSION_UNIT,
            ArkipelaParameterService::GROUP_PACKAGING_TYPE,
            ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE,
        ];

        return ArkipelaParameter::query()
            ->select('group_key')
            ->distinct()
            ->orderBy('group_key')
            ->pluck('group_key')
            ->merge($systemGroups)
            ->unique()
            ->values()
            ->all();
    }

    private function forgetParameterCache(string $groupKey): void
    {
        Cache::forget("arkipela-parameters:{$groupKey}");
    }
}
