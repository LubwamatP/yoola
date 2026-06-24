<?php

namespace App\Events;

use App\Models\ProductView;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductViewedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly ProductView $productView
    ) {
    }

    /**
     * Get the product ID from the view
     */
    public function getProductId(): int
    {
        return $this->productView->product_id;
    }

    /**
     * Get the user ID if available
     */
    public function getUserId(): ?int
    {
        return $this->productView->user_id;
    }

    /**
     * Get the session ID
     */
    public function getSessionId(): string
    {
        return $this->productView->session_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
