<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'from_currency_id'   => $this->from_currency_id,
            'from_currency'      => $this->fromCurrency->currency_code,
            'from_currency_name' => $this->fromCurrency->currency_name,
            'to_currency_id'     => $this->to_currency_id,
            'to_currency'        => $this->toCurrency->currency_code,
            'to_currency_name'   => $this->toCurrency->currency_name,
            'rate'               => $this->rate,
            'recorded_at'        => $this->recorded_at,
        ];
    }
}
