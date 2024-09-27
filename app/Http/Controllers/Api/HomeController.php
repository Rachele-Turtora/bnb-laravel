<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Home;
use App\Models\Message;
use App\Mail\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function index()
    {

        // $homes = Home::all();

        //? paginazione a 6 elementi con relazione services:
        // $homes = Home::with('ads','services', 'user')->paginate(12);

        // if ($homes) {
        //     return response()->json([
        //         'status' => 'success',
        //         'results' => $homes
        //     ]);
        // } else {
        //     return response()->json([
        //         'status' => 'failed',
        //         'results' => null
        //     ], 404);
        // }

       // Recuperiamo tutte le case con la relazione ads
       $homes = Home::with('ads', 'services', 'user')->get();

       $now = Carbon::now();

       // Filtriamo le case sponsorizzate attive
       $sponsoredHomes = $homes->filter(function ($home) use ($now) {
           foreach ($home->ads as $ad) {
               $startDate = $ad->pivot->created_at; // Data di inizio sponsorizzazione
               $endDate = Carbon::parse($startDate)->addHours((int) explode(':', $ad->duration)[0]); // Calcola la durata
               if ($now->lessThan($endDate)) {
                   return true; // Sponsorizzazione ancora attiva
               }
           }
           return false; // Sponsorizzazione scaduta
       });

       // Filtriamo le case non sponsorizzate
       $nonSponsoredHomes = $homes->filter(function ($home) use ($now) {
           foreach ($home->ads as $ad) {
               $startDate = $ad->pivot->created_at;
               $endDate = Carbon::parse($startDate)->addHours((int) explode(':', $ad->duration)[0]);
               if ($now->greaterThanOrEqualTo($endDate)) {
                   return true; // Sponsorizzazione scaduta o non presente
               }
           }
           return $home->ads->isEmpty(); // Case senza sponsorizzazioni
       });

       // Uniamo case sponsorizzate e non sponsorizzate
       $allHomes = $sponsoredHomes->merge($nonSponsoredHomes);

       // Paginiamo i risultati
       $perPage = 12; // Numero di case per pagina
       $currentPage = request()->get('page', 1); // Recupera la pagina corrente
       $paginatedHomes = $allHomes->slice(($currentPage - 1) * $perPage, $perPage)->values();

       return response()->json([
           'status' => 'success',
           'results' => [
               'data' => $paginatedHomes,
               'total' => $allHomes->count(),
               'current_page' => $currentPage,
               'per_page' => $perPage,
               'ads_active' => $sponsoredHomes,
           ]
       ]);
    }

    public function show(String $slug)
    {
        //? dettaglio con relazione services:
        $homes = Home::where('slug', $slug)->with('services', 'user', 'messages')->first();

        if ($homes) {
            return response()->json([
                'status' => 'success',
                'results' => $homes
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'results' => null
            ], 404);
        }
    }

    public function storeMessage(StoreMessageRequest $request, $slug)
    {
       
        $data = $request->validated();

        $apartment = Home::where('slug', $slug)->first();

        if (!$apartment) {
            return response()->json(['error' => 'Apartamento no encontrado.'], 404);
        }

        $message = new Message();
        $message->name = $data['name'];
        $message->email = $data['email'];
        $message->content = $data['content'];
        $message->home_id = $apartment->id;
        $message->save();

        return response()->json(['message' => 'Mensaje enviado con éxito.'], 201);
    }
}
