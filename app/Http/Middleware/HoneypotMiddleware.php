<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HoneypotMiddleware
{
    /**
     * Le nom du champ honeypot
     */
    protected string $honeypotField = 'website_url';

    /**
     * Le nom du champ timestamp
     */
    protected string $timestampField = 'form_timestamp';

    /**
     * Temps minimum en secondes pour remplir le formulaire
     */
    protected int $minSubmitTime = 3;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le champ honeypot est rempli (seulement les bots le remplissent)
        if ($request->filled($this->honeypotField)) {
            // Log pour analyse (optionnel)
            \Log::warning('Honeypot triggered', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'honeypot_value' => $request->input($this->honeypotField),
            ]);

            // Simuler un succès pour tromper le bot
            return $this->fakeSuccessResponse($request);
        }

        // Vérifier le temps de soumission (trop rapide = bot)
        if ($request->has($this->timestampField)) {
            $timestamp = (int) $request->input($this->timestampField);
            $timeTaken = time() - $timestamp;

            if ($timeTaken < $this->minSubmitTime) {
                \Log::warning('Form submitted too quickly', [
                    'ip' => $request->ip(),
                    'time_taken' => $timeTaken,
                ]);

                return $this->fakeSuccessResponse($request);
            }
        }

        return $next($request);
    }

    /**
     * Retourne une fausse réponse de succès pour tromper les bots
     */
    protected function fakeSuccessResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true], 200);
        }

        // Rediriger vers la page d'accueil avec un message générique
        return redirect('/')->with('status', 'Opération effectuée.');
    }
}
