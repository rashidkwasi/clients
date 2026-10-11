<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HttpResponses;

class ClientsController extends Controller
{
    use HttpResponses;

    /**
     * Display all client records
     */
    public function allclients()
    {
        $clients = Client::where('businessid', auth()->user()->businessid)->get();
        return $this->success($clients);
    }

    /**
     * Create a new client record
     */
    public function createclient(Request $request)
    {
        DB::beginTransaction();

        try {
            $client = Client::create([
                'cleintid' => $request->cleintid,
                'business_name' => $request->business_name,
                'business_owner' => $request->business_owner,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'address' => $request->address,
                'city' => $request->city,
                'url' => $request->url,
                'icon' => $request->icon,
                'createdby' => auth()->user()->userid ?? 'system',
                'created_at' => $request->created_at,
                'modifiedby' => auth()->user()->userid ?? 'system',
                'modified_at' => $request->modified_at,
                'plan' => $request->plan,
                'status' => $request->status,
                'businessid' => $request->businessid,
            ]);

            DB::commit();
            return $this->success($client);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single client record
     */
    public function show($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($client);
    }

    /**
     * Update a client record
     */
    public function updateclient(Request $request)
    {
        $client = Client::find($request->id);

        if (!$client) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'cleintid' => 'sometimes|string|max:20|unique:clients,cleintid,' . $client->id,
            'business_name' => 'sometimes|string|max:45',
            'business_owner' => 'sometimes|string|max:45',
            'email' => 'sometimes|email|max:128',
            'telephone' => 'sometimes|string|max:15',
            'address' => 'sometimes|string|max:45',
            'city' => 'sometimes|string|max:20',
            'url' => 'sometimes|string|max:256|nullable',
            'icon' => 'sometimes|string|max:256|nullable',
            'createdby' => 'sometimes|string|max:45|nullable',
            'created_at' => 'sometimes|date',
            'modifiedby' => 'sometimes|string|max:45|nullable',
            'modified_at' => 'sometimes|date',
            'plan' => 'sometimes|string|max:45|nullable',
            'status' => 'sometimes|string|max:45|nullable',
            'businessid' => 'sometimes|string|max:20',
        ]);

        $client->update([
            'cleintid' => $request->cleintid ?? $client->cleintid,
            'business_name' => $request->business_name ?? $client->business_name,
            'business_owner' => $request->business_owner ?? $client->business_owner,
            'email' => $request->email ?? $client->email,
            'telephone' => $request->telephone ?? $client->telephone,
            'address' => $request->address ?? $client->address,
            'city' => $request->city ?? $client->city,
            'url' => $request->url ?? $client->url,
            'icon' => $request->icon ?? $client->icon,
            'createdby' => $request->createdby ?? $client->createdby,
            'created_at' => $request->created_at ?? $client->created_at,
            'modifiedby' => auth()->user()->userid ?? 'system',
            'modified_at' => $request->modified_at ?? $client->modified_at,
            'plan' => $request->plan ?? $client->plan,
            'status' => $request->status ?? $client->status,
            'businessid' => $request->businessid ?? $client->businessid,
        ]);

        return $this->success($client);
    }

    /**
     * Delete a client record
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}