<?php

namespace App\Enums;

/**
 * Single source of truth for order statuses. Previously this list of
 * strings was duplicated in OrderController@updateStatus (as a Rule::in
 * array) and in orders/show.blade.php (as a hand-typed @foreach list) -
 * two places that could silently drift apart. Now both read from here.
 *
 * Backed by the same string values already stored in the orders.status
 * column, so no migration is needed - Laravel casts the column straight
 * to/from this enum.
 */
enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * Tailwind-free, plain hex tokens matching the app's design system
     * (see resources/views/layouts/app.blade.php :root variables).
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => '#C98A2C',   // gold
            self::Paid => '#2F6E4E',      // moss
            self::Shipped => '#2F6E4E',   // moss
            self::Completed => '#12172B', // ink
            self::Cancelled => '#FF5A3C', // signal
        };
    }

    public function backgroundColor(): string
    {
        return match ($this) {
            self::Pending => 'rgba(201,138,44,0.14)',
            self::Paid, self::Shipped => 'rgba(47,110,78,0.14)',
            self::Completed => 'rgba(18,23,43,0.08)',
            self::Cancelled => 'rgba(255,90,60,0.12)',
        };
    }

    /** All cases as [value => label] for building <select> options. */
    public static function options(): array
    {
        return array_combine(
            array_map(fn (self $c) => $c->value, self::cases()),
            array_map(fn (self $c) => $c->label(), self::cases()),
        );
    }
}