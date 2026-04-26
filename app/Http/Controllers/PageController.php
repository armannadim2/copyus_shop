<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function privacy()
    {
        return $this->renderLegal('privacy');
    }

    public function terms()
    {
        return $this->renderLegal('terms');
    }

    public function cookies()
    {
        return $this->renderLegal('cookies');
    }

    private function renderLegal(string $type)
    {
        return view('pages.legal', [
            'type'        => $type,
            'pageTitle'   => __("legal.{$type}.page_title"),
            'pageLead'    => __("legal.{$type}.page_lead"),
            'sections'    => __("legal.{$type}.sections"),
            'updatedLabel'=> __('legal.last_updated_label'),
            'updatedDate' => __('legal.last_updated_date'),
        ]);
    }
}
