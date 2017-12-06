<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class DataTableService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    function getFilter()
    {
        $request = $this->requestStack->getCurrentRequest();

        $length = $request->get('length');
        $length = $length && ($length != -1) ? $length : 0;

        $start = $request->get('start');
        $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

        $column = $search = $request->get('columns');

        $order = $request->get('order')[0];

        $columnOrder = $order['column'];
        $dirOrder = $order['dir'];
        $columNameOrder = $column[$columnOrder]['data'];
        $search = $request->get('search');
        $filters = [
            'query' => @$search['value']
        ];
        return array(
            'filters'        => $filters,
            'start'          => $start,
            'length'         => $length,
            'columNameOrder' => $columNameOrder,
            'dirOrder'       => $dirOrder,

        );
    }


}