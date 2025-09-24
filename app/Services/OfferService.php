<?php

namespace App\Services;


use Exception;
use Carbon\Carbon;
use App\Enums\Ask;
use App\Enums\Status;
use App\Enums\OfferType;
use App\Enums\ItemType;
use App\Models\Item;
use App\Models\Offer;
use App\Models\ItemCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OfferRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;

class OfferService
{
    public $offer;
    protected $offerFilter = [
        'name',
        'amount',
        'status',
        'start_date',
        'end_date',
    ];

    protected $exceptFilter = [
        'excepts'
    ];

    private static function normalizePercent($value): ?float
    {
        if ($value === null || $value === '') return null;
        // quita % y espacios
        $v = str_ireplace('%', '', (string)$value);
        // quita separadores de miles
        $v = str_replace([',', ' '], ['', ''], $v);
        $num = (float)$v;
        if ($num < 0)   $num = 0;
        if ($num > 100) $num = 100;
        return $num;
    }

    private static function normalizeMoney($value): ?float
    {
        if ($value === null || $value === '') return null;
        $v = (string)$value;
        // Quita símbolos comunes (Q, $, etc.) y espacios
        $v = preg_replace('/[^\d\.\,\-]/', '', $v) ?: '0';
        // Si tu local usa coma decimal, conviértela a punto
        // Heurística: si hay tanto punto como coma, asumimos coma como miles -> quitamos comas
        if (strpos($v, ',') !== false && strpos($v, '.') !== false) {
            $v = str_replace(',', '', $v);
        } else {
            // si solo hay coma, úsala como decimal
            if (strpos($v, ',') !== false && strpos($v, '.') === false) {
                $v = str_replace(',', '.', $v);
            }
        }
        // quita posibles separadores de miles restantes
        $v = str_replace(',', '', $v);
        return (float)$v;
    }

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $limit       = $request->get('limit') ? $request->get('limit') : '';

