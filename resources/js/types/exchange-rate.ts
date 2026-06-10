export interface Currency {
    id: number;
    code: string;
    name: string;
}
export interface ExchangeRate {
    from_currency_id: number;
    from_currency: string;
    from_currency_name: string;
    to_currency_id: number;
    to_currency: string;
    to_currency_name: string;
    rate: number;
    recorded_at: string;
}

export interface SavedTable {
    id: number;
    name: string;
    from_currency: Currency;
    to_currencies: Currency[];
}

export interface Table {
    id: number;
    dbId: number | null;
    name: string;
    isRenamed: boolean;
    newName: string;
    fromCurrency: Currency;
    toCurrencies: Currency[];
}
