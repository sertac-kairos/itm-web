<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OnboardingSlide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $slides = OnboardingSlide::query()->active()->ordered()->with('translations')->get();

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $slides->map(function (OnboardingSlide $slide) {
                return [
                    'id' => $slide->id,
                    'title' => $slide->title ?: '',
                    'description' => $slide->description ?: '',
                    'image' => $slide->image ? url('storage/'.$slide->image) : null,
                    'sort_order' => $slide->sort_order,
                ];
            }),
        ]);
    }
}


