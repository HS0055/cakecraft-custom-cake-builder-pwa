<?php

namespace App\Livewire\Traits;

use App\Models\{CakeShape, CakeColor, ReadyCake, ShapeFlavor, ShapeTopping};

trait WithVisualData
{
    public function getVisualDataForItem(array $item): ?array
    {
        $toppingIds = [];

        if (isset($item['type']) && $item['type'] === 'custom' && !empty($item['details'])) {
            $shapeId = $item['details']['shape_id'] ?? null;
            $colorId = $item['details']['color_id'] ?? null;
            $flavorId = $item['details']['flavor_id'] ?? null;
            if (!empty($item['details']['topping_id'])) {
                $toppingIds[] = $item['details']['topping_id'];
            }
        } elseif (isset($item['type']) && $item['type'] === 'ready' && !empty($item['ready_cake_id'])) {
            $cake = ReadyCake::with('cakeTopping')->find($item['ready_cake_id']);
            if ($cake) {
                $shapeId = $cake->cake_shape_id;
                $colorId = $cake->cake_color_id;
                $flavorId = $cake->cake_flavor_id;
                $toppingIds = $cake->cake_topping_id ? [$cake->cake_topping_id] : [];
            }
        }

        if (!$shapeId)
            return null;

        $shape = CakeShape::with('media')->find($shapeId);
        if ($colorId) {
            $color = CakeColor::find($colorId);
        } elseif (!empty($item['details']['color_hex'])) {
            $color = (object) [
                'name' => $item['details']['color'] ?? __('front.cake_builder.custom_color'),
                'hex_code' => $item['details']['color_hex'],
                'id' => null
            ];
        } else {
            $color = null;
        }

        $flavorLayer = ($shapeId && $flavorId)
            ? ShapeFlavor::with('media')->where('cake_shape_id', $shapeId)->where('cake_flavor_id', $flavorId)->first()
            : null;

        $toppingLayers = ($shapeId && !empty($toppingIds))
            ? ShapeTopping::with('media')->where('cake_shape_id', $shapeId)->whereIn('cake_topping_id', $toppingIds)->get()
            : collect();

        return [
            'shape' => $shape,
            'color' => $color,
            'flavorLayer' => $flavorLayer,
            'toppingLayers' => $toppingLayers,
        ];
    }
}
