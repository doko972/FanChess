<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameMoveMade implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $gameUuid,
        public string $from,
        public string $to,
        public string $san,
        public string $fen,
        public string $piece,
        public ?string $captured = null,
        public ?string $promotion = null,
        public bool $isCheck = false,
        public bool $isCheckmate = false,
        public bool $isCastling = false,
        public bool $isEnPassant = false,
        public ?string $winner = null,
        public ?string $endReason = null,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('game.' . $this->gameUuid),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'GameMoveMade';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'gameUuid' => $this->gameUuid,
            'from' => $this->from,
            'to' => $this->to,
            'san' => $this->san,
            'fen' => $this->fen,
            'piece' => $this->piece,
            'captured' => $this->captured,
            'promotion' => $this->promotion,
            'isCheck' => $this->isCheck,
            'isCheckmate' => $this->isCheckmate,
            'isCastling' => $this->isCastling,
            'isEnPassant' => $this->isEnPassant,
            'winner' => $this->winner,
            'endReason' => $this->endReason,
        ];
    }
}
