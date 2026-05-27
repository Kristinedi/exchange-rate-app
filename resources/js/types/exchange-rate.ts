export interface Currency {
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