            return Offer::with('items')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->offerFilter)) {
                        if ($key == "start_date") {
                            $start_date  = Date('Y-m-d', strtotime($request));
                            $query->whereDate($key, '>=', $start_date);
                        } else if ($key == "end_date") {
                            $end_date  = Date('Y-m-d', strtotime($request));
                            $query->whereDate($key, '<=', $end_date);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->limit($limit)->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function activeWise(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $limit       = $request->get('limit') ? $request->get('limit') : '';

            return Offer::with('items')->where('end_date', '>=', now()->toDateTimeString())->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->offerFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->limit($limit)->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(OfferRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $type = (int)$request->input('type');

                // Construye payload base
                $payload = [
                    'name'       => $request->name,
                    'slug'       => Str::slug($request->name),
                    'type'       => $type,
                    'start_date' => date('Y-m-d H:i:s', strtotime($request->start_date)),
                    'end_date'   => date('Y-m-d H:i:s', strtotime($request->end_date)),
                    'status'     => $request->status,
                ];

                if ($type === OfferType::DISCOUNT) {
                    // Porcentaje 0..100 en amount; combo_price null
                    $payload['amount']      = self::normalizePercent($request->input('amount'));
                    $payload['combo_price'] = null;
                } else { // OfferType::COMBO
                    // Guarda combo_price como dinero; amount null
                    // Si reutilizas "amount" como precio en el form, lo normalizamos igualmente:
                    $comboPrice = $request->filled('combo_price')
                        ? $request->input('combo_price')
                        : $request->input('amount');

                    $payload['combo_price'] = self::normalizeMoney($comboPrice);
                    $payload['amount']      = null;
                }

                $offer = Offer::create($payload);

                if ($request->image) {
                    $offer->clearMediaCollection('offer');
                    $offer->addMedia($request->image)->toMediaCollection('offer');
                }
                $this->syncComboItem($offer);
                $this->offer = $offer->fresh(['comboItem']);
            });

            return $this->offer;

        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function update(OfferRequest $request, Offer $offer)
    {
        try {
            DB::transaction(function () use ($request, $offer) {
                $type = (int)$request->input('type');

                $offer->name       = $request->name;
                $offer->slug       = Str::slug($request->name);
                $offer->type       = $type;
                $offer->start_date = date('Y-m-d H:i:s', strtotime($request->start_date));
                $offer->end_date   = date('Y-m-d H:i:s', strtotime($request->end_date));
                $offer->status     = $request->status;

                if ($type === OfferType::DISCOUNT) {
                    $offer->amount      = self::normalizePercent($request->input('amount'));
                    $offer->combo_price = null;
                } else { // COMBO
                    $comboPrice = $request->filled('combo_price')
                        ? $request->input('combo_price')
                        : $request->input('amount'); // por si el front reusa "amount" como precio

                    $offer->combo_price = self::normalizeMoney($comboPrice);
                    $offer->amount      = null;
                }

                $offer->save();
                
                if ($request->image) {
                    $offer->clearMediaCollection('offer');
                    $offer->addMedia($request->image)->toMediaCollection('offer');
                }

                $this->syncComboItem($offer);
                $this->offer = $offer->fresh(['comboItem']);
            });

            return $this->offer;

        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function destroy(Offer $offer)
    {
        try {
            if ($offer->comboItem) {
                $offer->comboItem->delete();
            }
            $offer->offerItems()->delete();
            $offer->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Offer $offer): Offer
    {
        try {
            return $offer->load('items');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeImage(ChangeImageRequest $request, Offer $offer): Offer
    {
        try {
            if ($request->image) {
                $offer->clearMediaCollection('offer');
                $offer->addMedia($request->image)->toMediaCollection('offer');
            }
            return $offer;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function offerItemByDate(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Offer::with('offerItems')->where(['status' => Status::ACTIVE])->get()->filter(function ($offer) {
                if (Carbon::now()->between($offer->start_date, $offer->end_date)) {
                    return $offer;
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
    protected function syncComboItem(Offer $offer): void
    {
        if ((int)$offer->type !== OfferType::COMBO) {
            if ($offer->comboItem) {
                $offer->comboItem->delete();
            }

            if ($offer->combo_item_id !== null) {
                $offer->combo_item_id = null;
                $offer->save();
            }

            return;
        }

        $category = $this->comboCategory();
        $existingId = $offer->comboItem?->id;

        $item = $offer->comboItem ?? new Item();

        $item->fill([
            'item_category_id' => $category->id,
            'name'             => $offer->name,
            'slug'             => $this->generateComboSlug($offer->name, $existingId),
            'tax_id'           => null,
            'item_type'        => $item->item_type ?? ItemType::VEG,
            'price'            => $offer->combo_price ?? 0,
            'is_featured'      => Ask::NO,
            'description'      => self::buildComboDescription($offer),
            'caution'          => null,
            'status'           => $offer->status,
            'order'            => $item->order ?? 1,
        ]);

        $item->save();

        if ($offer->combo_item_id !== $item->id) {
            $offer->combo_item_id = $item->id;
            $offer->save();
        }

        $offerWithMedia = $offer->fresh('media');
        $this->syncComboMedia($offerWithMedia, $item);

        self::updateComboItemDescription($offer->fresh(['offerItems.item', 'comboItem']));
    }

    private function comboCategory(): ItemCategory
    {
        return ItemCategory::firstOrCreate(
            ['slug' => 'combo-items'],
            ['name' => 'Combos', 'status' => Status::ACTIVE]
        );
    }

    private function generateComboSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim($name) === '' ? 'combo-offer' : $name . ' combo');
        if ($base === '') {
            $base = 'combo-offer';
        }

        $slug = $base;
        $counter = 1;

        while (
            Item::where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function syncComboMedia(Offer $offer, Item $item): void
    {
        $media = $offer->getFirstMedia('offer');
        if (!$media) {
            return;
        }

        $path = $media->getPath();
        if (!$path || !is_file($path)) {
            return;
        }

        $item->clearMediaCollection('item');
        $item->addMedia($path)->preservingOriginal()->toMediaCollection('item');
    }

    private static function buildComboDescription(Offer $offer): ?string
    {
        $offer->loadMissing('offerItems.item');

        $parts = $offer->offerItems
            ->map(function ($offerItem) {
                if (!$offerItem->item) {
                    return null;
                }

                $quantity = max(1, (int) $offerItem->quantity);
                $name = $offerItem->item->name;

                return $quantity > 1 ? $quantity . ' × ' . $name : $name;
            })
            ->filter()
            ->values();

        if ($parts->isEmpty()) {
            return null;
        }

        return $parts->implode(', ');
    }

    public static function updateComboItemDescription(Offer $offer): void
    {
        if (!$offer->combo_item_id) {
            return;
        }

        $offer->loadMissing('comboItem');

        if (!$offer->comboItem) {
            return;
        }

        $description = self::buildComboDescription($offer);
        $offer->comboItem->description = $description;
        $offer->comboItem->save();
    }
}