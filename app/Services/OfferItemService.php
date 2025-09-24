<?php

namespace App\Services;

use Exception;
use App\Models\Offer;
use App\Models\OfferItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\OfferItemRequest;
use App\Libraries\QueryExceptionLibrary;

class OfferItemService
{
    public $itemExtra;
    protected $itemExtraFilter = ['item_id', 'name', 'price', 'status'];

    public function list(PaginateRequest $request, Offer $offer)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return OfferItem::with('offer', 'item')
                ->where(['offer_id' => $offer->id])
                ->where(function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->itemExtraFilter)) {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                })
                ->orderBy($orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Guarda/actualiza por par (offer_id, item_id).
     * - Si ya existe el item en ese offer: suma quantity.
     * - Si no existe: lo crea con esa quantity.
     */
    public function store(OfferItemRequest $request, Offer $offer)
    {
        try {
            $validated = $request->validated();
            $itemId    = (int) $validated['item_id'];
            $qty       = (int) ($validated['quantity'] ?? 1);

            return DB::transaction(function () use ($offer, $itemId, $qty) {
                // Crea si no existe (con quantity=0), luego incrementa de forma atómica.
                $offerItem = OfferItem::firstOrCreate(
                    ['offer_id' => $offer->id, 'item_id' => $itemId],
                    ['quantity' => 0]
                );

                // suma la cantidad (evita “double-insert”)
                $offerItem->increment('quantity', $qty);

                return $offerItem->fresh(['offer', 'item']);
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function destroy(Offer $offer, OfferItem $offerItem)
    {
        try {
            if ($offer->id == $offerItem->offer_id) {
                $offerItem->delete();
            } else {
                throw new Exception(trans('all.item_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}