<x-app-layout>
    @push('styles')
    <style>
        /* Échiquier */
        .chess-board {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 560px;
            margin: 0 auto;
        }

        .chess-square {
            position: relative;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            height: 70px;
            width: 70px;
        }

        .chess-square.light {
            background: linear-gradient(135deg, #e8d5b7 0%, #d4c4a8 100%);
        }

        .chess-square.dark {
            background: linear-gradient(135deg, #8b7355 0%, #6d5a45 100%);
        }

        .chess-square:hover {
            filter: brightness(1.1);
        }

        .chess-square.selected {
            box-shadow: inset 0 0 0 4px #f59e0b !important;
        }

        .chess-square.legal-move::after {
            content: '';
            position: absolute;
            width: 30%;
            height: 30%;
            background: rgba(99, 102, 241, 0.6);
            border-radius: 50%;
            z-index: 5;
        }

        .chess-square.legal-capture {
            box-shadow: inset 0 0 0 4px rgba(239, 68, 68, 0.8) !important;
        }

        .chess-square.last-move {
            background: rgba(245, 158, 11, 0.4) !important;
        }

        .chess-square.check {
            background: rgba(239, 68, 68, 0.6) !important;
        }

        /* Pièces (cartes miniatures) */
        .chess-piece {
            width: 88%;
            height: 88%;
            border-radius: 6px;
            background: linear-gradient(145deg, #ffffff 0%, #e8e8e8 100%);
            box-shadow: 0 3px 10px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: grab;
            transition: transform 0.15s;
            user-select: none;
            z-index: 10;
            color: #1a1a1a; /* Texte foncé pour les cartes blanches */
        }

        .chess-piece.black-piece {
            background: linear-gradient(145deg, #4a4a4a 0%, #2a2a2a 100%);
            color: #f0f0f0;
        }

        .chess-piece:hover {
            transform: scale(1.08);
            z-index: 20;
        }

        .chess-piece.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .piece-icon {
            font-size: 1.6rem;
            line-height: 1;
        }

        .card-image {
            width: 80%;
            height: 65%;
            object-fit: cover;
            border-radius: 4px;
        }

        .piece-name {
            font-size: 0.4rem;
            font-weight: 600;
            text-align: center;
            line-height: 1.1;
            max-width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 1px;
            opacity: 0.9;
        }

        /* Carte de détail */
        .card-detail {
            background: linear-gradient(135deg, rgba(99,102,241,0.2) 0%, rgba(139,92,246,0.2) 100%);
            border: 2px solid rgba(99,102,241,0.3);
        }

        .card-detail.enemy {
            background: linear-gradient(135deg, rgba(239,68,68,0.2) 0%, rgba(185,28,28,0.2) 100%);
            border-color: rgba(239,68,68,0.3);
        }

        /* Stats bars */
        .stat-bar {
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .stat-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        /* Historique des coups */
        .move-item {
            display: flex;
            padding: 0.25rem 0.5rem;
            font-family: monospace;
            font-size: 0.85rem;
        }

        .move-item:nth-child(odd) {
            background: rgba(255,255,255,0.03);
        }

        .move-number {
            width: 2rem;
            color: #9ca3af;
        }

        .move-white, .move-black {
            width: 4rem;
        }
    </style>
    @endpush

    <div class="max-w-7xl mx-auto px-4 py-6" x-data="chessGame()" x-init="init()">
        <!-- Header de la partie -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('lobby') }}" class="text-gray-400 hover:text-white transition">
                    ← Retour
                </a>
                <div class="h-6 w-px bg-white/20"></div>
                <span class="font-gaming text-lg">{{ $game->theme?->name ?? 'Partie Classique' }}</span>
            </div>
            <div class="flex items-center space-x-2">
                @if($game->isInProgress())
                    <button @click="offerDraw()" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition text-sm">
                        ½ Nulle
                    </button>
                    <form action="{{ route('game.resign', $game->uuid) }}" method="POST" 
                          onsubmit="return confirm('Voulez-vous vraiment abandonner ?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500/20 border border-red-500/30 text-red-400 rounded-lg hover:bg-red-500/30 transition text-sm">
                            Abandonner
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Colonne gauche : Info adversaire + historique -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Adversaire -->
                <div class="card-glass rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-600 to-gray-800 flex items-center justify-center font-bold">
                            @if($game->isAiGame())
                                🤖
                            @else
                                {{ $playerColor === 'white' ? strtoupper(substr($game->blackPlayer?->name ?? '?', 0, 1)) : strtoupper(substr($game->whitePlayer->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">
                                @if($game->isAiGame())
                                    Stockfish (Niv. {{ $game->ai_level }})
                                @else
                                    {{ $playerColor === 'white' ? ($game->blackPlayer?->name ?? 'En attente...') : $game->whitePlayer->name }}
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">
                                Joue les {{ $playerColor === 'white' ? 'Noirs' : 'Blancs' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte sélectionnée -->
                <div class="card-glass rounded-xl p-4" x-show="selectedCard" x-transition>
                    <div class="card-detail rounded-xl p-4" :class="{ 'enemy': selectedCard?.isEnemy }">
                        <div class="text-center mb-3">
                            <div class="text-4xl mb-2" x-text="selectedCard?.icon"></div>
                            <div class="font-gaming text-lg" x-text="selectedCard?.name"></div>
                            <div class="text-xs text-gray-400" x-text="selectedCard?.type"></div>
                        </div>
                        <p class="text-sm text-gray-300 italic mb-4" x-show="selectedCard?.quote">
                            « <span x-text="selectedCard?.quote"></span> »
                        </p>
                        <div class="space-y-2">
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Attaque</span>
                                    <span x-text="selectedCard?.attack"></span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-fill bg-red-500" :style="'width:' + selectedCard?.attack + '%'"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Défense</span>
                                    <span x-text="selectedCard?.defense"></span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-fill bg-blue-500" :style="'width:' + selectedCard?.defense + '%'"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span>Vitesse</span>
                                    <span x-text="selectedCard?.speed"></span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-fill bg-green-500" :style="'width:' + selectedCard?.speed + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des coups -->
                <div class="card-glass rounded-xl p-4">
                    <h3 class="font-gaming text-sm mb-3">Historique</h3>
                    <div class="max-h-48 overflow-y-auto" id="moves-history">
                        <template x-for="(movePair, index) in movesHistory" :key="index">
                            <div class="move-item">
                                <span class="move-number" x-text="(index + 1) + '.'"></span>
                                <span class="move-white text-white" x-text="movePair[0] || ''"></span>
                                <span class="move-black text-gray-400" x-text="movePair[1] || ''"></span>
                            </div>
                        </template>
                        <div x-show="movesHistory.length === 0" class="text-gray-500 text-sm text-center py-2">
                            Aucun coup joué
                        </div>
                    </div>
                </div>
            </div>

            <!-- Échiquier -->
            <div class="lg:col-span-2">
                <div class="chess-board">
                    <template x-for="(row, rowIndex) in displayBoard" :key="rowIndex">
                        <template x-for="(square, colIndex) in row" :key="rowIndex + '-' + colIndex">
                            <div class="chess-square"
                                 :class="[
                                     getSquareColor(rowIndex, colIndex),
                                     { 'selected': isSelected(rowIndex, colIndex) },
                                     { 'legal-move': isLegalMove(rowIndex, colIndex) && !square },
                                     { 'legal-capture': isLegalMove(rowIndex, colIndex) && square },
                                     { 'last-move': isLastMove(rowIndex, colIndex) },
                                     { 'check': isKingInCheck(rowIndex, colIndex) }
                                 ]"
                                 @click="handleSquareClick(rowIndex, colIndex)"
                                 @dragover.prevent
                                 @drop="handleDrop($event, rowIndex, colIndex)">
                                
                                <div x-show="square" 
                                     class="chess-piece"
                                     :class="{ 'black-piece': square?.color === 'b' }"
                                     draggable="true"
                                     @dragstart="handleDragStart($event, rowIndex, colIndex)"
                                     @dragend="handleDragEnd($event)">
                                    <!-- Si la carte a une image -->
                                    <template x-if="getCardImage(square)">
                                        <img :src="getCardImage(square)" :alt="getCardName(square)" class="card-image">
                                    </template>
                                    <!-- Sinon, afficher l'icône unicode -->
                                    <template x-if="!getCardImage(square)">
                                        <span class="piece-icon" x-text="getPieceIcon(square)"></span>
                                    </template>
                                    <span class="piece-name" x-text="getCardName(square)"></span>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>

                <!-- Statut de la partie -->
                <div class="mt-4 text-center">
                    <div x-show="gameStatus === 'checkmate'" class="text-2xl font-gaming text-amber-400">
                        Échec et Mat ! 
                        <span x-text="winner === playerColor ? '🎉 Victoire !' : 'Défaite'"></span>
                    </div>
                    <div x-show="gameStatus === 'stalemate'" class="text-2xl font-gaming text-gray-400">
                        Pat - Match Nul
                    </div>
                    <div x-show="gameStatus === 'draw'" class="text-2xl font-gaming text-gray-400">
                        Match Nul
                    </div>
                    <div x-show="gameStatus === 'playing' && isMyTurn" class="text-lg text-indigo-400">
                        À vous de jouer !
                    </div>
                    <div x-show="gameStatus === 'playing' && !isMyTurn" class="text-lg text-gray-400">
                        <span x-show="isAiGame">L'IA réfléchit...</span>
                        <span x-show="!isAiGame">En attente de l'adversaire...</span>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Info joueur -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Joueur -->
                <div class="card-glass rounded-xl p-4 border border-indigo-500/30">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="font-medium">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-400">
                                Joue les <span class="{{ $playerColor === 'white' ? 'text-gray-200' : 'text-gray-400' }}">{{ $playerColor === 'white' ? 'Blancs' : 'Noirs' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pièces capturées -->
                <div class="card-glass rounded-xl p-4">
                    <h3 class="font-gaming text-sm mb-3">Pièces capturées</h3>
                    <div class="mb-2">
                        <span class="text-xs text-gray-400">Par vous :</span>
                        <div class="flex flex-wrap gap-1 mt-1 min-h-[2rem]">
                            <template x-for="(piece, idx) in capturedByMe" :key="'me-'+idx">
                                <span class="text-xl" x-text="getCapturedIcon(piece)"></span>
                            </template>
                            <span x-show="capturedByMe.length === 0" class="text-gray-600 text-sm">-</span>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-2">
                        <span class="text-xs text-gray-400">Par l'adversaire :</span>
                        <div class="flex flex-wrap gap-1 mt-1 min-h-[2rem]">
                            <template x-for="(piece, idx) in capturedByOpponent" :key="'opp-'+idx">
                                <span class="text-xl" x-text="getCapturedIcon(piece)"></span>
                            </template>
                            <span x-show="capturedByOpponent.length === 0" class="text-gray-600 text-sm">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.3/chess.min.js"></script>
    <script>
        function chessGame() {
            return {
                // Config
                gameUuid: '{{ $game->uuid }}',
                playerColor: '{{ $playerColor ?? "white" }}',
                isAiGame: {{ $game->isAiGame() ? 'true' : 'false' }},
                
                // État du jeu
                chess: null,
                board: [],
                displayBoard: [],
                selectedSquare: null,
                legalMoves: [],
                lastMove: null,
                movesHistory: [],
                gameStatus: 'playing',
                winner: null,
                currentTurn: 'white',
                isMyTurn: false,
                
                // Cartes
                cards: @json($cards ?? []),
                selectedCard: null,
                
                // Pièces capturées
                capturedByMe: [],
                capturedByOpponent: [],

                init() {
                    console.log('Init FanChess - Player:', this.playerColor);
                    
                    this.chess = new Chess('{{ $game->current_fen }}');
                    this.updateBoard();
                    this.loadMovesHistory();
                    this.updateTurn();
                    
                    // Si c'est une partie IA et que l'IA joue en premier (joueur = noir)
                    if (this.isAiGame && this.playerColor === 'black' && this.currentTurn === 'white') {
                        setTimeout(() => this.makeAiMove(), 1000);
                    }
                },

                updateBoard() {
                    // chess.board() retourne [rang8...rang1] donc noirs en haut, blancs en bas
                    this.board = this.chess.board();
                    
                    // Si le joueur joue les noirs, on inverse l'affichage pour avoir ses pièces en bas
                    if (this.playerColor === 'black') {
                        this.displayBoard = this.board.slice().reverse().map(row => row.slice().reverse());
                    } else {
                        this.displayBoard = this.board;
                    }
                },

                displayToReal(displayRow, displayCol) {
                    if (this.playerColor === 'black') {
                        return { row: 7 - displayRow, col: 7 - displayCol };
                    }
                    return { row: displayRow, col: displayCol };
                },

                getSquareColor(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    return (row + col) % 2 === 0 ? 'light' : 'dark';
                },

                updateTurn() {
                    this.currentTurn = this.chess.turn() === 'w' ? 'white' : 'black';
                    this.isMyTurn = this.currentTurn === this.playerColor;
                },

                getPieceIcon(piece) {
                    if (!piece) return '';
                    const icons = {
                        'wp': '♙', 'wn': '♘', 'wb': '♗', 'wr': '♖', 'wq': '♕', 'wk': '♔',
                        'bp': '♟', 'bn': '♞', 'bb': '♝', 'br': '♜', 'bq': '♛', 'bk': '♚'
                    };
                    return icons[piece.color + piece.type] || '?';
                },

                getCapturedIcon(piece) {
                    const icons = {
                        'wp': '♙', 'wn': '♘', 'wb': '♗', 'wr': '♖', 'wq': '♕',
                        'bp': '♟', 'bn': '♞', 'bb': '♝', 'br': '♜', 'bq': '♛'
                    };
                    return icons[piece] || '?';
                },

                getCardName(piece) {
                    if (!piece) return '';
                    const color = piece.color === 'w' ? 'white' : 'black';
                    const typeMap = { p: 'pawn', n: 'knight', b: 'bishop', r: 'rook', q: 'queen', k: 'king' };
                    const pieceType = typeMap[piece.type];
                    
                    if (this.cards && this.cards[color] && this.cards[color][pieceType]) {
                        return this.cards[color][pieceType].name || '';
                    }
                    return '';
                },

                getCardImage(piece) {
                    if (!piece) return null;
                    const color = piece.color === 'w' ? 'white' : 'black';
                    const typeMap = { p: 'pawn', n: 'knight', b: 'bishop', r: 'rook', q: 'queen', k: 'king' };
                    const pieceType = typeMap[piece.type];
                    
                    const card = this.cards?.[color]?.[pieceType];
                    if (card && card.image) {
                        return '/storage/' + card.image;
                    }
                    return null;
                },

                getCard(piece) {
                    if (!piece) return null;
                    const color = piece.color === 'w' ? 'white' : 'black';
                    const typeMap = { p: 'pawn', n: 'knight', b: 'bishop', r: 'rook', q: 'queen', k: 'king' };
                    const pieceType = typeMap[piece.type];
                    
                    const card = this.cards?.[color]?.[pieceType];
                    if (!card) return null;
                    
                    const pieceNames = { pawn: 'Pion', knight: 'Cavalier', bishop: 'Fou', rook: 'Tour', queen: 'Dame', king: 'Roi' };
                    
                    return {
                        name: card.name,
                        type: pieceNames[pieceType] || pieceType,
                        quote: card.quote || '',
                        icon: this.getPieceIcon(piece),
                        attack: card.attack_visual || 50,
                        defense: card.defense_visual || 50,
                        speed: card.speed_visual || 50,
                        isEnemy: color !== this.playerColor
                    };
                },

                indexToSquare(row, col) {
                    const files = 'abcdefgh';
                    return files[col] + (8 - row);
                },

                squareToIndex(square) {
                    const files = 'abcdefgh';
                    return {
                        row: 8 - parseInt(square[1]),
                        col: files.indexOf(square[0])
                    };
                },

                isSelected(displayRow, displayCol) {
                    if (!this.selectedSquare) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    return this.selectedSquare.row === row && this.selectedSquare.col === col;
                },

                isLegalMove(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const square = this.indexToSquare(row, col);
                    return this.legalMoves.some(m => m.to === square);
                },

                isLastMove(displayRow, displayCol) {
                    if (!this.lastMove) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const square = this.indexToSquare(row, col);
                    return this.lastMove.from === square || this.lastMove.to === square;
                },

                isKingInCheck(displayRow, displayCol) {
                    if (!this.chess.in_check()) return false;
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    return piece && piece.type === 'k' && piece.color === this.chess.turn();
                },

                handleSquareClick(displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    const clickedSquare = this.indexToSquare(row, col);

                    // Afficher la carte si on clique sur une pièce
                    if (piece) {
                        this.selectedCard = this.getCard(piece);
                    }

                    // Si on clique sur une case de destination valide
                    if (this.selectedSquare && this.legalMoves.some(m => m.to === clickedSquare)) {
                        this.makeMove(this.selectedSquare, { row, col });
                        return;
                    }

                    // Si on clique sur une de nos pièces (et c'est notre tour)
                    if (piece && this.isMyTurn) {
                        const pieceColor = piece.color === 'w' ? 'white' : 'black';
                        if (pieceColor === this.playerColor) {
                            this.selectedSquare = { row, col };
                            const square = this.indexToSquare(row, col);
                            this.legalMoves = this.chess.moves({ square, verbose: true });
                            return;
                        }
                    }

                    // Désélectionner
                    this.selectedSquare = null;
                    this.legalMoves = [];
                },

                async makeMove(from, to) {
                    const fromSquare = this.indexToSquare(from.row, from.col);
                    const toSquare = this.indexToSquare(to.row, to.col);
                    
                    // Vérifier promotion
                    let promotion = null;
                    const piece = this.chess.get(fromSquare);
                    if (piece && piece.type === 'p') {
                        const targetRow = piece.color === 'w' ? 0 : 7;
                        if (to.row === targetRow) {
                            promotion = prompt('Promotion: q (Dame), r (Tour), b (Fou), n (Cavalier)', 'q');
                            if (!['q', 'r', 'b', 'n'].includes(promotion)) promotion = 'q';
                        }
                    }

                    const move = this.chess.move({
                        from: fromSquare,
                        to: toSquare,
                        promotion: promotion
                    });

                    if (move) {
                        // Pièce capturée
                        if (move.captured) {
                            const capturedPiece = (move.color === 'w' ? 'b' : 'w') + move.captured;
                            if (move.color === (this.playerColor === 'white' ? 'w' : 'b')) {
                                this.capturedByMe.push(capturedPiece);
                            } else {
                                this.capturedByOpponent.push(capturedPiece);
                            }
                        }
                        
                        this.lastMove = { from: fromSquare, to: toSquare };
                        this.updateBoard();
                        this.updateMovesHistory(move);
                        this.updateTurn();
                        this.checkGameEnd();

                        // Envoyer au serveur
                        await this.sendMoveToServer(move);

                        // Si partie contre IA et c'est au tour de l'IA
                        if (this.isAiGame && !this.isMyTurn && this.gameStatus === 'playing') {
                            setTimeout(() => this.makeAiMove(), 800);
                        }
                    }

                    this.selectedSquare = null;
                    this.legalMoves = [];
                },

                async sendMoveToServer(move) {
                    try {
                        await fetch(`/partie/${this.gameUuid}/coup`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                from: move.from,
                                to: move.to,
                                promotion: move.promotion || null,
                                san: move.san,
                                fen: this.chess.fen(),
                                piece: move.piece,
                                captured: move.captured || null,
                                is_check: this.chess.in_check(),
                                is_checkmate: this.chess.in_checkmate(),
                                is_castling: move.flags.includes('k') || move.flags.includes('q'),
                                is_en_passant: move.flags.includes('e')
                            })
                        });
                    } catch (error) {
                        console.error('Erreur envoi coup:', error);
                    }
                },

                makeAiMove() {
                    if (this.gameStatus !== 'playing') return;
                    
                    const moves = this.chess.moves({ verbose: true });
                    if (moves.length > 0) {
                        // IA basique : priorise captures et échecs
                        let bestMoves = moves.filter(m => m.captured || m.san.includes('+'));
                        if (bestMoves.length === 0) bestMoves = moves;
                        
                        const randomMove = bestMoves[Math.floor(Math.random() * bestMoves.length)];
                        const from = this.squareToIndex(randomMove.from);
                        const to = this.squareToIndex(randomMove.to);
                        
                        this.makeMove(from, to);
                    }
                },

                updateMovesHistory(move) {
                    if (move.color === 'w') {
                        this.movesHistory.push([move.san, null]);
                    } else {
                        if (this.movesHistory.length > 0) {
                            this.movesHistory[this.movesHistory.length - 1][1] = move.san;
                        } else {
                            this.movesHistory.push(['...', move.san]);
                        }
                    }
                },

                loadMovesHistory() {
                    const history = this.chess.history();
                    this.movesHistory = [];
                    for (let i = 0; i < history.length; i += 2) {
                        this.movesHistory.push([history[i], history[i + 1] || null]);
                    }
                },

                checkGameEnd() {
                    if (this.chess.in_checkmate()) {
                        this.gameStatus = 'checkmate';
                        this.winner = this.chess.turn() === 'w' ? 'black' : 'white';
                    } else if (this.chess.in_stalemate()) {
                        this.gameStatus = 'stalemate';
                    } else if (this.chess.in_draw() || this.chess.in_threefold_repetition()) {
                        this.gameStatus = 'draw';
                    }
                },

                handleDragStart(event, displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const piece = this.board[row]?.[col];
                    
                    if (piece && this.isMyTurn) {
                        const pieceColor = piece.color === 'w' ? 'white' : 'black';
                        if (pieceColor === this.playerColor) {
                            this.selectedSquare = { row, col };
                            const square = this.indexToSquare(row, col);
                            this.legalMoves = this.chess.moves({ square, verbose: true });
                            event.target.classList.add('dragging');
                        }
                    }
                },

                handleDragEnd(event) {
                    event.target.classList.remove('dragging');
                },

                handleDrop(event, displayRow, displayCol) {
                    const { row, col } = this.displayToReal(displayRow, displayCol);
                    const clickedSquare = this.indexToSquare(row, col);
                    
                    if (this.selectedSquare && this.legalMoves.some(m => m.to === clickedSquare)) {
                        this.makeMove(this.selectedSquare, { row, col });
                    } else {
                        this.selectedSquare = null;
                        this.legalMoves = [];
                    }
                },

                offerDraw() {
                    alert('Fonctionnalité à implémenter avec WebSocket');
                }
            };
        }
    </script>
    @endpush
</x-app-layout>