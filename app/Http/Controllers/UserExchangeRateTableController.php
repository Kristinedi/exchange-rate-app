<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserExchangeRateTableController extends Controller
{
    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
    public function index()
    {
        $tables = $this->currentUser()->exchangeRateTables()
            ->with(['fromCurrency', 'toCurrencies'])
            ->get();

        return response()->json($tables->map(fn($table) => [
            'id'               => $table->id,
            'name'             => $table->name,
            'from_currency_id' => $table->from_currency_id,
            'from_currency'    => new CurrencyResource($table->fromCurrency),
            'to_currencies'    => CurrencyResource::collection($table->toCurrencies),
        ]));
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'tables'                    => 'nullable|array',
            'tables.*.id'               => 'nullable|exists:user_exchange_rate_tables,id',
            'tables.*.name'             => 'required|string|max:30',
            'tables.*.from_currency_id' => 'required|exists:currencies,id',
            'tables.*.to_currency_ids'  => 'required|array|min:1',
            'tables.*.to_currency_ids.*'=> 'exists:currencies,id',
        ]);

        $user = $this->currentUser();

        $incomingIds = collect($validated['tables'] ?? [])->pluck('id')->filter();
        $user->exchangeRateTables()->whereNotIn('id', $incomingIds)->delete();

        foreach ($validated['tables'] ?? [] as $tableData) {
            $table = $user->exchangeRateTables()->updateOrCreate(
                ['id' => $tableData['id'] ?? null],
                ['name' => $tableData['name'], 'from_currency_id' => $tableData['from_currency_id']],
            );

            $table->toCurrencies()->sync($tableData['to_currency_ids']);
        }

        return response()->json($user->exchangeRateTables()
            ->with(['fromCurrency', 'toCurrencies'])
            ->get()
        );
    }
}
