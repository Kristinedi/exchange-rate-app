# Exchange Rate Tracker

A web application that fetches and displays currency exchange rates, updated hourly.

## Features

- Live exchange rate tables with currency filtering
- Customizable rate tables per user (add, rename, delete)
- Automatic hourly rate updates via WebSocket
- User authentication

## Tech Stack

**Backend:** Laravel, PHP  
**Frontend:** Vue 3 (Composition API), TypeScript, Inertia.js  
**UI:** shadcn/ui, Tailwind CSS  
**Real-time:** Laravel Echo, WebSockets  
**Data:** [FreeExchangeRateApi](https://github.com/haxqer/FreeExchangeRateApi)

## Status

Work in progress.

## Setup

```bash
composer install
pnpm install
cp .env.example .env
php artisan key:generate
php artisan migrate
pnpm dev
```
