<?php

namespace App\Http\Controllers;

use App\Models\WidgetModel;
use Illuminate\Http\Request;

class WidgetsController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $size = $request->get('size') ?? 10;

        $widgets = WidgetModel::skip(($page - 1) * $size)
            ->take($size)
            ->get();
        return response()->json($widgets);
    }

    public function view(Request $request)
    {
        $id = $request->route('id');
        $widget = WidgetModel::find($id);

        if ($widget) {
            return response()->json($widget);
        } else {
            return response()->json(['message' => 'Widget not found.'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2',
            'color' => 'required|string|min:2',
            'size' => 'required|string|in:small,medium,large',
            'count' => 'required|integer',
        ]);

        $payload = [
            'name' => $request->post('name'),
            'color' => $request->post('color'),
            'size' => $request->post('size'),
            'count' => $request->post('count'),
            'active' => $request->post('active', false),
        ];

        $widget = WidgetModel::create($payload);
        return response()->json($widget);
    }

    public function update(Request $request) {
        $request->validate([
            'name' => 'required|string|min:2',
            'color' => 'required|string|in:red,green,blue,white,black',
            'size' => 'required|string|in:small,medium,large',
            'count' => 'required|integer',
        ]);

        $payload = [
            'name' => $request->post('name'),
            'color' => $request->post('color'),
            'size' => $request->post('size'),
            'count' => $request->post('count'),
            'active' => $request->post('active', false),
        ];

        $id = $request->route('id');
        $widget = WidgetModel::find($id);
        if ($widget) {
            $widget->update($payload);
            $widget->refresh();
            return response()->json($widget);
        } else {
            return response()->json(['message' => 'Widget not found.'], 404);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->route('id');
        $widget = WidgetModel::find($id);

        if ($widget) {
            $widget->delete();
            return response()->json(['message' => 'Widget deleted.'], 200);
        } else {
            return response()->json(['message' => 'Widget not found.'], 404);
        }
    }
}
