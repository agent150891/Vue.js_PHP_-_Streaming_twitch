<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Validator;

use App\Models\{Viewer, Item, ViewerItem, Card, ViewerPrize, StockPrize};

class ViewerItemsController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['lastPrizes']]);
        header("Access-Control-Allow-Origin: " . getOrigin($_SERVER));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $viewer = $user->viewer()->first();
        $viewerItems = $viewer->items()->get();
        $items = [];
        $cards = Card::where('viewer_id', $viewer->id)->get();
        $cardItemsIds = [];
        foreach ($cards as $card) {
            $cardItemsIds[] = $card->frame_id;
            $cardItemsIds[] = $card->hero_id;
        }
        foreach ($viewerItems as $viewerItem) {
            if (!in_array($viewerItem->id, $cardItemsIds)) {
                $item = $viewerItem->item()->first();
                $item->type = $item->type()->first()->name;
                $item_id = $item->id;
                $item->id = $viewerItem->id;
                $item->item_id = $item_id;
                $items[] = $item;
            }
        }
        return response()->json(['data' => [
            'items' => $items,
        ]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'id'   =>  'required|numeric|min:1',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
            ]);
        }
        if (!Item::find($request->id)) {
            return response()->json([
                'errors' => ['item id not found'],
            ]);
        }
        $user = auth()->user();
        $viewer = $user->viewer()->first();
        $item = new ViewerItem();
        $item->viewer_id = $viewer->id;
        $item->item_id = $request->id;
        $item->save();
        
        return response()->json([
            'message' => 'item added to viewer',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'id'   =>  'required|numeric|min:1',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
            ]);
        }
        $user = auth()->user();
        $viewer = $user->viewer()->first();
        $viewerItem = ViewerItem::where([
            ['viewer_id', '=', $viewer->id],
            ['item_id', '=', $request->id],
        ])->first();
        if (!$viewerItem) {
            return response()->json([
                'errors' => ['item id not found '],
            ]);
        }
        $item = $viewerItem->item()->first();

        return response()->json([
            'data' => $item,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'       => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
            ]);
        }
        $user = auth()->user();
        $viewer = $user->viewer()->first();
        $viewerItem = ViewerItem::where([
            ['viewer_id', '=', $viewer->id],
            ['item_id', '=', $request->id],
        ])->first();
        if (!$viewerItem) {
            return response()->json([
                'errors' => ['item id not found'],
            ]);
        }
        $viewerItem->delete();
        return response()->json([
            'message' => 'item delete successful',
        ]);
    }

    public function lastPrizes(Request $request)
    {
        $count = 4;
        $viewerPrizes = ViewerPrize::orderBy('created_at', 'desc')->limit($count)->get();
        $prizes = [];
        for ($i = 0; $i < count($viewerPrizes); $i++) {
            $prize = [];
            $stockPrize = StockPrize::find($viewerPrizes[$i]->prize_id);
            $prize['id']  = $i;
            $prize['name'] = $stockPrize->name;
            $prize['description'] = $stockPrize->description;
            $prize['image'] = $stockPrize->image;
            $prize['viewer'] = $viewerPrizes[$i]->viewer_id;
            $prize['cost']  = $stockPrize->cost;
            $viewer = Viewer::find($prize['viewer']);
            $prize['viewer'] = $viewer->name;
            $prizes[] = $prize;
        }
        return response()->json([
            'data' => [
                'prizes'    =>  $prizes,
            ],
        ]);
    }
}
