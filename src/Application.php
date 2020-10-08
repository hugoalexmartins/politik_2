<?php

namespace App;

use App\Service\Context;
use App\Service\CouncillorService;
use App\Service\FactionService;

class Application
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * Run application
     * @param array $get
     * @return array|false|string
     */
    public function run(array $get)
    {

        if (!isset($get['data'])) {
            $get['data'] = 'councillors';
        }

        switch ($get['data']) {
            case 'factions':
                $this->context->setStrategy(new FactionService($this->context->getHttpClient()));
                break;
            case 'councillors':
            default:
                $this->context->setStrategy(new CouncillorService($this->context->getHttpClient()));
                break;

        }

        if (!empty($get['page'])) {
            $this->context->getStrategy()->setSortBy(intval($get['page']));
        }

        if (!empty($get['sortBy'])) {
            $this->context->getStrategy()->setSortBy($get['sortBy']);
        }

        if ($this->context->getStrategy()) {
            // render output
            return json_encode($this->context->getStrategy()->getList());
        }

        // must implement a strategy
        return [];
    }
}